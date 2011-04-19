<?php

/**
 * This class will handle all stuff related to blocks
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@dieterve.be>
 * @since		2.0
 */
class FrontendBlockExtra extends FrontendBaseObject
{
	/**
	 * The current action
	 *
	 * @var string
	 */
	private $action;


	/**
	 * The configfile
	 *
	 * @var	FrontendBaseConfig
	 */
	private $config;


	/**
	 * The data that was passed by the extra
	 *
	 * @var	mixed
	 */
	private $data;


	/**
	 * The current module
	 *
	 * @var	string
	 */
	private $module;


	/**
	 * Should the template overwrite the current one
	 *
	 * @var	bool
	 */
	protected $overwrite = false;


	/**
	 * The path for the template
	 *
	 * @var	string
	 */
	protected $templatePath = '';


	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param	string $module			The module to load.
	 * @param	string $action			The action to load.
	 * @param	mixed[optional] $data	The data that was passed from the database.
	 */
	public function __construct($module, $action, $data = null)
	{
		// call the parent
		parent::__construct();

		// set properties
		$this->setModule($module);
		$this->setAction($action);
		if($data !== null) $this->setData($data);

		// load the configfile for the required module
		$this->loadConfig();

		// is the requested action possible? If not we throw an exception. We don't redirect because that could trigger a redirect loop
		if(!in_array($this->getAction(), $this->config->getPossibleActions())) $this->setAction($this->config->getDefaultAction());
	}


	/**
	 * Execute the action
	 * We will build the classname, require the class and call the execute method.
	 *
	 * @return	void
	 */
	public function execute()
	{
		// build action-class-name
		$actionClassName = 'Frontend' . SpoonFilter::toCamelCase($this->getModule() . '_' . $this->getAction());

		// require the config file, we know it is there because we validated it before (possible actions are defined by existance off the file).
		require_once FRONTEND_MODULES_PATH . '/' . $this->getModule() . '/actions/' . $this->getAction() . '.php';

		// validate if class exists (aka has correct name)
		if(!class_exists($actionClassName)) throw new FrontendException('The actionfile is present, but the classname should be: ' . $actionClassName . '.');

		// create action-object
		$object = new $actionClassName($this->getModule(), $this->getAction(), $this->getData());

		// validate if the execute-method is callable
		if(!is_callable(array($object, 'execute'))) throw new FrontendException('The actionfile should contain a callable method "execute".');

		// call the execute method of the real action (defined in the module)
		$object->execute();

		// set some properties
		$this->setOverwrite($object->getOverwrite());
		if($object->getTemplatePath() !== null) $this->setTemplatePath($object->getTemplatePath());
	}


	/**
	 * Get the current action
	 * REMARK: You should not use this method from your code, but it has to be public so we can access it later on in the core-code
	 *
	 * @return	string
	 */
	public function getAction()
	{
		// no action specified?
		if($this->action === null)
		{
			// get first parameter
			$actionParameter = $this->URL->getParameter(0);

			// unknown action and not provided in URL
			if($actionParameter === null) $this->setAction($this->config->getDefaultAction());

			// action provided in the URL
			else
			{
				// loop possible actions
				foreach($this->config->getPossibleActions() as $actionName)
				{
					// get action that should be passed as parameter
					$actionURL = FL::act(SpoonFilter::toCamelCase($actionName));

					// the action is the requested one
					if($actionURL == $actionParameter)
					{
						// set action
						$this->setAction($actionName);

						// stop the loop
						break;
					}
				}
			}
		}

		// return
		return $this->action;
	}


	/**
	 * Get the data
	 *
	 * @return	mixed
	 */
	public function getData()
	{
		return $this->data;
	}


	/**
	 * Get the current module
	 * REMARK: You should not use this method from your code, but it has to be public so we can access it later on in the core-code
	 *
	 * @return	string
	 */
	public function getModule()
	{
		return $this->module;
	}


	/**
	 * Get overwrite mode
	 *
	 * @return	bool
	 */
	public function getOverwrite()
	{
		return $this->overwrite;
	}


	/**
	 * Get path for the template
	 *
	 * @return	string
	 */
	public function getTemplatePath()
	{
		return $this->templatePath;
	}


