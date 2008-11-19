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
		define('FRONTEND_MODULE', strtolower($moduleName));
		define('FRONTEND_MODULE_PATH', FRONTEND_MODULES_PATH .'/'. FRONTEND_MODULE);

		// set properties
		$this->proposedAction = $proposedAction;
		$this->parameters = $parameters;
	}


	/**
	 * Get the parameters
	 *
	 * @return	mixed
	 */
	public function getParameters()
	{
		return $this->parameters;
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
		$className = ucfirst(FRONTEND_MODULE) .'Config';

		// create new config-instance
		$config = new $className;

		// set possible actions
		$config->setPossibleActions();

		// get the action filename
		$actionFileName = $config->getActionFileName($this->proposedAction, $className);

		// get the real action
		$actionName = $config->getActionName($this->proposedAction, $className);

		// make available as constant
		define('FRONTEND_ACTION', strtolower($actionName));

		// build action path
		$actionPath = FRONTEND_MODULE_PATH .'/actions/'. $actionFileName;

		// require the action
		require_once $actionPath;

		// create actionclass name
		$actionName = FRONTEND_MODULE . $actionName;

		// crete instance
		$action = new $actionName();

		// call display
		$action->display();
	}
}

?>