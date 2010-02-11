<?php

/**
 * BackendWidgetAnalyse
 *
 * @package		backend
 * @subpackage	settings
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendWidgetAnalyse extends BackendBaseWidget
{
	/**
	 * Execute the widget
	 *
	 * @return	void
	 */
	public function execute()
	{
		// set column
		$this->setColumn('left');

		// set position
		$this->setPosition(0);

		// parse
		$this->parse();

		// display
		$this->display();
	}


	/**
	 * Parse into template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// init vars
		$warnings = array();
		$activeModules = BackendModel::getModules(true);

		// add warnings
		$warnings = array_merge($warnings, BackendModel::checkSettings());

		// loop active modules
		foreach($activeModules as $module)
		{
			// model class
			$class = 'Backend'. ucfirst($module) .'Model';

			// model file exists
			if(SpoonFile::exists(BACKEND_MODULES_PATH .'/'. $module .'/engine/model.php'))
			{
				// require class
				require_once BACKEND_MODULES_PATH .'/'. $module .'/engine/model.php';
			}

			// method exists
			if(method_exists($class, 'checkSettings'))
			{
				// add possible warnings
				$warnings = array_merge($warnings, call_user_func(array($class, 'checkSettings')));
			}
		}

		// assign warnings
		$this->tpl->assign('warnings', $warnings);
	}
}

?>