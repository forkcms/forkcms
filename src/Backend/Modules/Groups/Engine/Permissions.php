<?php

namespace Backend\Modules\Groups\Engine;

use Symfony\Component\Finder\Finder;
use Common\Classes;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Modules\Groups\Engine\Model as BackendGroupsModel;

/**
 * In this file we store all generic functions that we will be using in the groups module.
 *
 * @author Wouter Sioen <wouter@wijs.be>
 */
class Permissions extends BackendBaseActionEdit
{
    /**
     * @var array
     */
    protected $actionGroups = array();
    protected $actions = array();
    protected $modules = array();
    protected $widgets = array();
    protected $widgetInstances = array();
    protected $sites = array();

    /**
     * @param int[optional] $groupId
     *
     * Adds language/site checkboxes to our form
     */
    protected function addLanguageFields($groupId = null)
    {
        $selectedItems = array();
        if ($groupId !== null) {
            $selectedItems = BackendGroupsModel::getAllowedLanguages($groupId);
        }

        // get multisite instance from the DIC
        $multisite = $this->get('multisite');

        // build a tree with all sites containing their languages
        $this->sites = $multisite->getSites();
        foreach ($this->sites as $siteId => $domain) {
            $this->sites[$siteId] = array(
                'siteId' => $siteId,
                'domain' => $domain,
                'checkbox' => $this->frm
                    ->addCheckbox(
                        'site-' . $siteId,
                        null,
                        'inputCheckbox checkBeforeUnload selectAll'
                    )
                    ->parse(),
                'languages' => $multisite->getLanguageList($siteId),
            );

            foreach ($this->sites[$siteId]['languages'] as $key => $language) {
                $checkboxName = $siteId . '-' . $language;
                $this->sites[$siteId]['languages'][$key] = array(
                    'language' => $language,
                    'checkbox' => $this->frm
                        ->addCheckbox(
                            $siteId . '-' . $language,
                            in_array($checkboxName, $selectedItems)
                        )
                        ->parse(),
                );
            }
        }
    }

    /**
     * Bundle all actions that need to be bundled
     */
    protected function bundleActions()
    {
        foreach ($this->modules as $module) {
            // loop through actions and add all classnames
            foreach ($this->actions[$module['value']] as $key => $action) {
                $class = Classes::buildForAjax('Backend', $module['value'], $action['value']);
                if (!class_exists($class)) {
                    $class = Classes::buildForAction('Backend', $module['value'], $action['value']);
                }

                $reflection = new \ReflectionClass($class);

                // get the tag offset
                $offset = strpos($reflection->getDocComment(), ACTION_GROUP_TAG) + strlen(ACTION_GROUP_TAG);

                // no tag present? move on!
                if(!($offset - strlen(ACTION_GROUP_TAG))) continue;

                // get the group info
                $groupInfo = trim(substr($reflection->getDocComment(), $offset, (strpos($reflection->getDocComment(), '*', $offset) - $offset)));

                // get name and description
                $bits = explode("\t", $groupInfo);

                // delete empty values
                foreach($bits as $i => $bit) if(empty($bit)) unset($bits[$i]);

                // add group to actions
                $this->actions[$module['value']][$key]['group'] = $bits[0];

                // add group to array
                $this->actionGroups[$bits[0]] = end($bits);
            }
        }
    }

    /**
     * Get the widgets
     */
    protected function getWidgets()
    {
        $finder = new Finder();
        $finder->name('*.php')
            ->in(BACKEND_MODULES_PATH . '/*/Widgets');
        foreach ($finder->files() as $file) {
            $module = $file->getPathInfo()->getPathInfo()->getBasename();
            if (BackendAuthentication::isAllowedModule($module)) {
                $widgetName = $file->getBasename('.php');
                $className = Classes::buildForWidget('Backend', $module, $widgetName);

                if (class_exists($className)) {
                    // add to array
                    $this->widgetInstances[] = array(
                        'module' => $module,
                        'widget' => $widgetName,
                        'className' => $className
                    );

                    // create reflection class
                    $reflection = new \ReflectionClass($className);
                    $phpDoc = trim($reflection->getDocComment());
                    if ($phpDoc != '') {
                        $offset = strpos($reflection->getDocComment(), '*', 7);
                        $description = substr($reflection->getDocComment(), 0, $offset);
                        $description = str_replace('*', '', $description);
                        $description = trim(str_replace('/', '', $description));
                    } else {
                        $description = '';
                    }

                    // check if model file exists
                    $pathName = $file->getPathInfo()->getPathInfo()->getRealPath();

                    // add to array
                    $this->widgets[] = array(
                        'label' => \SpoonFilter::toCamelCase($widgetName),
                        'value' => $widgetName,
                        'description' => $description
                    );
                }
            }
        }
    }

