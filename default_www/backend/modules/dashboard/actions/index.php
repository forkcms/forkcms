<?php

/**
 * BackendDashboardIndex
 * This is the index-action (default), it will display the login screen
 *
 * @package		backend
 * @subpackage	dashboard
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
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
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load data
		$this->loadData();

		// parse
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the data
	 *
	 * @return	void
	 */
	private function loadData()
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
				$pathName = BACKEND_MODULES_PATH .'/'. $module;

				// check if the folder exists
				if(SpoonDirectory::exists($pathName .'/widgets'))
				{
					// get widgets
					$widgets = (array) SpoonFile::getList($pathName .'/widgets', '/(.*)\.php/i');

					// loop widgets
					foreach($widgets as $widget)
					{
						// require the class
						require_once $pathName .'/widgets/'. $widget;

						// build classname
						$className = 'Backend'. SpoonFilter::toCamelCase($module) .'Widget'. SpoonFilter::toCamelCase(str_replace('.php', '', $widget));

						// validate if the class exists
						if(!class_exists($className)) throw new BackendException('The widgetfile is present, but the classname should be: '. $className .'.');

						// check if model file exists
						if(SpoonFile::exists($pathName .'/engine/model.php'))
						{
							// require model
							require_once $pathName .'/engine/model.php';
						}

						// create instance
						$instance = new $className();

						// has rights
						if(!$instance->isAllowed()) continue;

						// execute instance
						$instance->execute();

						// add to correct column and position
						$column = $instance->getColumn();
						$position = $instance->getPosition();
						$templatePath = $instance->getTemplatePath();

						// reset template path
						if($templatePath == null) $templatePath = BACKEND_PATH .'/modules/'. $module .'/layout/widgets/'. str_replace('.php', '.tpl', $widget);

						// add on new position
						if($position === null) $this->widgets[$column][] = array('template' => $templatePath);

						// add on requested position
						else $this->widgets[$column][$position] = array('template' => $templatePath);
					}
				}
			}
		}
	}


	/**
	 * Parse the page with its widgets.
	 *
	 * @return	void
	 */
	private function parse()
	{
		// show report
		if($this->getParameter('password_reset') == 'success')
		{
			$this->tpl->assign('reportMessage', BL::getMessage('PasswordResetSuccess', 'core'));
			$this->tpl->assign('report', true);
		}

		// assign
		$this->tpl->assign('leftColumn', $this->widgets['left']);
		$this->tpl->assign('middleColumn', $this->widgets['middle']);
		$this->tpl->assign('rightColumn', $this->widgets['right']);
	}
}

?>