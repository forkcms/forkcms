<?php

namespace Backend\Modules\Groups\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\DataGridArray as BackendDataGridArray;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Groups\Engine\Model as BackendGroupsModel;
use Backend\Modules\Groups\Engine\Permissions as GroupsPermissions;

/**
 * This is the add-action, it will display a form to create a new group
 *
 * @author Jeroen Van den Bossche <jeroenvandenbossche@netlash.com>
 */
class Add extends GroupsPermissions
{
    /**
     * The dashboard sequence
     *
     * @var	array
     */
    private $dashboardSequence;

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
     * Get the data
     */
    private function getData()
    {
        $this->getWidgets();
        $this->getActions();
        $this->bundleActions();
    }

    /**
     * Insert the permissions
     *
     * @param array $actionPermissions The action permissions.
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
     * Insert the widgets
     *
     * @param array $widgetPresets The widgets presets.
     */
    private function insertWidgets($widgetPresets)
    {
        // loop through all widgets
        foreach ($this->widgetInstances as $widget) {
            // create instance
            $instance = new $widget['className']($this->getKernel());

            // execute instance
            $instance->execute();

            // create module array if no existence
            if (!isset($this->dashboardSequence[$widget['module']])) $this->dashboardSequence[$widget['module']] = array();

            // create dashboard sequence
            $this->dashboardSequence[$widget['module']] += array(
                $widget['widget'] => array(
                    'column' => $instance->getColumn(),
                    'position' => (int) $instance->getPosition(),
                    'hidden' => false,
                    'present' => false
                )
            );

            foreach ($widgetPresets as $preset) {
                if ($preset->getChecked()) {
                    // remove widgets_ prefix
                    $selected = str_replace('widgets_', '', $preset->getName());

                    // if right widget set visible
                    if ($selected == $widget['widget']) $this->dashboardSequence[$widget['module']][$widget['widget']]['present'] = true;
                }
            }
        }

        // build group
        $group['name'] = $this->frm->getField('name')->getValue();

        // build setting
        $setting['name'] = 'dashboard_sequence';
        $setting['value'] = serialize($this->dashboardSequence);

        // insert group and settings
        $group['id'] = BackendGroupsModel::insert($group, $setting);

        return $group;
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
                $widgetBoxes[$j]['checkbox'] = '<span>' . $this->frm->addCheckbox('widgets_' . $widget['label'])->parse() . '</span>';
                $widgetBoxes[$j]['widget'] = '<label for="widgets' . \SpoonFilter::toCamelCase($widget['label']) . '">' . $widget['label'] . '</label>';
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
                        $actionBoxes[$key]['actions'][$i]['checkbox'] = $this->frm->addCheckbox('actions_' . $module['label'] . '_' . 'Group_' . \SpoonFilter::ucfirst($action['group']))->parse();
                        $actionBoxes[$key]['actions'][$i]['action'] = \SpoonFilter::ucfirst($action['group']);
                        $actionBoxes[$key]['actions'][$i]['description'] = $this->actionGroups[$action['group']];

                        // add the group to the added bundles
                        $addedBundles[] = $action['group'];
                    }
                }

                // action not bundled
                else {
                    // assign action boxes
                    $actionBoxes[$key]['actions'][$i]['checkbox'] = $this->frm->addCheckbox('actions_' . $module['label'] . '_' . $action['label'])->parse();
                    $actionBoxes[$key]['actions'][$i]['action'] = '<label for="actions' . \SpoonFilter::toCamelCase($module['label'] . '_' . $action['label']) . '">' . $action['label'] . '</label>';
                    $actionBoxes[$key]['actions'][$i]['description'] = $action['description'];
                }
            }

            // widgetboxes available?
            if (isset($widgetBoxes)) {
                // create datagrid
                $widgetGrid = new BackendDataGridArray($widgetBoxes);
                $widgetGrid->setHeaderLabels(array('checkbox' => '<span class="checkboxHolder"><input id="toggleChecksWidgets" type="checkbox" name="toggleChecks" value="toggleChecks" /><span class="visuallyHidden"></span>'));

                // get content
                $widgets = $widgetGrid->getContent();
            }

            // create datagrid
            $actionGrid = new BackendDataGridArray($actionBoxes[$key]['actions']);

            // disable paging
            $actionGrid->setPaging(false);

            // get content of datagrids
            $permissionBoxes[$key]['actions']['dataGrid'] = $actionGrid->getContent();
            $permissionBoxes[$key]['chk'] = $this->frm->addCheckbox($module['label'], null, 'inputCheckbox checkBeforeUnload selectAll')->parse();
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
     * Parse the form
     *
     * @todo method is not necessary see the content...
     */
    protected function parse()
    {
        parent::parse();
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
                    if (!array_key_exists('group', $action)) $actionPermissions[] = $this->frm->getField('actions_' . $module['label'] . '_' . $action['label']);
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
            foreach ($this->widgets as $widget) $widgetPresets[] = $this->frm->getField('widgets_' . $widget['label']);

            // validate fields
            $nameField->isFilled(BL::err('NameIsRequired'));

            // group already exists?
            if (BackendGroupsModel::alreadyExists($nameField->getValue())) $nameField->setError(BL::err('GroupAlreadyExists'));

            // no errors?
            if ($this->frm->isCorrect()) {
                // insert widgets
                $group = $this->insertWidgets($widgetPresets);

                // assign id
                $this->id = $group['id'];

                // insert permissions
                $this->insertPermissions($actionPermissions, $bundledActionPermissions);

                // trigger event
                BackendModel::triggerEvent($this->getModule(), 'after_add', array('item' => $group));

                // everything is saved, so redirect to the overview
                $this->redirect(BackendModel::createURLForAction('Index') . '&report=added&var=' . urlencode($group['name']) . '&highlight=row-' . $group['id']);
            }
        }
    }
}
