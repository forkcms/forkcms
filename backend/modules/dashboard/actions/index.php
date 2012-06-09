<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the index-action (default), it will display the login screen
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendDashboardIndex extends BackendBaseActionIndex
{
	/**
	 * The widgets
	 *
	 * @var	array
	 */
	private $widgets = array('left' => array(), 'middle' => array(), 'right' => array());

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->loadData();
		$this->parse();
		$this->display();
	}

	/**
	 * Load the data
	 */
	private function loadData()
	{
		// get all modules
		$modules = BackendModel::getModules();

		// get user sequence
		$userSequence = BackendAuthentication::getUser()->getSetting('dashboard_sequence');

		// user sequence does not exist?
		if(!isset($userSequence))
		{
			// get group ID of user
			$groupId = BackendAuthentication::getUser()->getGroupId();

			// get group preset
			$userSequence = BackendGroupsModel::getSetting($groupId, 'dashboard_sequence');
		}

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

					// loop widgets
					foreach($widgets as $widget)
					{
						// require the class
						require_once $pathName . '/widgets/' . $widget;

						// init var
						$widgetName = str_replace('.php', '', $widget);

						// build classname
						$className = 'Backend' . SpoonFilter::toCamelCase($module) . 'Widget' . SpoonFilter::toCamelCase($widgetName);

						// validate if the class exists
						if(!class_exists($className)) throw new BackendException('The widgetfile is present, but the classname should be: ' . $className . '.');

						// check if model file exists
						if(SpoonFile::exists($pathName . '/engine/model.php'))
						{
							// require model
							require_once $pathName . '/engine/model.php';
						}

						// present?
						$present = (isset($userSequence[$module][$widgetName]['present'])) ? $userSequence[$module][$widgetName]['present'] : false;

						// if not present, continue
						if(!$present) continue;

						// create instance
						$instance = new $className();

						// has rights
						if(!$instance->isAllowed()) continue;

						// hidden?
						$hidden = (isset($userSequence[$module][$widgetName]['hidden'])) ? $userSequence[$module][$widgetName]['hidden'] : false;

						// execute instance if it is not hidden
						if(!$hidden) $instance->execute();

						// user sequence provided?
						$column = (isset($userSequence[$module][$widgetName]['column'])) ? $userSequence[$module][$widgetName]['column'] : $instance->getColumn();
						$position = (isset($userSequence[$module][$widgetName]['position'])) ? $userSequence[$module][$widgetName]['position'] : $instance->getPosition();
						$title = SpoonFilter::ucfirst(BL::lbl(SpoonFilter::toCamelCase($module))) . ': ' . BL::lbl(SpoonFilter::toCamelCase($widgetName));
						$templatePath = $instance->getTemplatePath();

						// reset template path
						if($templatePath == null) $templatePath = BACKEND_PATH . '/modules/' . $module . '/layout/widgets/' . $widgetName . '.tpl';

						// build item
						$item = array('template' => $templatePath, 'module' => $module, 'widget' => $widgetName, 'title' => $title, 'hidden' => $hidden);

						// add on new position if no position is set or if the position is already used
						if($position === null || isset($this->widgets[$column][$position])) $this->widgets[$column][] = $item;

						// add on requested position
						else $this->widgets[$column][$position] = $item;
					}
				}
			}
		}

		// sort the widgets
		foreach($this->widgets as &$column) ksort($column);
	}

	/**
	 * Parse the page with its widgets.
	 */
	protected function parse()
	{
		parent::parse();

		// show report
		if($this->getParameter('password_reset') == 'success')
		{
			$this->tpl->assign('reportMessage', BL::msg('PasswordResetSuccess', 'core'));
			$this->tpl->assign('report', true);
		}

		// assign
		$this->tpl->assign('leftColumn', $this->widgets['left']);
		$this->tpl->assign('middleColumn', $this->widgets['middle']);
		$this->tpl->assign('rightColumn', $this->widgets['right']);
	}
}
