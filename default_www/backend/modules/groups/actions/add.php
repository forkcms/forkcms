<?php

/**
 * This is the add-action, it will display a form to create a new group
 *
 * @package		backend
 * @subpackage	groups
 * @actiongroup	management	This is the all-around management action.
 *
 * @author		Jeroen Van den Bossche <jeroenvandenbossche@netlash.com>
 * @since		2.0
 */
class BackendGroupsAdd extends BackendBaseActionAdd
{
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
	private $dashboardSequence;


	/**
	 * The id of the new group
	 *
	 * @var	int
	 */
	private $id;


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
	 *
	 * @return	void
	 */
	private function bundleActions()
	{
		// loop through modules
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

				// add group to action
				$this->actions[$module['value']][$key]['group'] = $bits[0];

				// add group to array
				$this->actionGroups[$bits[0]] = end($bits);
			}
		}
	}


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get the data
		$this->getData();

		// load the form
		$this->loadForm();

		// validate the form
		$this->validateForm();

		// parse the form
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Get the actions
	 *
	 * @return	void
	 */
	private function getActions()
	{
		// create filter with modules which may not be displayed
		$filter = array('authentication', 'error', 'core');

		// get all active filtered modules
		$modules = array_diff(BackendModel::getModules(true), $filter);

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

						// get the offset
						$offset = strpos($reflection->getDocComment(), '*', 7);

						// get the first sentence
						$description = substr($reflection->getDocComment(), 0, $offset);

						// replacements
						$description = str_replace('*', '', $description);
						$description = trim(str_replace('/', '', $description));

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

						// get the offset
						$offset = strpos($reflection->getDocComment(), '*', 7);

						// get the first sentence
						$description = substr($reflection->getDocComment(), 0, $offset);

						// replacements
						$description = str_replace('*', '', $description);
						$description = trim(str_replace('/', '', $description));

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
	 * Get the data
	 *
	 * @return	void
	 */
	private function getData()
	{
		// get the widgets
		$this->getWidgets();

		// get the actions
		$this->getActions();

		// bundle actions
		$this->bundleActions();
	}


	/**
	 * Get the widgets
	 *
	 * @return	void
	 */
	private function getWidgets()
	{
		// get all active modules
		$modules = BackendModel::getModules(true);

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
	 * Insert the permissions
	 *
	 * @return	void
	 * @param	array $actionPermissions			The action permissions.
	 * @param	array $bundledActionPermissions		The bundled action permissions.
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
	 * Insert the widgets
	 *
	 * @return	void
	 * @param	array $widgetPresets	The widgets presets.
	 */
	private function insertWidgets($widgetPresets)
	{
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
 			                                                                           'present' => false));

			// loop through presets
			foreach($widgetPresets as $preset)
			{
				// if selected
				if($preset->getChecked())
				{
					// convert camelcasing to underscore notation
					$selected = trim(strtolower(preg_replace('/([A-Z])/', '_${1}', str_replace('widgets_', '', $preset->getName()))), '_');

					// if right widget set visible
					if($selected === $widget['widget']) $this->dashboardSequence[$widget['module']][$widget['widget']]['present'] = true;
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

		// return the group
		return $group;
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('add');

		// widgets available?
		if(isset($this->widgets))
		{
			// loop through widgets
			foreach($this->widgets as $j => $widget)
			{
				// add widget checkboxes
				$widgetBoxes[$j]['checkbox'] = $this->frm->addCheckbox('widgets_' . $widget['label'])->parse();
				$widgetBoxes[$j]['widget'] = $widget['label'];
				$widgetBoxes[$j]['description'] = $widget['description'];
			}
		}

		// loop through modules
		foreach($this->modules as $key => $module)
		{
			// add module label
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
						$actionBoxes[$key]['actions'][$i]['checkbox'] = $this->frm->addCheckbox('actions_' . $module['label'] . '_' . 'Group_' . ucfirst($action['group']))->parse();
						$actionBoxes[$key]['actions'][$i]['action'] = ucfirst($action['group']);
						$actionBoxes[$key]['actions'][$i]['description'] = $this->actionGroups[$action['group']];

						// add the group to the added bundles
						$addedBundles[] = $action['group'];
					}
				}

				// action not bundled
				else
				{
					// assign action boxes
					$actionBoxes[$key]['actions'][$i]['checkbox'] = $this->frm->addCheckbox('actions_' . $module['label'] . '_' . $action['label'])->parse();
					$actionBoxes[$key]['actions'][$i]['action'] = $action['label'];
					$actionBoxes[$key]['actions'][$i]['description'] = $action['description'];
				}
			}

			// widgetboxes available?
			if(isset($widgetBoxes))
			{
				// create datagrid
				$widgetGrid = new BackendDataGridArray($widgetBoxes);
				$widgetGrid->setHeaderLabels(array('checkbox' => '<span class="checkboxHolder"><input type="checkbox" name="toggleChecks" value="toggleChecks" /></span>'));

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
		}

		// create elements
		$this->frm->addText('name');
		$this->frm->addDropdown('manage_users', array('Deny', 'Allow'));
		$this->frm->addDropdown('manage_groups', array('Deny', 'Allow'));
		$this->tpl->assign('permissions', $permissionBoxes);
		$this->tpl->assign('widgets', isset($widgets) ?  $widgets : false);
	}


	/**
	 * Parse the form
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// call parent
		parent::parse();
	}


	/**
	 * Validate the form
	 *
	 * @return	void
	 */
	private function validateForm()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
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
						if($field->getName() == 'actions_' . $module['label'] . '_' . 'Group_' . ucfirst($key))
						{
							// add to bundled actions
							$bundledActionPermissions[] = $this->frm->getField('actions_' . $module['label'] . '_' . 'Group_' . ucfirst($key));
						}
					}
				}
			}

			// loop through widgets and collect presets
			foreach($this->widgets as $widget) $widgetPresets[] = $this->frm->getField('widgets_' . $widget['label']);

			// validate fields
			$nameField->isFilled(BL::err('NameIsRequired'));

			// group already exists?
			if(BackendGroupsModel::alreadyExists($nameField->getValue())) $nameField->setError(BL::err('GroupAlreadyExists'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// insert widgets
				$group = $this->insertWidgets($widgetPresets);

				// assign id
				$this->id = $group['id'];

				// insert permissions
				$this->insertPermissions($actionPermissions, $bundledActionPermissions);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') . '&report=added&var=' . urlencode($group['name']) . '&highlight=row-' . $group['id']);
			}
		}
	}
}

?>