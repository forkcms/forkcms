<?php

namespace Backend\Modules\Groups\Actions;

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
    private $actionGroups = [];

    /**
     * The actions
     *
     * @var array
     */
    private $actions = [];

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
    private function bundleActions(): void
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

    public function execute(): void
    {
        parent::execute();
        $this->getData();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    private function getActions(): void
    {
        $this->actions = [];
        $filter = ['Authentication', 'Error', 'Core'];
        $modules = [];

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

                $this->actions[$module][] = [
                    'label' => \SpoonFilter::toCamelCase($actionName),
                    'value' => $actionName,
                    'description' => $description,
                ];
            }
        }

        $modules = array_unique($modules);
        foreach ($modules as $module) {
            $this->modules[] = [
                'label' => \SpoonFilter::toCamelCase($module),
                'value' => $module,
            ];

            usort($this->actions[$module], function ($a, $b) {
                return strcmp($a["label"], $b["label"]);
            });
        }
    }

    private function getData(): void
    {
        $this->getWidgets();
        $this->getActions();
        $this->bundleActions();
    }

    private function getWidgets(): void
    {
        $this->widgets = [];
        $this->widgetInstances = [];

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
                    $this->widgetInstances[] = [
                        'module' => $module,
                        'widget' => $widgetName,
                        'className' => $class,
                    ];

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
                    $this->widgets[] = [
                        'module_name' => $module,
                        'checkbox_name' => \SpoonFilter::toCamelCase($module) . \SpoonFilter::toCamelCase($widgetName),
                        'label' => \SpoonFilter::toCamelCase($widgetName),
                        'value' => $widgetName,
                        'description' => $description,
                    ];
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
    private function insertPermissions(array $actionPermissions, array $bundledActionPermissions): void
    {
        $modulesDenied = [];
        $modulesGranted = [];
        $actionsDenied = [];
        $actionsGranted = [];
        $checkedModules = [];
        $uncheckedModules = [];

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
                $actionsGranted[] = ['group_id' => $this->id, 'module' => $module, 'action' => $action, 'level' => ACTION_RIGHTS_LEVEL];

                // if not yet present, add to checked modules
                if (!in_array($module, $checkedModules)) {
                    $checkedModules[] = $module;
                }
            } else {
                // add to denied
                $actionsDenied[] = ['group_id' => $this->id, 'module' => $module, 'action' => $action, 'level' => ACTION_RIGHTS_LEVEL];

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
                        $actionsGranted[] = ['group_id' => $this->id, 'module' => $module, 'action' => $moduleAction['value'], 'level' => ACTION_RIGHTS_LEVEL];
                    }

                    // if not yet present, add to checked modules
                    if (!in_array($module, $checkedModules)) {
                        $checkedModules[] = $module;
                    }
                } else {
                    // add to denied
                    if (in_array($group, $moduleAction)) {
                        $actionsDenied[] = ['group_id' => $this->id, 'module' => $module, 'action' => $moduleAction['value'], 'level' => ACTION_RIGHTS_LEVEL];
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
            $modulesGranted[] = ['group_id' => $this->id, 'module' => $module];
        }

        // loop through denied modules and add to array
        foreach (array_diff($uncheckedModules, $checkedModules) as $module) {
            $modulesDenied[] = ['group_id' => $this->id, 'module' => $module];
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
    private function insertWidgets(array $widgetPresets)
    {
        // empty dashboard sequence
        $this->hiddenOnDashboard = [];

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
                        $this->hiddenOnDashboard[$widget['module']] = [];
                    }
                    $this->hiddenOnDashboard[$widget['module']][] = $widget['widget'];
                }
            }
        }

        // build group
        $userGroup = [];
        $userGroup['name'] = $this->form->getField('name')->getValue();

        // build setting
        $setting = [];
        $setting['name'] = 'hidden_on_dashboard';
        $setting['value'] = serialize($this->hiddenOnDashboard);

        // insert group
        $userGroup['id'] = BackendGroupsModel::insert($userGroup, $setting);

        return $userGroup;
    }

    private function loadForm(): void
    {
        // create form
        $this->form = new BackendForm('add');

        $widgetBoxes = [];

        // widgets available?
        if (isset($this->widgets)) {
            // loop through widgets
            foreach ($this->widgets as $j => $widget) {
                // add widget checkboxes
                $widgetBoxes[$j]['check'] = '<span>' . $this->form->addCheckbox('widgets_' . $widget['checkbox_name'], true)->parse() . '</span>';
                $widgetBoxes[$j]['module'] = \SpoonFilter::ucfirst(BL::lbl($widget['module_name']));
                $widgetBoxes[$j]['widget'] = '<label for="widgets' . \SpoonFilter::toCamelCase($widget['checkbox_name']) . '">' . $widget['label'] . '</label>';
                $widgetBoxes[$j]['description'] = $widget['description'];
            }
        }

        $permissionBoxes = [];
        $actionBoxes = [];

        // loop through modules
        foreach ($this->modules as $key => $module) {
            // add module label
            $permissionBoxes[$key]['label'] = $module['label'];

            // init var
            $addedBundles = [];

            // loop through actions
            foreach ($this->actions[$module['value']] as $i => $action) {
                // action is bundled?
                if (array_key_exists('group', $action)) {
                    // bundle not yet in array?
                    if (!in_array($action['group'], $addedBundles)) {
                        // assign bundled action boxes
                        $actionBoxes[$key]['actions'][$i]['check'] = $this->form->addCheckbox('actions_' . $module['label'] . '_' . 'Group_' . \SpoonFilter::ucfirst($action['group']))->parse();
                        $actionBoxes[$key]['actions'][$i]['action'] = \SpoonFilter::ucfirst($action['group']);
                        $actionBoxes[$key]['actions'][$i]['description'] = $this->actionGroups[$action['group']];

                        // add the group to the added bundles
                        $addedBundles[] = $action['group'];
                    }
                } else {
                    // assign action boxes
                    $actionBoxes[$key]['actions'][$i]['check'] = $this->form->addCheckbox('actions_' . $module['label'] . '_' . $action['label'])->parse();
                    $actionBoxes[$key]['actions'][$i]['action'] = '<label for="actions' . \SpoonFilter::toCamelCase($module['label'] . '_' . $action['label']) . '">' . $action['label'] . '</label>';
                    $actionBoxes[$key]['actions'][$i]['description'] = $action['description'];
                }
            }

            // widgetboxes available?
            if (count($widgetBoxes) > 0) {
                // create datagrid
                $widgetGrid = new BackendDataGridArray($widgetBoxes);
                $widgetGrid->setHeaderLabels(['check' => '<span class="checkboxHolder"><input id="toggleChecksWidgets" type="checkbox" name="toggleChecks" value="toggleChecks" />']);

                // get content
                $widgets = $widgetGrid->getContent();
            }

            // create datagrid
            $actionGrid = new BackendDataGridArray($actionBoxes[$key]['actions']);
            $actionGrid->setHeaderLabels(['check' => '']);

            // disable paging
            $actionGrid->setPaging(false);

            // get content of datagrids
            $permissionBoxes[$key]['actions']['dataGrid'] = $actionGrid->getContent();
            $permissionBoxes[$key]['chk'] = $this->form->addCheckbox(
                $module['label'],
                false,
                'inputCheckbox checkBeforeUnload jsSelectAll'
            )->parse();
            $permissionBoxes[$key]['id'] = \SpoonFilter::toCamelCase($module['label']);
        }

        // create elements
        $this->form->addText('name');
        $this->form->addDropdown('manage_users', ['Deny', 'Allow']);
        $this->form->addDropdown('manage_groups', ['Deny', 'Allow']);
        $this->template->assign('permissions', $permissionBoxes);
        $this->template->assign('widgets', $widgets ?? false);
    }

    private function validateForm(): void
    {
        if ($this->form->isSubmitted()) {
            $bundledActionPermissions = [];

            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->form->cleanupFields();

            // get fields
            $nameField = $this->form->getField('name');

            $actionPermissions = [];
            foreach ($this->modules as $module) {
                // loop through actions
                foreach ($this->actions[$module['value']] as $action) {
                    // collect permissions if not bundled
                    if (!array_key_exists('group', $action)) {
                        $actionPermissions[] = $this->form->getField('actions_' . $module['label'] . '_' . $action['label']);
                    }
                }

                // loop through bundled actions
                foreach ($this->actionGroups as $key => $group) {
                    // loop through all fields
                    foreach ($this->form->getFields() as $field) {
                        // field exists?
                        if ($field->getName() == 'actions_' . $module['label'] . '_' . 'Group_' . \SpoonFilter::ucfirst($key)) {
                            // add to bundled actions
                            $bundledActionPermissions[] = $this->form->getField('actions_' . $module['label'] . '_' . 'Group_' . \SpoonFilter::ucfirst($key));
                        }
                    }
                }
            }

            // loop through widgets and collect presets
            $widgetPresets = [];
            foreach ($this->widgets as $widget) {
                $widgetPresets[] = $this->form->getField('widgets_' . $widget['checkbox_name']);
            }

            // validate fields
            $nameField->isFilled(BL::err('NameIsRequired'));

            // group already exists?
            if (BackendGroupsModel::alreadyExists($nameField->getValue())) {
                $nameField->setError(BL::err('GroupAlreadyExists'));
            }

            // no errors?
            if ($this->form->isCorrect()) {
                // insert widgets
                $group = $this->insertWidgets($widgetPresets);

                // assign id
                $this->id = $group['id'];

                // insert permissions
                $this->insertPermissions($actionPermissions, $bundledActionPermissions);

                // everything is saved, so redirect to the overview
                $this->redirect(BackendModel::createUrlForAction('Index') . '&report=added&var=' . rawurlencode($group['name']) . '&highlight=row-' . $group['id']);
            }
        }
    }
}
