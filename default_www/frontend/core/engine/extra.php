<?php

/**
 * Fork
 *
 * This source file is part of Fork CMS.
 *
 * @package		frontend
 * @subpackage	extra
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendExtra extends FrontendBaseObject
{
	/**
	 * The parameters
	 *
	 * @var	mixed
	 */
	private $parameters;


	/**
	 * The proposed action
	 *
	 * @var	mixed
	 */
	private $proposedAction;


	/**
	 * Default constructor
	 *
	 * @return	void
	 */
	public function __construct($proposedAction, $moduleName, $parameters)
	{
		// call parent
		parent::__construct();

		// define some extra paths
		define('FRONTEND_CURRENT_MODULE', strtolower($moduleName));
		define('FRONTEND_MODULE_PATH', FRONTEND_MODULES_PATH .'/'. FRONTEND_CURRENT_MODULE);

		// set properties
		$this->proposedAction = $proposedAction;
		$this->parameters = $parameters;
	}


	/**
	 * Parse the extra into the template
	 *
	 * @return	void
	 */
	public function parse()
	{
		// require class
		require_once FRONTEND_MODULE_PATH .'/config.php';

		// create class name
		$className = ucfirst(FRONTEND_CURRENT_MODULE) .'Config';

		// create new config-instance
		$config = new $className;

		// set possible actions
		$config->setPossibleActions();

		// get the action filename
		$actionFileName = $config->getActionFileName($this->proposedAction, $className);

		// get the real action
		$actionName = $config->getActionName($this->proposedAction, $className);

		// build action path
		$actionPath = FRONTEND_MODULE_PATH .'/actions/'. $actionFileName;

		// require the action
		require_once $actionPath;

		// create actionclass name
		$actionName = FRONTEND_CURRENT_MODULE . $actionName;

		// crete instance
		$action = new $actionName();

		// call display
		$action->display();
	}
}

?>