<?php

namespace Backend\Modules\Groups\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Finder\Finder;
use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\DataGridArray as BackendDataGridArray;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Groups\Engine\Model as BackendGroupsModel;

/**
 * This is the add-action, it will display a form to create a new group
 */
class Add extends BackendBaseActionAdd
{
    /**
     * The action groups
     *
     * @var array
     */
    private $actionGroups = array();

    /**
     * The actions
     *
     * @var array
     */
    private $actions = array();

    /**
     * The id of the new group
     *
     * @var int
     */
    private $id;

    /**
     * The modules
     *
     * @var array
     */
    private $modules;

    /**
     * The widgets
     *
     * @var array
     */
    private $widgets;

    /**
     * The widget instances
     *
     * @var array
     */
    private $widgetInstances;

    /**
     * Hidden widgets on dashboard
     *
     * @var array
     */
    private $hiddenOnDashboard;

    /**
     * Bundle all actions that need to be bundled
     */
    private function bundleActions()
    {
        foreach ($this->modules as $module) {
            // loop through actions and add all classnames
            foreach ($this->actions[$module['value']] as $key => $action) {
                // ajax action?
                if (class_exists('Backend\\Modules\\' . $module['value'] . '\\Ajax\\' . $action['value'])) {
                    // create reflection class
                    $reflection = new \ReflectionClass('Backend\\Modules\\' . $module['value'] . '\\Ajax\\' . $action['value']);
                } else {
                    // no ajax action? create reflection class
                    $reflection = new \ReflectionClass('Backend\\Modules\\' . $module['value'] . '\\Actions\\' . $action['value']);
                }

                // get the tag offset
                $offset = mb_strpos($reflection->getDocComment(), ACTION_GROUP_TAG) + mb_strlen(ACTION_GROUP_TAG);

                // no tag present? move on!
                if (!($offset - mb_strlen(ACTION_GROUP_TAG))) {
                    continue;
                }

                // get the group info
                $groupInfo = trim(mb_substr($reflection->getDocComment(), $offset, (mb_strpos($reflection->getDocComment(), '*', $offset) - $offset)));

                // get name and description
                $bits = explode("\t", $groupInfo);

                // delete empty values
                foreach ($bits as $i => $bit) {
                    if (empty($bit)) {
                        unset($bits[$i]);
                    }
                }

                // add group to actions
                $this->actions[$module['value']][$key]['group'] = $bits[0];

                // add group to array
                $this->actionGroups[$bits[0]] = end($bits);
            }
        }
    }

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->getData();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    /**
     * Get the actions
     */
    private function getActions()
    {
        $this->actions = array();
        $filter = array('Authentication', 'Error', 'Core');
        $modules = array();

        $finder = new Finder();
        $finder->name('*.php')
            ->in(BACKEND_MODULES_PATH . '/*/Actions')
            ->in(BACKEND_MODULES_PATH . '/*/Ajax');
        foreach ($finder->files() as $file) {
            /** @var $file \SplFileInfo */
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
                    $class = 'Backend\\Modules\\' . $module . '\\Ajax\\' . $actionName;
                } else {
                    $class = 'Backend\\Modules\\' . $module . '\\Actions\\' . $actionName;
                }

                $reflection = new \ReflectionClass($class);
                $phpDoc = trim($reflection->getDocComment());
                if ($phpDoc != '') {
                    $offset = mb_strpos($reflection->getDocComment(), '*', 7);
                    $description = mb_substr($reflection->getDocComment(), 0, $offset);
                    $description = str_replace('*', '', $description);
                    $description = trim(str_replace('/', '', $description));
                } else {
                    $description = '';
                }

                $this->actions[$module][] = array(
                    'label' => \SpoonFilter::toCamelCase($actionName),
                    'value' => $actionName,
                    'description' => $description,
                );
            }
        }

        $modules = array_unique($modules);
        foreach ($modules as $module) {
            $this->modules[] = array(
                'label' => \SpoonFilter::toCamelCase($module),
                'value' => $module,
            );
        }
    }

    /**
     * Get the data
     */
    private function getData()
    {
        $this->getWidgets();
        $this->getActions();
        $this->bundleActions();
    }

    /**
     * Get the widgets
     */
    private function getWidgets()
    {
        $this->widgets = array();
        $this->widgetInstances = array();

        $finder = new Finder();
        $finder->name('*.php')->in(BACKEND_MODULES_PATH . '/*/Widgets');
        foreach ($finder->files() as $file) {
            /** @var $file \SplFileInfo */
            $module = $file->getPathInfo()->getPathInfo()->getBasename();
            if (BackendAuthentication::isAllowedModule($module)) {
                $widgetName = $file->getBasename('.php');
                $class = 'Backend\\Modules\\' . $module . '\\Widgets\\' . $widgetName;

                if (class_exists($class)) {
                    // add to array
                    $this->widgetInstances[] = array(
                        'module' => $module,
                        'widget' => $widgetName,
                        'className' => $class,
                    );

                    // create reflection class
                    $reflection = new \ReflectionClass($class);
                    $phpDoc = trim($reflection->getDocComment());
                    if ($phpDoc != '') {
                        $offset = mb_strpos($reflection->getDocComment(), '*', 7);
                        $description = mb_substr($reflection->getDocComment(), 0, $offset);
                        $description = str_replace('*', '', $description);
                        $description = trim(str_replace('/', '', $description));
                    } else {
                        $description = '';
                    }

                    // check if model file exists
                    $pathName = $file->getPathInfo()->getPathInfo()->getRealPath();
                    if (is_file($pathName . '/engine/model.php')) {
                        // require model
                        require_once $pathName . '/engine/model.php';
                    }

                    // add to array
                    $this->widgets[] = array(
                        'module_name' => $module,
                        'checkbox_name' => \SpoonFilter::toCamelCase($module) . \SpoonFilter::toCamelCase($widgetName),
                        'label' => \SpoonFilter::toCamelCase($widgetName),
                        'value' => $widgetName,
                        'description' => $description,
                    );
                }
            }
        }
    }

    /**
     * Insert the permissions
     *
     * @param \SpoonFormElement[] $actionPermissions The action permissions.
     * @param array $bundledActionPermissions The bundled action permissions.
     */
    private function insertPermissions($actionPermissions, $bundledActionPermissions)
    {
        // init vars
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
                if (!in_array($module, $checkedModules)) {
                    $checkedModules[] = $module;
                }
            } else {
                // add to denied
                $actionsDenied[] = array('group_id' => $this->id, 'module' => $module, 'action' => $action, 'level' => ACTION_RIGHTS_LEVEL);

                // if not yet present add to unchecked modules
                if (!in_array($module, $uncheckedModules)) {
                    $uncheckedModules[] = $module;
                }
            }
        }

        // loop through bundled action permissions
        foreach ($bundledActionPermissions as $permission) {
            // get bits
            $bits = explode('_', $permission->getName());

            // convert camelcasing to underscore notation
            $module = $bits[1];
            $group = $bits[3];

            // loop through actions
            foreach ($this->actions[$module] as $moduleAction) {
                // permission checked?
                if ($permission->getChecked()) {
                    // add to granted if in the right group
                    if (in_array($group, $moduleAction)) {
                        $actionsGranted[] = array('group_id' => $this->id, 'module' => $module, 'action' => $moduleAction['value'], 'level' => ACTION_RIGHTS_LEVEL);
                    }

                    // if not yet present, add to checked modules
                    if (!in_array($module, $checkedModules)) {
                        $checkedModules[] = $module;
                    }
                } else {
                    // add to denied
                    if (in_array($group, $moduleAction)) {
                        $actionsDenied[] = array('group_id' => $this->id, 'module' => $module, 'action' => $moduleAction['value'], 'level' => ACTION_RIGHTS_LEVEL);
                    }

                    // if not yet present add to unchecked modules
                    if (!in_array($module, $uncheckedModules)) {
                        $uncheckedModules[] = $module;
                    }
                }
            }
        }

        // loop through granted modules and add to array
        foreach ($checkedModules as $module) {
            $modulesGranted[] = array('group_id' => $this->id, 'module' => $module);
        }

        // loop through denied modules and add to array
        foreach (array_diff($uncheckedModules, $checkedModules) as $module) {
            $modulesDenied[] = array('group_id' => $this->id, 'module' => $module);
        }

        // add granted permissions
        BackendGroupsModel::addModulePermissions($modulesGranted);
        BackendGroupsModel::addActionPermissions($actionsGranted);

        // delete denied permissions
        BackendGroupsModel::deleteModulePermissions($modulesDenied);
        BackendGroupsModel::deleteActionPermissions($actionsDenied);
    }

    /**
     * Insert the widgets
     *
     * @param \SpoonFormElement[] $widgetPresets The widgets presets.
     *
     * @return mixed
     */
    private function insertWidgets($widgetPresets)
    {
        // empty dashboard sequence
        $this->hiddenOnDashboard = array();

        // loop through all widgets
        foreach ($this->widgetInstances as $widget) {
            if (!BackendModel::isModuleInstalled($widget['module'])) {
                continue;
            }

            foreach ($widgetPresets as $preset) {
                if ($preset->getAttribute('id') !== 'widgets' . $widget['module'] . $widget['widget']) {
                    continue;
                }

                if (!$preset->getChecked()) {
                    if (!isset($this->hiddenOnDashboard[$widget['module']])) {
                        $this->hiddenOnDashboard[$widget['module']] = array();
                    }
                    $this->hiddenOnDashboard[$widget['module']][] = $widget['widget'];
                }
            }
        }

        // build group
        $userGroup['name'] = $this->frm->getField('name')->getValue();

        // build setting
        $setting['name'] = 'hidden_on_dashboard';
        $setting['value'] = serialize($this->hiddenOnDashboard);

        // insert group
        $userGroup['id'] = BackendGroupsModel::insert($userGroup, $setting);

        return $userGroup;
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        // create form
        $this->frm = new BackendForm('add');

        // widgets available?
        if (isset($this->widgets)) {
            // loop through widgets
            foreach ($this->widgets as $j => $widget) {
                // add widget checkboxes
                $widgetBoxes[$j]['check'] = '<span>' . $this->frm->addCheckbox('widgets_' . $widget['checkbox_name'], true)->parse() . '</span>';
                $widgetBoxes[$j]['module'] = \SpoonFilter::ucfirst(BL::lbl($widget['module_name']));
                $widgetBoxes[$j]['widget'] = '<label for="widgets' . \SpoonFilter::toCamelCase($widget['checkbox_name']) . '">' . $widget['label'] . '</label>';
                $widgetBoxes[$j]['description'] = $widget['description'];
            }
        }

        // loop through modules
        foreach ($this->modules as $key => $module) {
            // add module label
            $permissionBoxes[$key]['label'] = $module['label'];

            // init var
            $addedBundles = array();

            // loop through actions
            foreach ($this->actions[$module['value']] as $i => $action) {
                // action is bundled?
                if (array_key_exists('group', $action)) {
                    // bundle not yet in array?
                    if (!in_array($action['group'], $addedBundles)) {
                        // assign bundled action boxes
                        $actionBoxes[$key]['actions'][$i]['check'] = $this->frm->addCheckbox('actions_' . $module['label'] . '_' . 'Group_' . \SpoonFilter::ucfirst($action['group']))->parse();
                        $actionBoxes[$key]['actions'][$i]['action'] = \SpoonFilter::ucfirst($action['group']);
                        $actionBoxes[$key]['actions'][$i]['description'] = $this->actionGroups[$action['group']];

                        // add the group to the added bundles
                        $addedBundles[] = $action['group'];
                    }
                } else {
                    // assign action boxes
                    $actionBoxes[$key]['actions'][$i]['check'] = $this->frm->addCheckbox('actions_' . $module['label'] . '_' . $action['label'])->parse();
                    $actionBoxes[$key]['actions'][$i]['action'] = '<label for="actions' . \SpoonFilter::toCamelCase($module['label'] . '_' . $action['label']) . '">' . $action['label'] . '</label>';
                    $actionBoxes[$key]['actions'][$i]['description'] = $action['description'];
                }
            }

            // widgetboxes available?
            if (isset($widgetBoxes)) {
                // create datagrid
                $widgetGrid = new BackendDataGridArray($widgetBoxes);
                $widgetGrid->setHeaderLabels(array('check' => '<span class="checkboxHolder"><input id="toggleChecksWidgets" type="checkbox" name="toggleChecks" value="toggleChecks" /><span class="visuallyHidden"></span>'));

                // get content
                $widgets = $widgetGrid->getContent();
            }

            // create datagrid
            $actionGrid = new BackendDataGridArray($actionBoxes[$key]['actions']);
            $actionGrid->setHeaderLabels(array('check' => ''));

            // disable paging
            $actionGrid->setPaging(false);

            // get content of datagrids
            $permissionBoxes[$key]['actions']['dataGrid'] = $actionGrid->getContent();
            $permissionBoxes[$key]['chk'] = $this->frm->addCheckbox($module['label'], null, 'inputCheckbox checkBeforeUnload jsSelectAll')->parse();
            $permissionBoxes[$key]['id'] = \SpoonFilter::toCamelCase($module['label']);
        }

        // create elements
        $this->frm->addText('name');
        $this->frm->addDropdown('manage_users', array('Deny', 'Allow'));
        $this->frm->addDropdown('manage_groups', array('Deny', 'Allow'));
        $this->tpl->assign('permissions', $permissionBoxes);
        $this->tpl->assign('widgets', isset($widgets) ? $widgets : false);
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $bundledActionPermissions = array();

            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->frm->cleanupFields();

            // get fields
            $nameField = $this->frm->getField('name');

            foreach ($this->modules as $module) {
                // loop through actions
                foreach ($this->actions[$module['value']] as $action) {
                    // collect permissions if not bundled
                    if (!array_key_exists('group', $action)) {
                        $actionPermissions[] = $this->frm->getField('actions_' . $module['label'] . '_' . $action['label']);
                    }
                }

                // loop through bundled actions
                foreach ($this->actionGroups as $key => $group) {
                    // loop through all fields
                    foreach ($this->frm->getFields() as $field) {
                        // field exists?
                        if ($field->getName() == 'actions_' . $module['label'] . '_' . 'Group_' . \SpoonFilter::ucfirst($key)) {
                            // add to bundled actions
                            $bundledActionPermissions[] = $this->frm->getField('actions_' . $module['label'] . '_' . 'Group_' . \SpoonFilter::ucfirst($key));
                        }
                    }
                }
            }

            // loop through widgets and collect presets
            $widgetPresets = array();
            foreach ($this->widgets as $widget) {
                $widgetPresets[] = $this->frm->getField('widgets_' . $widget['checkbox_name']);
            }

            // validate fields
            $nameField->isFilled(BL::err('NameIsRequired'));

            // group already exists?
            if (BackendGroupsModel::alreadyExists($nameField->getValue())) {
                $nameField->setError(BL::err('GroupAlreadyExists'));
            }

            // no errors?
            if ($this->frm->isCorrect()) {
                // insert widgets
                $group = $this->insertWidgets($widgetPresets);

                // assign id
                $this->id = $group['id'];

                // insert permissions
                $this->insertPermissions($actionPermissions, $bundledActionPermissions);

                // everything is saved, so redirect to the overview
                $this->redirect(BackendModel::createURLForAction('Index') . '&report=added&var=' . rawurlencode($group['name']) . '&highlight=row-' . $group['id']);
            }
        }
    }
}
