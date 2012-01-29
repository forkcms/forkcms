<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit-action, it will display a form to edit a group
 *
 * @author Jeroen Van den Bossche <jeroenvandenbossche@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendGroupsEdit extends BackendBaseActionEdit
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
	 * @var	array
	 */
	private $actions = array();

	/**
	 * The dashboard sequence
	 *
	 * @var	array
	 */
	private $dashboardSequence = array();

	/**
	 * The users datagrid
	 *
	 * @var	BackendDataGrid
	 */
	private $dataGridUsers;

	/**
	 * The modules
	 *
	 * @var	array
	 */
	private $modules;

	/**
	 * The widgets
	 *
	 * @var	array
	 */
	private $widgets;

	/**
	 * The widget instances
	 *
	 * @var	array
	 */
	private $widgetInstances;

	/**
	 * Bundle all actions that need to be bundled
	 */
	private function bundleActions()
	{
		foreach($this->modules as $module)
		{
			// loop through actions and add all classnames
			foreach($this->actions[$module['value']] as $key => $action)
			{
				// ajax action?
				if(SpoonFile::exists(BACKEND_MODULES_PATH . '/' . $module['value'] . '/ajax/' . $action['value'] . '.php'))
				{
					// create reflection class
					$reflection = new ReflectionClass('Backend' . $module['label'] . 'Ajax' . $action['label']);
				}

				// no ajax action? create reflection class
				else $reflection = new ReflectionClass('Backend' . $module['label'] . $action['label']);

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
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->getData();
		$this->loadDataGrids();
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
		// create filter with modules which may not be displayed
		$filter = array('authentication', 'error', 'core');

		// get all modules
		$modules = array_diff(BackendModel::getModules(), $filter);

		// loop all modules
		foreach($modules as $module)
		{
			// you have sufficient rights?
			if(BackendAuthentication::isAllowedModule($module))
			{
				// build pathName
				$pathName = BACKEND_MODULES_PATH . '/' . $module;

				// module has actions?
				if(SpoonDirectory::exists($pathName . '/actions'))
				{
					// get actions
					$actions = (array) SpoonFile::getList($pathName . '/actions', '/(.*)\.php/i');
					$ajaxActions = (array) SpoonFile::getList($pathName . '/ajax', '/(.*)\.php/i');

					// loop through actions
					foreach($actions as $action)
					{
						// get action name
						$actionName = str_replace('.php', '', $action);

						// create reflection class
						$reflection = new ReflectionClass('Backend' . SpoonFilter::toCamelCase($module) . SpoonFilter::toCamelCase($actionName));

						// get the comment
						$phpDoc = trim($reflection->getDocComment());

						if($phpDoc != '')
						{
							// get the offset
							$offset = strpos($reflection->getDocComment(), '*', 7);

							// get the first sentence
							$description = substr($reflection->getDocComment(), 0, $offset);

							// replacements
							$description = str_replace('*', '', $description);
							$description = trim(str_replace('/', '', $description));
						}

						else $description = '';

						// assign actions to array
						$this->actions[$module][] = array('label' => SpoonFilter::toCamelCase($actionName), 'value' => $actionName, 'description' => $description);
					}

					// loop through ajax actions
					foreach($ajaxActions as $action)
					{
						// get action name
						$actionName = str_replace('.php', '', $action);

						// require file
						require_once BACKEND_MODULES_PATH . '/' . $module . '/ajax/' . $action;

						// create reflection class
						$reflection = new ReflectionClass('Backend' . SpoonFilter::toCamelCase($module) . 'Ajax' . SpoonFilter::toCamelCase($actionName));

						// get the comment
						$phpDoc = trim($reflection->getDocComment());

						if($phpDoc != '')
						{
							// get the offset
							$offset = strpos($reflection->getDocComment(), '*', 7);

							// get the first sentence
							$description = substr($reflection->getDocComment(), 0, $offset);

							// replacements
							$description = str_replace('*', '', $description);
							$description = trim(str_replace('/', '', $description));
						}

						else $description = '';

						// assign actions to array
						$this->actions[$module][] = array('label' => SpoonFilter::toCamelCase($actionName), 'value' => $actionName, 'description' => $description);
					}

					// module has actions?
					if(!empty($this->actions[$module])) $this->modules[] = array('label' => SpoonFilter::toCamelCase($module), 'value' => $module);
				}
			}
		}
	}

	/**
	 * Get the data to edit
	 */
	private function getData()
	{
		$this->id = $this->getParameter('id');

		// get dashboard sequence
		$this->dashboardSequence = BackendGroupsModel::getSetting($this->id, 'dashboard_sequence');

		// get the record
		$this->record = BackendGroupsModel::get($this->id);

		// no item found, throw an exceptions, because somebody is fucking with our URL
		if(empty($this->record)) $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');

		$this->getWidgets();
		$this->getActions();
		$this->bundleActions();
	}

	/**
	 * Get the widgets
	 */
	private function getWidgets()
	{
		// get all modules
		$modules = BackendModel::getModules();

		// loop all modules
		foreach($modules as $module)
		{
			// you have sufficient rights?
			if(BackendAuthentication::isAllowedModule($module))
			{
				// build pathName
				$pathName = BACKEND_MODULES_PATH . '/' . $module;

				// check if the folder exists
				if(SpoonDirectory::exists($pathName . '/widgets'))
				{
					// get widgets
					$widgets = (array) SpoonFile::getList($pathName . '/widgets', '/(.*)\.php/i');

					// loop through widgets
					foreach($widgets as $widget)
					{
						// require the classes
						require_once $pathName . '/widgets/' . $widget;

						// init var
						$widgetName = str_replace('.php', '', $widget);

						// build classname
						$className = 'Backend' . SpoonFilter::toCamelCase($module) . 'Widget' . SpoonFilter::toCamelCase($widgetName);

						// validate if the class exists
						if(!class_exists($className))
						{
							// throw exception
							throw new BackendException('The widgetfile is present, but the classname should be: ' . $className . '.');
						}

						// class exists
						else
						{
							// add to array
							$this->widgetInstances[] = array('module' => $module, 'widget' => $widgetName, 'className' => $className);

							// create reflection class
							$reflection = new ReflectionClass('Backend' . SpoonFilter::toCamelCase($module) . 'Widget' . SpoonFilter::toCamelCase($widgetName));

							// get the offset
							$offset = strpos($reflection->getDocComment(), '*', 7);

							// get the first sentence
							$description = substr($reflection->getDocComment(), 0, $offset);

							// replacements
							$description = str_replace('*', '', $description);
							$description = trim(str_replace('/', '', $description));
						}

						// check if model file exists
						if(SpoonFile::exists($pathName . '/engine/model.php'))
						{
							// require model
							require_once $pathName . '/engine/model.php';
						}

						// add to array
						$this->widgets[] = array('label' => SpoonFilter::toCamelCase($widgetName), 'value' => $widgetName, 'description' => $description);
					}
				}
			}
		}
	}

	/**
	 * Load the datagrid
	 */
	private function loadDataGrids()
	{
		$this->dataGridUsers = new BackendDataGridDB(BackendGroupsModel::QRY_ACTIVE_USERS, array($this->id, 'N'));

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('edit', 'users'))
		{
			// add columns
			$this->dataGridUsers->addColumn('nickname', SpoonFilter::ucfirst(BL::lbl('Nickname')), null, BackendModel::createURLForAction('edit', 'users') . '&amp;id=[id]');
			$this->dataGridUsers->addColumn('surname', SpoonFilter::ucfirst(BL::lbl('Surname')), null, BackendModel::createURLForAction('edit', 'users') . '&amp;id=[id]');
			$this->dataGridUsers->addColumn('name', SpoonFilter::ucfirst(BL::lbl('Name')), null, BackendModel::createURLForAction('edit', 'users') . '&amp;id=[id]');

			// add column URL
			$this->dataGridUsers->setColumnURL('email', BackendModel::createURLForAction('edit', 'users') . '&amp;id=[id]');

			// set columns sequence
			$this->dataGridUsers->setColumnsSequence('nickname', 'surname', 'name', 'email');

			// show users's name, surname and nickname
			$this->dataGridUsers->setColumnFunction(array('BackendUser', 'getSettingByUserId'), array('[id]', 'surname'), 'surname', false);
			$this->dataGridUsers->setColumnFunction(array('BackendUser', 'getSettingByUserId'), array('[id]', 'name'), 'name', false);
			$this->dataGridUsers->setColumnFunction(array('BackendUser', 'getSettingByUserId'), array('[id]', 'nickname'), 'nickname', false);
		}
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		$this->frm = new BackendForm('edit');

		// get selected permissions
		$modulePermissions = BackendGroupsModel::getModulePermissions($this->id);
		$actionPermissions = BackendGroupsModel::getActionPermissions($this->id);

		// add selected modules to array
		foreach($modulePermissions as $permission) $selectedModules[] = $permission['module'];

		// loop through modules
		foreach($this->modules as $key => $module)
		{
			// widgets available?
			if(isset($this->widgets))
			{
				// loop through widgets
				foreach($this->widgets as $j => $widget)
				{
					// widget is present?
					if(isset($this->dashboardSequence[$module['value']][$widget['value']]['present']) && $this->dashboardSequence[$module['value']][$widget['value']]['present'] === true)
					{
						// add to array
						$selectedWidgets[$j] = $widget['value'];
					}

					// add widget checkboxes
					$widgetBoxes[$j]['checkbox'] = '<span>' . $this->frm->addCheckbox('widgets_' . $widget['label'], isset($selectedWidgets[$j]) ? $selectedWidgets[$j] : null)->parse() . '</span>';
					$widgetBoxes[$j]['widget'] = '<label for="widgets' . SpoonFilter::toCamelCase($widget['label']) . '">' . $widget['label'] . '</label>';
					$widgetBoxes[$j]['description'] = $widget['description'];
				}
			}

			$selectedActions = array();

			// loop through action permissions
			foreach($actionPermissions as $permission)
			{
				// add to selected actions
				if($permission['module'] == $module['value']) $selectedActions[] = $permission['action'];
			}

			// add module labels
			$permissionBoxes[$key]['label'] = $module['label'];

			// init var
			$addedBundles = array();

			// loop through actions
			foreach($this->actions[$module['value']] as $i => $action)
			{
				// action is bundled?
				if(array_key_exists('group', $action))
				{
					// bundle not yet in array?
					if(!in_array($action['group'], $addedBundles))
					{
						// assign bundled action boxes
						$actionBoxes[$key]['actions'][$i]['checkbox'] = $this->frm->addCheckbox('actions_' . $module['label'] . '_' . 'Group_' . SpoonFilter::ucfirst($action['group']), in_array($action['value'], $selectedActions))->parse();
						$actionBoxes[$key]['actions'][$i]['action'] = SpoonFilter::ucfirst($action['group']);
						$actionBoxes[$key]['actions'][$i]['description'] = $this->actionGroups[$action['group']];

						// add the group to the added bundles
						$addedBundles[] = $action['group'];
					}
				}

				// action not bundled
				else
				{
					// assign action boxes
					$actionBoxes[$key]['actions'][$i]['checkbox'] = $this->frm->addCheckbox('actions_' . $module['label'] . '_' . $action['label'], in_array($action['value'], $selectedActions))->parse();
					$actionBoxes[$key]['actions'][$i]['action'] = '<label for="actions' . SpoonFilter::toCamelCase($module['label'] . '_' . $action['label']) . '">' . $action['label'] . '</label>';
					$actionBoxes[$key]['actions'][$i]['description'] = $action['description'];
				}
			}

			// widgetboxes available?
			if(isset($widgetBoxes))
			{
				// create datagrid
				$widgetGrid = new BackendDataGridArray($widgetBoxes);
				$widgetGrid->setHeaderLabels(array('checkbox' => '<span class="checkboxHolder"><input id="toggleChecksWidgets" type="checkbox" name="toggleChecks" value="toggleChecks" /></span>'));

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
			$permissionBoxes[$key]['id'] = SpoonFilter::toCamelCase($module['label']);

		}

		// create elements
		$this->frm->addText('name', $this->record['name']);
		$this->frm->addDropdown('manage_users', array('Deny', 'Allow'));
		$this->frm->addDropdown('manage_groups', array('Deny', 'Allow'));
		$this->tpl->assign('permissions', $permissionBoxes);
		$this->tpl->assign('widgets', isset($widgets) ? $widgets : false);
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		parent::parse();

		$this->tpl->assign('dataGridUsers', ($this->dataGridUsers->getNumResults() != 0) ? $this->dataGridUsers->getContent() : false);
		$this->tpl->assign('item', $this->record);
		$this->tpl->assign('groupName', $this->record['name']);

		// only allow deletion of empty groups
		$this->tpl->assign('showGroupsDelete', $this->dataGridUsers->getNumResults() == 0 && BackendAuthentication::isAllowedAction('delete'));
	}

	/**
	 * Update the permissions
	 *
	 * @param array $actionPermissions The action permissions.
	 * @param array $bundledActionPermissions The bundled action permissions.
	 */
	private function updatePermissions($actionPermissions, $bundledActionPermissions)
	{
		$modulesDenied = array();
		$modulesGranted = array();
		$actionsDenied = array();
		$actionsGranted = array();
		$checkedModules = array();
		$uncheckedModules = array();

		// loop through action permissions
		foreach($actionPermissions as $permission)
		{
			// get bits
			$bits = explode('_', $permission->getName());

			// convert camelcasing to underscore notation
			$module = trim(strtolower(preg_replace('/([A-Z])/', '_${1}', $bits[1])), '_');
			$action = trim(strtolower(preg_replace('/([A-Z])/', '_${1}', $bits[2])), '_');

			// permission checked?
			if($permission->getChecked())
			{
				// add to granted
				$actionsGranted[] = array('group_id' => $this->id, 'module' => $module, 'action' => $action, 'level' => ACTION_RIGHTS_LEVEL);

				// if not yet present, add to checked modules
				if(!in_array($module, $checkedModules)) $checkedModules[] = $module;
			}

			// permission not checked?
			else
			{
				// add to denied
				$actionsDenied[] = array('group_id' => $this->id, 'module' => $module, 'action' => $action, 'level' => ACTION_RIGHTS_LEVEL);

				// if not yet present add to unchecked modules
				if(!in_array($module, $uncheckedModules)) $uncheckedModules[] = $module;
			}
		}

		// loop through bundled action permissions
		foreach($bundledActionPermissions as $permission)
		{
			// get bits
			$bits = explode('_', $permission->getName());

			// convert camelcasing to underscore notation
			$module = trim(strtolower(preg_replace('/([A-Z])/', '_${1}', $bits[1])), '_');
			$group = trim(strtolower(preg_replace('/([A-Z])/', '_${1}', $bits[3])), '_');

			// create new item
			$moduleItem = array('group_id' => $this->id, 'module' => $module);

			// loop through actions
			foreach($this->actions[$module] as $moduleAction)
			{
				// permission checked?
				if($permission->getChecked())
				{
					// add to granted if in the right group
					if(in_array($group, $moduleAction)) $actionsGranted[] = array('group_id' => $this->id, 'module' => $module, 'action' => $moduleAction['value'], 'level' => ACTION_RIGHTS_LEVEL);

					// if not yet present, add to checked modules
					if(!in_array($module, $checkedModules)) $checkedModules[] = $module;
				}

				// permission not checked?
				else
				{
					// add to denied
					if(in_array($group, $moduleAction)) $actionsDenied[] = array('group_id' => $this->id, 'module' => $module, 'action' => $moduleAction['value'], 'level' => ACTION_RIGHTS_LEVEL);

					// if not yet present add to unchecked modules
					if(!in_array($module, $uncheckedModules)) $uncheckedModules[] = $module;
				}
			}
		}

		// loop through granted modules and add to array
		foreach($checkedModules as $module) $modulesGranted[] = array('group_id' => $this->id, 'module' => $module);

		// loop through denied modules and add to array
		foreach(array_diff($uncheckedModules, $checkedModules) as $module) $modulesDenied[] = array('group_id' => $this->id, 'module' => $module);

		// add granted permissions
		BackendGroupsModel::addModulePermissions($modulesGranted);
		BackendGroupsModel::addActionPermissions($actionsGranted);

		// delete denied permissions
		BackendGroupsModel::deleteModulePermissions($modulesDenied);
		BackendGroupsModel::deleteActionPermissions($actionsDenied);
	}

	/**
	 * Update the widgets
	 *
	 * @param array $widgetPresets The widgets presets.
	 * @return array
	 */
	private function updateWidgets($widgetPresets)
	{
		// empty dashboard sequence
		$this->dashboardSequence = array();

		// get users
		$users = BackendGroupsModel::getUsers($this->id);

		// loop through users and create objects
		foreach($users as $user) $userObjects[] = new BackendUser($user['id']);

		// any users present?
		if(!empty($userObjects))
		{
			// loop through user objects and get all sequences
			foreach($userObjects as $user) $userSequences[$user->getUserId()] = $user->getSetting('dashboard_sequence');
		}

		// loop through all widgets
		foreach($this->widgetInstances as $widget)
		{
			// create instance
			$instance = new $widget['className']();

			// execute instance
			$instance->execute();

			// create module array if no existance
			if(!isset($this->dashboardSequence[$widget['module']])) $this->dashboardSequence[$widget['module']] = array();

			// create dashboard sequence
			$this->dashboardSequence[$widget['module']] += array(
				$widget['widget'] => array(
					'column' => $instance->getColumn(),
					'position' => (int) $instance->getPosition(),
					'hidden' => false,
					'present' => false
				)
			);

			// loop through selected widgets
			foreach($widgetPresets as $preset)
			{
				// if selected
				if($preset->getChecked())
				{
					// convert camelcasing to underscore notation
					$selected = trim(strtolower(preg_replace('/([A-Z])/', '_${1}', str_replace('widgets_', '', $preset->getName()))), '_');

					// if selected is the right widget, set visible
					if($selected == $widget['widget']) $this->dashboardSequence[$widget['module']][$widget['widget']]['present'] = true;
				}
			}
		}

		// build group
		$group['name'] = $this->frm->getField('name')->getValue();
		$group['id'] = $this->id;

		// build setting
		$setting['group_id'] = $this->id;
		$setting['name'] = 'dashboard_sequence';
		$setting['value'] = serialize($this->dashboardSequence);

		// update group
		BackendGroupsModel::update($group, $setting);

		// loop through all widgets
		foreach($this->widgetInstances as $widget)
		{
			// loop through users
			foreach($users as $user)
			{
				// unset visible if already present
				if(isset($userSequences[$user['id']][$widget['module']][$widget['widget']])) $userSequences[$user['id']][$widget['module']][$widget['widget']]['present'] = false;

				// get groups for user
				$groups = BackendGroupsModel::getGroupsByUser($user['id']);

				// loop through groups
				foreach($groups as $group)
				{
					// get group sequence
					$groupSequence = BackendGroupsModel::getSetting($group['id'] , 'dashboard_sequence');

					// loop through selected widgets
					foreach($widgetPresets as $preset)
					{
						// if selected
						if($preset->getChecked())
						{
							// convert camelcasing to underscore notation
							$selected = trim(strtolower(preg_replace('/([A-Z])/', '_${1}', str_replace('widgets_', '', $preset->getName()))), '_');

							// if selected is the right widget
							if($selected == $widget['widget'])
							{
								// set widgets visible
								$this->dashboardSequence[$widget['module']][$widget['widget']]['present'] = true;

								// usersequence has widget?
								if(isset($userSequences[$user['id']][$widget['module']][$widget['widget']]))
								{
									// set visible
									$userSequences[$user['id']][$widget['module']][$widget['widget']]['present'] = true;
								}

								// else assign widget
								else
								{
									// assign module if not yet present
								 	if(!isset($userSequences[$user['id']][$widget['module']])) $userSequences[$user['id']][$widget['module']] = array();

								 	// add widget
								 	$userSequences[$user['id']][$widget['module']] += array(
								 		$widget['widget'] => array(
								 			'column' => $instance->getColumn(),
								 			'position' => (int) $instance->getPosition(),
								 			'hidden' => false,
								 			'present' => true
								 		)
								 	);
								}
							}
						}

						// widget in visible in other group?
						if($groupSequence[$widget['module']][$widget['widget']]['present'])
						{
							// set visible
							$userSequences[$user['id']][$widget['module']][$widget['widget']]['present'] = true;
						}
					}
				}
			}
		}

		// any users present?
		if(!empty($userObjects))
		{
			// loop through users and update sequence
			foreach($userObjects as $user) $user->setSetting('dashboard_sequence', $userSequences[$user->getUserId()]);
		}

		return $group;
	}

	/**
	 * Validate the form
	 */
	private function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			$bundledActionPermissions = array();

			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// get fields
			$nameField = $this->frm->getField('name');

			// loop through modules
			foreach($this->modules as $module)
			{
				// loop through actions
				foreach($this->actions[$module['value']] as $action)
				{
					// collect permissions if not bundled
					if(!array_key_exists('group', $action)) $actionPermissions[] = $this->frm->getField('actions_' . $module['label'] . '_' . $action['label']);
				}

				// loop through bundled actions
				foreach($this->actionGroups as $key => $group)
				{
					// loop through all fields
					foreach($this->frm->getFields() as $field)
					{
						// field exists?
						if($field->getName() == 'actions_' . $module['label'] . '_' . 'Group_' . SpoonFilter::ucfirst($key))
						{
							// add to bundled actions
							$bundledActionPermissions[] = $this->frm->getField('actions_' . $module['label'] . '_' . 'Group_' . SpoonFilter::ucfirst($key));
						}
					}
				}
			}

			// loop through widgets and collect presets
			foreach($this->widgets as $widget) $widgetPresets[] = $this->frm->getField('widgets_' . $widget['label']);

			// validate fields
			$nameField->isFilled(BL::err('NameIsRequired'));

			// new name given?
			if($nameField->getValue() !== $this->record['name'])
			{
				// group already exists?
				if(BackendGroupsModel::alreadyExists($nameField->getValue())) $nameField->setError(BL::err('GroupAlreadyExists'));
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				// update widgets
				$group = $this->updateWidgets($widgetPresets);

				// update permissions
				$this->updatePermissions($actionPermissions, $bundledActionPermissions);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_edit', array('item' => $group));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') . '&report=edited&var=' . urlencode($group['name']) . '&highlight=row-' . $group['id']);
			}
		}
	}
}