	/**
	 * Load the config file for the requested block.
	 * In the config file we have to find dissabled actions, the constructor will read the folder and set possible actions
	 * Other configurations will be stored in it also.
	 *
	 * @return	void
	 */
	public function loadConfig()
	{
		// build path to the module
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
	 * @param	string[optional] $action	The action to load.
	 */
	private function setAction($action = null)
	{
		if($action !== null) $this->action = (string) $action;
	}


	/**
	 * Set the data
	 *
	 * @return	void
	 * @param	mixed $data		The data that should be set.
	 */
	private function setData($data)
	{
		$this->data = $data;
	}


	/**
	 * Set the module
	 *
	 * @return	void
	 * @param	string $module	The module to load.
	 */
	private function setModule($module)
	{
		$this->module = (string) $module;
	}


	/**
	 * Set overwrite mode
	 *
	 * @return	void
	 * @param	bool $overwrite		Should the template overwrite the already loaded template.
	 */
	private function setOverwrite($overwrite)
	{
		$this->overwrite = (bool) $overwrite;
	}


	/**
	 * Set the path for the template
	 *
	 * @return	void
	 * @param	string $path	The path to set.
	 */
	private function setTemplatePath($path)
	{
		$this->templatePath = FrontendTheme::getPath($path);
	}
}


/**
 * FrontendBlockWidget
 * This class will handle all stuff related to widgets
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@dieterve.be>
 * @since		2.0
 */
class FrontendBlockWidget extends FrontendBaseObject
{
	/**
	 * The current action
	 *
	 * @var string
	 */
	private $action;


	/**
	 * The configfile
	 *
	 * @var	FrontendBaseConfig
	 */
	private $config;


	/**
	 * The data that was passed by the extra
	 *
	 * @var	mixed
	 */
	private $data;


	/**
	 * The current moduled
	 *
	 * @var	string
	 */
	private $module;


	/**
	 * The path for the template
	 *
	 * @var	string
	 */
	protected $templatePath = '';


	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param	string $module			The module to load.
	 * @param	string $action			The action to load.
	 * @param	mixed[optional] $data	The data that was passed from the database.
	 */
	public function __construct($module, $action, $data = null)
	{
		// call the parent
		parent::__construct();

		// set properties
		$this->setModule($module);
		$this->setAction($action);
		if($data !== null) $this->setData($data);

		// load the configfile for the required module
		$this->loadConfig();
	}


	/**
	 * Execute the action
	 * We will build the classname, require the class and call the execute method.
	 *
	 * @return	void
	 */
	public function execute()
	{
		// build action-class-name
		$actionClassName = 'Frontend' . SpoonFilter::toCamelCase($this->getModule() . '_widget_' . $this->getAction());

		// build path to the module
		$frontendModulePath = FRONTEND_MODULES_PATH . '/' . $this->getModule();

		// require the config file, we know it is there because we validated it before (possible actions are defined by existance off the file).
		require_once $frontendModulePath . '/widgets/' . $this->getAction() . '.php';

		// validate if class exists (aka has correct name)
		if(!class_exists($actionClassName)) throw new FrontendException('The actionfile is present, but the classname should be: ' . $actionClassName . '.');

		// create action-object
		$object = new $actionClassName($this->getModule(), $this->getAction(), $this->getData());

		// validate if the execute-method is callable
		if(!is_callable(array($object, 'execute'))) throw new FrontendException('The actionfile should contain a callable method "execute".');

		// call the execute method of the real action (defined in the module)
		$output = $object->execute();

		// set path to template if the widget didnt return any data
		if($output === null) $this->setTemplatePath($object->getTemplatePath());

		// return possible output
		return $output;
	}


	/**
	 * Get the current action
	 * REMARK: You should not use this method from your code, but it has to be public so we can access it later on in the core-code
	 *
	 * @return	string
	 */
	public function getAction()
	{
		// no action specified?
		if($this->action === null) $this->setAction($this->config->getDefaultAction());

		// return action
		return $this->action;
	}


	/**
	 * Get the data
	 *
	 * @return	mixed
	 */
	public function getData()
	{
		return $this->data;
	}


	/**
	 * Get the current module
	 * REMARK: You should not use this method from your code, but it has to be public so we can access it later on in the core-code
	 *
	 * @return	string
	 */
	public function getModule()
	{
		return $this->module;
	}


	/**
	 * Get path for the template
	 *
	 * @return	string
	 */
	public function getTemplatePath()
	{
		return $this->templatePath;
	}


	/**
	 * Load the config file for the requested block.
	 * In the config file we have to find dissabled actions, the constructor will read the folder and set possible actions
	 * Other configurations will be stored in it also.
	 *
	 * @return	void
	 */
	public function loadConfig()
	{
		// build path to the module
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
	 * @param	string[optional] $action		The action to load.
	 */
	private function setAction($action = null)
	{
		if($action !== null) $this->action = (string) $action;
	}


	/**
	 * Set the data
	 *
	 * @return	void
	 * @param	mixed $data		The data that should be set.
	 */
	private function setData($data)
	{
		$this->data = $data;
	}


	/**
	 * Set the module
	 *
	 * @return	void
	 * @param	string $module		The module to load.
	 */
	private function setModule($module)
	{
		$this->module = (string) $module;
	}


	/**
	 * Set the path for the template
	 *
	 * @return	void
	 * @param	string $path		The path for the template to use.
	 */
	private function setTemplatePath($path)
	{
		$this->templatePath = FrontendTheme::getPath($path);
	}
}

?>