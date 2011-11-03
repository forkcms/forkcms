<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class is the real code, it creates an action, loads the configfile, ...
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class BackendAJAXAction
{
	/**
	 * The current action
	 *
	 * @var	string
	 */
	private $action;

	/**
	 * The config file
	 *
	 * @var	BackendBaseConfig
	 */
	private $config;

	/**
	 * The current module
	 *
	 * @var	string
	 */
	private $module;

	/**
	 * You have to specify the action and module so we know what to do with this instance
	 *
	 * @param string $action The action to load.
	 * @param string $module The module to load.
	 */
	public function __construct($action, $module)
	{
		$this->setModule($module);
		$this->setAction($action);

		$this->loadConfig();
		$allowed = false;

		// is this an allowed action
		if(BackendAuthentication::isAllowedAction($action, $this->getModule())) $allowed = true;

		// is this an allowed AJAX-action?
		if(!$allowed)
		{
			// set correct headers
			SpoonHTTP::setHeadersByCode(403);

			// output
			$fakeAction = new BackendBaseAJAXAction('', '');
			$fakeAction->output(BackendBaseAJAXAction::FORBIDDEN, null, 'Not logged in.');
		}
	}

	/**
	 * Execute the action
	 * We will build the classname, require the class and call the execute method.
	 */
	public function execute()
	{
		// build action-class-name
		$actionClassName = 'Backend' . SpoonFilter::toCamelCase($this->getModule() . '_ajax_' . $this->getAction());

		// require the config file, we know it is there because we validated it before (possible actions are defined by existance of the file).
		require_once BACKEND_MODULE_PATH . '/ajax/' . $this->getAction() . '.php';

		// validate if class exists (aka has correct name)
		if(!class_exists($actionClassName)) throw new BackendException('The actionfile is present, but the classname should be: ' . $actionClassName . '.');

		// create action-object
		$object = new $actionClassName($this->getAction(), $this->getModule());
		$object->execute();
	}

	/**
	 * Get the current action
	 * REMARK: You should not use this method from your code, but it has to be public so we can access it later on in the core-code
	 *
	 * @return string
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * Get the current module
	 * REMARK: You should not use this method from your code, but it has to be public so we can access it later on in the core-code
	 *
	 * @return string
	 */
	public function getModule()
	{
		return $this->module;
	}

	/**
	 * Load the config file for the requested module.
	 * In the config file we have to find disabled actions, the constructor will read the folder
	 * and set possible actions. Other configurations will also be stored in it.
	 */
	public function loadConfig()
	{
		// check if module path is not yet defined
		if(!defined('BACKEND_MODULE_PATH'))
		{
			// build path for core
			if($this->getModule() == 'core') define('BACKEND_MODULE_PATH', BACKEND_PATH . '/' . $this->getModule());

			// build path to the module and define it. This is a constant because we can use this in templates.
			else define('BACKEND_MODULE_PATH', BACKEND_MODULES_PATH . '/' . $this->getModule());
		}

		// check if the config is present? If it isn't present there is a huge problem, so we will stop our code by throwing an error
		if(!SpoonFile::exists(BACKEND_MODULE_PATH . '/config.php')) throw new BackendException('The configfile for the module (' . $this->getModule() . ') can\'t be found.');

		// build config-object-name
		$configClassName = 'Backend' . SpoonFilter::toCamelCase($this->getModule() . '_config');

		// require the config file, we validated before for existence.
		require_once BACKEND_MODULE_PATH . '/config.php';

		// validate if class exists (aka has correct name)
		if(!class_exists($configClassName)) throw new BackendException('The config file is present, but the classname should be: ' . $configClassName . '.');

		// create config-object, the constructor will do some magic
		$this->config = new $configClassName($this->getModule());
	}

	/**
	 * Set the action
	 *
	 * @param string $action The action to load.
	 */
	private function setAction($action)
	{
		$this->action = (string) $action;
	}

	/**
	 * Set the module
	 *
	 * @param string $module The module to load.
	 */
	private function setModule($module)
	{
		$this->module = (string) $module;
	}
}
