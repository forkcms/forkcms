<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class will handle cronjob related stuff
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendCronjob
{
	/**
	 * The action
	 *
	 * @var	string
	 */
	private $action;

	/**
	 * The id
	 *
	 * @var	int
	 */
	private $id;

	/**
	 * The working language
	 *
	 * @var	string
	 */
	private $language;

	/**
	 * The module
	 *
	 * @var	string
	 */
	private $module;

	public function __construct()
	{
		// because some cronjobs will be run on the command line we should pass parameters
		if(isset($_SERVER['argv']))
		{
			// init var
			$first = true;

			// loop all passes arguments
			foreach($_SERVER['argv'] as $parameter)
			{
				// ignore first, because this is the scripts name.
				if($first)
				{
					// reset
					$first = false;

					// skip
					continue;
				}

				// split into chunks
				$chunks = explode('=', $parameter, 2);

				// valid paramters?
				if(count($chunks) == 2)
				{
					// build key and value
					$key = trim($chunks[0], '--');
					$value = $chunks[1];

					// set in GET
					if($key != '' && $value != '') $_GET[$key] = $value;
				}
			}
		}

		// define the Named Application
		if(!defined('NAMED_APPLICATION')) define('NAMED_APPLICATION', 'backend');

		// set the module
		$this->setModule(SpoonFilter::getGetValue('module', null, ''));

		// set the requested file
		$this->setAction(SpoonFilter::getGetValue('action', null, ''));

		// set the language
		$this->setLanguage(SpoonFilter::getGetValue('language', FrontendLanguage::getActiveLanguages(), SITE_DEFAULT_LANGUAGE));

		// mark cronjob as run
		$cronjobs = (array) BackendModel::getModuleSetting('core', 'cronjobs');
		$cronjobs[] = $this->getModule() . '.' . $this->getAction();
		BackendModel::setModuleSetting('core', 'cronjobs', array_unique($cronjobs));

		// create new action
		$action = new BackendCronjobAction($this->getAction(), $this->getModule());
		$action->execute();
	}

	/**
	 * Get the action
	 *
	 * @return string
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * Get language
	 *
	 * @return string
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * Get module
	 *
	 * @return string
	 */
	public function getModule()
	{
		return $this->module;
	}

	/**
	 * Set action
	 *
	 * @param string $value The action to load.
	 */
	public function setAction($value)
	{
		$value = preg_replace('/([^a-zA-Z0-9_])/', '', (string) $value);

		// validate
		if($value == '')
		{
			// set correct headers
			SpoonHTTP::setHeadersByCode(400);

			// throw exceptions
			throw new BackendException('Action not present.');
		}

		// set property
		$this->action = (string) $value;
	}

	/**
	 * Set language
	 *
	 * @param string $value The language to load.
	 */
	public function setLanguage($value)
	{
		// get the possible languages
		$possibleLanguages = BackendLanguage::getWorkingLanguages();

		// validate
		if(!in_array($value, array_keys($possibleLanguages))) throw new BackendException('Invalid language.');

		// set property
		$this->language = $value;

		// set the locale (we need this for the labels)
		BackendLanguage::setLocale($this->language);

		// set working language
		BackendLanguage::setWorkingLanguage($this->language);
	}

	/**
	 * Set module
	 *
	 * @param string $value The module to use.
	 */
	public function setModule($value)
	{
		$value = preg_replace('/([^a-zA-Z0-9_])/', '', (string) $value);

		// validate
		if($value == '')
		{
			// set correct headers
			SpoonHTTP::setHeadersByCode(400);

			// throw exceptions
			throw new BackendException('Module not present.');
		}

		// set property
		$this->module = $value;
	}
}

/**
 * This class is the real code, it creates an action, ...
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendCronjobAction
{
	/**
	 * The current action
	 *
	 * @var	string
	 */
	private $action;

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
	}

	/**
	 * Execute the action
	 * We will build the classname, require the class and call the execute method.
	 */
	public function execute()
	{
		// build action-class-name
		$actionClassName = 'Backend' . SpoonFilter::toCamelCase($this->getModule() . '_cronjob_' . $this->getAction());

		if($this->getModule() == 'core')
		{
			// check if the file is present? If it isn't present there is a huge problem, so we will stop our code by throwing an error
			if(!SpoonFile::exists(BACKEND_CORE_PATH . '/cronjobs/' . $this->getAction() . '.php'))
			{
				// set correct headers
				SpoonHTTP::setHeadersByCode(500);

				// throw exception
				throw new BackendException('The cronjobfile for the module (' . $this->getAction() . '.php) can\'t be found.');
			}

			// require the config file, we know it is there because we validated it before (possible actions are defined by existance of the file).
			require_once BACKEND_CORE_PATH . '/cronjobs/' . $this->getAction() . '.php';
		}

		else
		{
			// check if the file is present? If it isn't present there is a huge problem, so we will stop our code by throwing an error
			if(!SpoonFile::exists(BACKEND_MODULES_PATH . '/' . $this->getModule() . '/cronjobs/' . $this->getAction() . '.php'))
			{
				// set correct headers
				SpoonHTTP::setHeadersByCode(500);

				// throw exception
				throw new BackendException('The cronjobfile for the module (' . $this->getAction() . '.php) can\'t be found.');
			}

			// require the config file, we know it is there because we validated it before (possible actions are defined by existance of the file).
			require_once BACKEND_MODULES_PATH . '/' . $this->getModule() . '/cronjobs/' . $this->getAction() . '.php';
		}

		// validate if class exists (aka has correct name)
		if(!class_exists($actionClassName))
		{
			// set correct headers
			SpoonHTTP::setHeadersByCode(500);

			// throw exception
			throw new BackendException('The cronjobfile is present, but the classname should be: ' . $actionClassName . '.');
		}

		// create action-object
		$object = new $actionClassName($this->getAction(), $this->getModule());
		$object->execute();
	}

	/**
	 * Get the current action
	 * REMARK: You should not use this method from your code, but it has to be public so we can
	 * access it later on in the core-code
	 *
	 * @return string
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * Get the current module
	 * REMARK: You should not use this method from your code, but it has to be public so we can
	 * access it later on in the core-code
	 *
	 * @return string
	 */
	public function getModule()
	{
		return $this->module;
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