    /**
     * Get the actions
     */
    protected function getActions()
    {
        $filter = array('Authentication', 'Error', 'Core');
        $modules = array();

        $finder = new Finder();
        $finder->name('*.php')
            ->in(BACKEND_MODULES_PATH . '/*/Actions')
            ->in(BACKEND_MODULES_PATH . '/*/Ajax');
        foreach ($finder->files() as $file) {
            $module = $file->getPathInfo()->getPathInfo()->getBasename();

            // skip some modules
            if (in_array($module, $filter)) {
                continue;
            }

            if (BackendAuthentication::isAllowedModule($module)) {
                $actionName = $file->getBasename('.php');
                $isAjax = $file->getPathInfo()->getBasename() == 'Ajax';
                $modules[] = $module;

                // ajax-files should be required
                if ($isAjax) {
                    $className = Classes::buildForAjax('Backend', $module, $actionName);
                } else {
                    $className = Classes::buildForAction('Backend', $module, $actionName);
                }

                $reflection = new \ReflectionClass($className);
                $phpDoc = trim($reflection->getDocComment());
                if ($phpDoc != '') {
                    $offset = strpos($reflection->getDocComment(), '*', 7);
                    $description = substr($reflection->getDocComment(), 0, $offset);
                    $description = str_replace('*', '', $description);
                    $description = trim(str_replace('/', '', $description));
                } else {
                    $description = '';
                }

                $this->actions[$module][] = array(
                    'label' => \SpoonFilter::toCamelCase($actionName),
                    'value' => $actionName,
                    'description' => $description
                );
            }
        }

        $modules = array_unique($modules);
        foreach ($modules as $module) {
            $this->modules[] = array(
                'label' => \SpoonFilter::toCamelCase($module),
                'value' => $module
            );
        }
    }

    /**
     * Update the permissions
     *
     * @param array $actionPermissions The action permissions.
     * @param array $bundledActionPermissions The bundled action permissions.
     */
    protected function updatePermissions($actionPermissions, $bundledActionPermissions)
    {
        $modulesDenied = array();
        $modulesGranted = array();
        $actionsDenied = array();
        $actionsGranted = array();
        $checkedModules = array();
        $uncheckedModules = array();

        // loop through action permissions
        foreach ($actionPermissions as $permission) {
            // get bits
            $bits = explode('_', $permission->getName());

            // convert camelcasing to underscore notation
            $module = $bits[1];
            $action = $bits[2];

            // permission checked?
            if ($permission->getChecked()) {
                // add to granted
                $actionsGranted[] = array('group_id' => $this->id, 'module' => $module, 'action' => $action, 'level' => ACTION_RIGHTS_LEVEL);

                // if not yet present, add to checked modules
                if (!in_array($module, $checkedModules)) $checkedModules[] = $module;
            }

            // permission not checked?
            else {
                // add to denied
                $actionsDenied[] = array('group_id' => $this->id, 'module' => $module, 'action' => $action, 'level' => ACTION_RIGHTS_LEVEL);

                // if not yet present add to unchecked modules
                if (!in_array($module, $uncheckedModules)) $uncheckedModules[] = $module;
            }
        }

        // loop through bundled action permissions
        foreach ($bundledActionPermissions as $permission) {
            // get bits
            $bits = explode('_', $permission->getName());

            // convert camelcasing to underscore notation
            $module = $bits[1];
            $group = $bits[3];

            // create new item
            $moduleItem = array('group_id' => $this->id, 'module' => $module);

            // loop through actions
            foreach ($this->actions[$module] as $moduleAction) {
                // permission checked?
                if ($permission->getChecked()) {
                    // add to granted if in the right group
                    if (in_array($group, $moduleAction)) $actionsGranted[] = array('group_id' => $this->id, 'module' => $module, 'action' => $moduleAction['value'], 'level' => ACTION_RIGHTS_LEVEL);

                    // if not yet present, add to checked modules
                    if (!in_array($module, $checkedModules)) $checkedModules[] = $module;
                }

                // permission not checked?
                else {
                    // add to denied
                    if (in_array($group, $moduleAction)) $actionsDenied[] = array('group_id' => $this->id, 'module' => $module, 'action' => $moduleAction['value'], 'level' => ACTION_RIGHTS_LEVEL);

                    // if not yet present add to unchecked modules
                    if (!in_array($module, $uncheckedModules)) $uncheckedModules[] = $module;
                }
            }
        }

        // loop through granted modules and add to array
        foreach ($checkedModules as $module) $modulesGranted[] = array('group_id' => $this->id, 'module' => $module);

        // loop through denied modules and add to array
        foreach (array_diff($uncheckedModules, $checkedModules) as $module) $modulesDenied[] = array('group_id' => $this->id, 'module' => $module);

        // add granted permissions
        BackendGroupsModel::addModulePermissions($modulesGranted);
        BackendGroupsModel::addActionPermissions($actionsGranted);

        // delete denied permissions
        BackendGroupsModel::deleteModulePermissions($modulesDenied);
        BackendGroupsModel::deleteActionPermissions($actionsDenied);
    }

    /**
     * Updates the language permissions
     */
    protected function updateLanguagePermissions()
    {
        // build a list with granted site language combinations
        $languagesGranted = array();
        foreach ($this->sites as $site) {
            foreach ($site['languages'] as $language) {
                $fieldName = $site['siteId'] . '-' . $language['language'];
                if ($this->frm->getField($fieldName)->isChecked()) {
                    $languagesGranted[] = array(
                        'group_id' => $this->id,
                        'site_id' => $site['siteId'],
                        'language' => $language['language'],
                    );
                }
            }
        }

        // remove all language/site rights for this group
        BackendGroupsModel::deleteLanguagePermissions($this->id);

        // add the needed permissions
        BackendGroupsModel::addLanguagePermissions($languagesGranted);
    }
}
