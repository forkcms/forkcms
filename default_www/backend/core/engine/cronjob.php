<?php

/**
 * BackendCronjob
 * This class will handle cronjob related stuff
 *
 * @package		backend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
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


	/**
	 * Default constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// define the Named appliation
		if(!defined('NAMED_APPLICATION')) define('NAMED_APPLICATION', 'backend');

		// set the module
		$this->setModule(SpoonFilter::getGetValue('module', null, ''));

		// set the requested file
		$this->setAction(SpoonFilter::getGetValue('action', null, ''));

		// set the id
		$this->setId(SpoonFilter::getGetValue('id', null, ''));

		// set the language
		$this->setLanguage(SpoonFilter::getGetValue('language', FrontendLanguage::getActiveLanguages(), FrontendLanguage::DEFAULT_LANGUAGE));

		// create URL instance
		new BackendURL();

		// create new action
		$action = new BackendCronjobAction($this->getAction(), $this->getModule(), $this->getId());

		// execute
		$action->execute();
	}


	/**
	 * Get the action
	 *
	 * @return	string
	 */
	public function getAction()
	{
		return $this->action;
	}


	/**
	 * Get the id
	 *
	 * @return	int
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * Get language
	 *
	 * @return	string
	 */
	public function getLanguage()
	{
		return $this->language;
	}


	/**
	 * Get module
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
	 * @param	string $value		The action to load.
	 */
	public function setAction($value)
	{
		$this->action = (string) $value;
	}


	/**
	 * Set id
	 *
	 * @return	void
	 * @param	string $value	The id of the cronjob.
	 */
	public function setId($value)
	{
		$this->id = (int) $value;
	}


	/**
	 * Set language
	 *
	 * @return	void
	 * @param	string $value	The language to load.
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
	 * @return	void
	 * @param	string $value	The module to use.
	 */
	public function setModule($value)
	{
		// set property
		$this->module = (string) $value;
	}
}


/**
 * BackendCronjobAction
 * This class is the real code, it creates an action, ...
 *
 * @package		backend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
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
	 * The id of the cronjob
	 *
	 * @var	int
	 */
	private $id;


	/**
	 * The current module
	 *
	 * @var	string
	 */
	private $module;


	/**
	 * Default constructor
	 * You have to specify the action and module so we know what to do with this instance
	 *
	 * @return	void
	 * @param	string $action		The action to load.
	 * @param	string $module		The module to load.
	 * @param	int $id				The id of the cronjob.
	 */
	public function __construct($action, $module, $id)
	{
		// set properties
		$this->setModule($module);
		$this->setAction($action);
		$this->setId($id);
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
		$actionClassName = 'Backend'. SpoonFilter::toCamelCase($this->getModule() .'_cronjob_'. $this->getAction());

		if($this->getModule() == 'core')
		{
			// require the config file, we know it is there because we validated it before (possible actions are defined by existance of the file).
			require_once BACKEND_CORE_PATH .'/cronjobs/'. $this->getAction() .'.php';
		}

		else
		{
			// require the config file, we know it is there because we validated it before (possible actions are defined by existance of the file).
			require_once BACKEND_MODULES_PATH .'/'. $this->getModule() .'/cronjobs/'. $this->getAction() .'.php';
		}

		// validate if class exists (aka has correct name)
		if(!class_exists($actionClassName)) throw new BackendException('The actionfile is present, but the classname should be: '. $actionClassName .'.');

		// create action-object
		$object = new $actionClassName($this->getAction(), $this->getModule(), $this->getId());

		// call the execute method of the real action (defined in the module)
		$object->execute();
	}


	/**
	 * Get the current action
	 * REMARK: You should not use this method from your code, but it has to be public so we can access it later on in the core-code
	 *
	 * @return	string
	 */
	public function getAction()
	{
		return $this->action;
	}


	/**
	 * Get the id of the cronjob
	 * REMARK: You should not use this method from your code, but it has to be public so we can access it later on in the core-code
	 *
	 * @return	int
	 */
	public function getId()
	{
		return $this->id;
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
	 * Set the action
	 *
	 * @return	void
	 * @param	string $action	The action to load.
	 */
	private function setAction($action)
	{
		$this->action = (string) $action;
	}


	/**
	 * Set the id
	 *
	 * @return	void
	 * @param	int $id		The id of the cronjob.
	 */
	private function setId($id)
	{
		$this->id = (int) $id;
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
}

?>