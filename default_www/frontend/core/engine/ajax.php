<?php

/**
 * FrontendAJAX
 * This class will handle AJAX-related stuff
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class FrontendAJAX
{
	/**
	 * The action
	 *
	 * @var	string
	 */
	private $action;


	/**
	 * The language
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


	/**
	 * Default constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// set the module
		$this->setModule(SpoonFilter::getGetValue('module', null, ''));

		// set the action
		$this->setAction(SpoonFilter::getGetValue('action', null, ''));

		// set the language
		$this->setLanguage(SpoonFilter::getGetValue('language', null, ''));

		// create a new action
		$action = new FrontendAJAXAction($this->getAction(), $this->getModule());

		// attempt to execute this action
		try
		{
			// execute the action
			$action->execute();
		}

		// we should catch exceptions
		catch(Exception $e)
		{
			// if we are debugging, we obviously want to see the exception
			if(SPOON_DEBUG) throw $e;

			// create fake action
			$fakeAction = new FrontendBaseAJAXAction('', '');

			// output the exceptions-message as an error
			$fakeAction->output(FrontendBaseAJAXAction::ERROR, null, $e->getMessage());
		}
	}


	/**
	 * Get the loaded action
	 *
	 * @return	string
	 */
	public function getAction()
	{
		return $this->action;
	}


	/**
	 * Get the loaded module
	 *
	 * @return	string
	 */
	public function getModule()
	{
		return $this->module;
	}


	/**
	 * Set action
	 *
	 * @return	void
	 * @param	string $value	The action that should be executed.
	 */
	public function setAction($value)
	{
		$this->action = (string) $value;
	}


	/**
	 * Set the language
	 *
	 * @return	void
	 * @param	string $value	The (interface-)language, will be used to parse labels.
	 */
	public function setLanguage($value)
	{
		// get the possible languages
		$possibleLanguages = FrontendLanguage::getActiveLanguages();

		// validate
		if(!in_array($value, $possibleLanguages))
		{
			// only 1 active language?
			if(!SITE_MULTILANGUAGE && count($possibleLanguages) == 1) $this->language = array_shift($possibleLanguages);

			// multiple languages available but none selected
			else
			{
				// create fake action
				$fakeAction = new FrontendBaseAJAXAction('', '');

				// output error
				$fakeAction->output(FrontendBaseAJAXAction::BAD_REQUEST, null, 'Language not provided.');
			}
		}

		// language is valid: set property
		else $this->language = (string) $value;

		// define constant
		define('FRONTEND_LANGUAGE', $this->language);

		// set the locale (we need this for the labels)
		FrontendLanguage::setLocale($this->language);
	}


	/**
	 * Set module
	 *
	 * @return	void
	 * @param	string $value	The module, wherefore an action will be executed.
	 */
	public function setModule($value)
	{
		// set property
		$this->module = (string) $value;
	}
}


/**
 * FrontendAJAXAction
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class FrontendAJAXAction
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
	 * @var	FrontendBaseConfig
	 */
	private $config;


	/**
	 * The current module
	 *
	 * @var	string
	 */
	private $module;


	/**
	 * Default constructor.
	 * You have to specify the action and module so we know what to do with this instance.
	 *
	 * @return	void
	 * @param	string $action	The action that should be executed.
	 * @param	string $module	The module that wherein the action is available.
	 */
	public function __construct($action, $module)
	{
		// set properties
		$this->setModule($module);
		$this->setAction($action);

		// load the configfile for the required module
		$this->loadConfig();
	}


	/**
	 * Execute the action.
	 * We will build the classname, require the class and call the execute method.
	 *
	 * @return	void
	 */
	public function execute()
	{
		// build action-class-name
		$actionClassName = 'Frontend' . SpoonFilter::toCamelCase($this->getModule() . '_ajax_' . $this->getAction());

		// build the path (core is a special case)
		if($this->getModule() == 'core') $path = FRONTEND_PATH . '/core/ajax/' . $this->getAction() . '.php';
		else $path = FRONTEND_PATH . '/modules/' . $this->getModule() . '/ajax/' . $this->getAction() . '.php';

		// check if the config is present? If it isn't present there is a huge problem, so we will stop our code by throwing an error
		if(!SpoonFile::exists($path)) throw new FrontendException('The actionfile (' . $path . ') can\'t be found.');

		// require the ajax file, we know it is there because we validated it before (possible actions are defined by existance of the file).
		require_once $path;

		// validate if class exists
		if(!class_exists($actionClassName)) throw new FrontendException('The actionfile is present, but the classname should be: ' . $actionClassName . '.');

		// create action-object
		$object = new $actionClassName($this->getAction(), $this->getModule());

		// validate if the execute-method is callable
		if(!is_callable(array($object, 'execute'))) throw new FrontendException('The actionfile should contain a callable method "execute".');

		// call the execute method of the real action (defined in the module)
		call_user_func(array($object, 'execute'));
	}


	/**
	 * Get the current action.
	 * REMARK: You should not use this method from your code, but it has to be public so we can access it later on in the core-code.
	 *
	 * @return	string
	 */
	public function getAction()
	{
		return $this->action;
	}


	/**
	 * Get the current module.
	 * REMARK: You should not use this method from your code, but it has to be public so we can access it later on in the core-code.
	 *
	 * @return	string
	 */
	public function getModule()
	{
		return $this->module;
	}


	/**
	 * Load the config file for the requested module.
	 * In the config file we have to find disabled actions, the constructor will read the folder and set possible actions.
	 * Other configurations will also be stored in it.
	 *
	 * @return	void
	 */
	public function loadConfig()
	{
		// build path to the module and define it. This is a constant because we can use this in templates.
		$frontendModulePath = FRONTEND_MODULES_PATH . '/' . $this->getModule();

		// check if the config is present? If it isn't present there is a huge problem, so we will stop our code by throwing an error
		if(!SpoonFile::exists($frontendModulePath . '/config.php')) throw new FrontendException('The configfile for the module (' . $this->getModule() . ') can\'t be found.');

		// build config-object-name
		$configClassName = 'Frontend' . SpoonFilter::toCamelCase($this->getModule() . '_config');

		// require the config file, we validated before for existence.
		require_once $frontendModulePath . '/config.php';

		// validate if class exists (aka has correct name)
		if(!class_exists($configClassName)) throw new FrontendException('The config file is present, but the classname should be: ' . $configClassName . '.');

		// create config-object, the constructor will do some magic
		$this->config = new $configClassName($this->getModule());
	}


	/**
	 * Set the action
	 *
	 * @return	void
	 * @param	string $action	The action that should be executed.
	 */
	private function setAction($action)
	{
		$this->action = (string) $action;
	}


	/**
	 * Set the module
	 *
	 * @return	void
	 * @param	string $module	The module wherin the action is available.
	 */
	private function setModule($module)
	{
		$this->module = (string) $module;
	}
}

?>