<?php

/**
 * FrontendBaseBlock
 *
 * This class implements a lot of functionality that can be extended by a specific action
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendBaseBlock
{
	/**
	 * The current action
	 *
	 * @var	string
	 */
	protected $action;


	/**
	 * The data
	 *
	 * @var	mixed
	 */
	protected $data;


	/**
	 * The parameters (urldecoded)
	 *
	 * @var	array
	 */
	protected $parameters = array();


	/**
	 * The header object
	 *
	 * @var	FrontendHeader
	 */
	protected $header;


	/**
	 * The current module
	 *
	 * @var	string
	 */
	protected $module;


	/**
	 * A reference to the current template
	 *
	 * @var	FrontendTemplate
	 */
	public $tpl;


	/**
	 * A reference to the url-instance
	 *
	 * @var	FrontendURL
	 */
	public $url;


	/**
	 * Should the current template be replaced with the blocks one?
	 *
	 * @var	bool
	 */
	private $overwrite;


	/**
	 * The path of the template to include, or that replaced the current one
	 *
	 * @var	string
	 */
	private $templatePath;


	/**
	 * Default constructor
	 * The constructor will set some properties. It populates the parameter array with urldecoded values for easy-use.
	 *
	 * @return	void
	 * @param	string $action
	 * @param	string $module
	 */
	public function __construct($module, $action, $data = null)
	{
		// get objects from the reference so they are accessable from the action-object
		$this->tpl = Spoon::getObjectReference('template');
		$this->header = Spoon::getObjectReference('header');

		$this->setModule($module);
		$this->setAction($action);
		$this->setData($data);
	}


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// build path to the module
		$frontendModulePath = FRONTEND_MODULES_PATH .'/'. $this->getModule();
		$frontendModuleUrl = '/frontend/modules/'. $this->getModule() .'/js';

		// add default js file (if the file exists)
		if(SpoonFile::exists($frontendModulePath .'/js/'. $this->getModule() .'.js')) $this->header->addJavascript($frontendModuleUrl .'/'. $this->getModule() .'.js', false, true);
		if(SpoonFile::exists($frontendModulePath .'/js/'. $this->getAction() .'.js')) $this->header->addJavascript($frontendModuleUrl .'/'. $this->getAction() .'.js', false, true);
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
	 * Get the module
	 *
	 * @return	string
	 */
	public function getModule()
	{
		return $this->module;
	}


	public function getOverwrite()
	{
		return $this->overwrite;
	}


	/**
	 * Get a parameter for a given key
	 * The function will return null if the key is not available
	 *
	 * By default we will cast the return value into a string, if you want something else specify it by passing the wanted type.
	 * Possible values are: bool, boolean, int, integer, float, double, string, array
	 *
	 * @return	mixed
	 * @param	string $key
	 * @param	string[optional] $type
	 */
	public function getParameter($key, $type = 'string')
	{
		// redefine key
		$key = (string) $key;

		// parameter exists
		if(isset($this->parameters[$key])) return SpoonFilter::getValue($this->parameters[$key], null, null, $type);

		// no such parammeter, fallback
		return null;
	}


	public function getTemplatePath()
	{
		return $this->templatePath;
	}


	/**
	 * Load the template
	 *
	 * @return	void
	 * @param	string[optional] $template
	 */
	protected function loadTemplate($template = null, $overwrite = false)
	{
		// redefine
		$overwrite = (bool) $overwrite;

		// no template given, so we should build the path
		if($template === null)
		{
			// build path to the module
			$frontendModulePath = FRONTEND_MODULES_PATH .'/'. $this->getModule();

			// build template path
			$template = $frontendModulePath .'/layout/templates/'. $this->getAction() .'.tpl';
		}

		// redefine
		else $template = (string) $template;

		// check if the file exists
		if(!SpoonFile::exists($template)) throw new FrontendException('The template ('. $template .') doesn\'t exists.');

		// set template
		$this->setOverwrite($overwrite);
		$this->setTemplatePath($template);
	}


	/**
	 * Redirect to a given url
	 *
	 * @return	void
	 * @param	string $url
	 */
	public function redirect($url)
	{
		SpoonHTTP::redirect((string) $url);
	}


	/**
	 * Set the action, for later use
	 *
	 * @return	void
	 * @param	string $action
	 */
	private function setAction($action)
	{
		$this->action = (string) $action;
	}


	/**
	 * Set the data, for later use
	 *
	 * @return	void
	 * @param	string $data
	 */
	private function setData($data = null)
	{
		// data given?
		if($data !== null)
		{
			// unserialize data
			$data = unserialize($data);

			// store
			$this->data = $data;
		}
	}


	/**
	 * Set the module, for later use
	 *
	 * @return	void
	 * @param	string $module
	 */
	private function setModule($module)
	{
		$this->module = (string) $module;
	}


	/**
	 * Set overwrite mode
	 *
	 * @return	void
	 * @param	bool $overwrite
	 */
	private function setOverwrite($overwrite)
	{
		$this->overwrite = (bool) $overwrite;
	}


	/**
	 * Set the path for the template to include or to replace the current one
	 *
	 * @return	void
	 * @param	string $path
	 */
	private function setTemplatePath($path)
	{
		$this->templatePath = (string) $path;
	}
}


/**
 * FrontendBaseWidget
 *
 * This class implements a lot of functionality that can be extended by a specific action
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendBaseWidget
{
	/**
	 * The current action
	 *
	 * @var	string
	 */
	protected $action;


	/**
	 * The data
	 *
	 * @var	mixed
	 */
	protected $data;


	/**
	 * The parameters (urldecoded)
	 *
	 * @var	array
	 */
	protected $parameters = array();


	/**
	 * The header object
	 *
	 * @var	FrontendHeader
	 */
	protected $header;


	/**
	 * The current module
	 *
	 * @var	string
	 */
	protected $module;


	/**
	 * A reference to the current template
	 *
	 * @var	FrontendTemplate
	 */
	public $tpl;


	/**
	 * A reference to the url-instance
	 *
	 * @var	FrontendURL
	 */
	public $url;


	/**
	 * Default constructor
	 * The constructor will set some properties. It populates the parameter array with urldecoded values for easy-use.
	 *
	 * @return	void
	 * @param	string $action
	 * @param	string $module
	 */
	public function __construct($module, $action, $data = null)
	{
		// get objects from the reference so they are accessable from the action-object
		$this->tpl = Spoon::getObjectReference('template');
		$this->header = Spoon::getObjectReference('header');

		$this->setModule($module);
		$this->setAction($action);
		$this->setData($data);
	}


	/**
	 * Display, this wil output the template to the browser
	 * If no template is specified we build the path form the current module and action
	 *
	 * @return	void
	 * @param	string[optional] $template
	 */
	public function display($template)
	{
		// parse header
		$this->header->parse();

		// display
		$this->tpl->display($template);
	}


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// build path to the module
		$frontendModulePath = FRONTEND_MODULES_PATH .'/'. $this->getModule();

		// add default js file (if the file exists)
		if(SpoonFile::exists($frontendModulePath .'/js/'. $this->getAction() .'.js')) $this->header->addJavascript($this->getModule() .'.js', null, true);
		if(SpoonFile::exists($frontendModulePath .'/js/'. $this->getAction() .'.js')) $this->header->addJavascript($this->getAction() .'.js', null, true);
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
	 * Get the module
	 *
	 * @return	string
	 */
	public function getModule()
	{
		return $this->module;
	}


	/**
	 * Get a parameter for a given key
	 * The function will return null if the key is not available
	 *
	 * By default we will cast the return value into a string, if you want something else specify it by passing the wanted type.
	 * Possible values are: bool, boolean, int, integer, float, double, string, array
	 *
	 * @return	mixed
	 * @param	string $key
	 * @param	string[optional] $type
	 */
	public function getParameter($key, $type = 'string')
	{
		// redefine key
		$key = (string) $key;

		// parameter exists
		if(isset($this->parameters[$key])) return SpoonFilter::getValue($this->parameters[$key], null, null, $type);

		// no such parammeter, fallback
		return null;
	}


	public function getTemplatePath()
	{
		return $this->templatePath;
	}


	/**
	 * Load the template
	 *
	 * @return	void
	 * @param	string[optional] $template
	 */
	protected function loadTemplate($template = null, $overwrite = false)
	{
		// redefine
		$overwrite = (bool) $overwrite;

		// no template given, so we should build the path
		if($template === null)
		{
			// build path to the module
			$frontendModulePath = FRONTEND_MODULES_PATH .'/'. $this->getModule();

			// build template path
			$template = $frontendModulePath .'/layout/widgets/'. $this->getAction() .'.tpl';
		}

		// redefine
		else $template = (string) $template;

		// check if the file exists
		if(!SpoonFile::exists($template)) throw new FrontendException('The template ('. $template .') doesn\'t exists.');

		// set template
		$this->setTemplatePath($template);
	}


	/**
	 * Redirect to a given url
	 *
	 * @return	void
	 * @param	string $url
	 */
	public function redirect($url)
	{
		SpoonHTTP::redirect((string) $url);
	}


	/**
	 * Set the action, for later use
	 *
	 * @return	void
	 * @param	string $action
	 */
	private function setAction($action)
	{
		$this->action = (string) $action;
	}


	/**
	 * Set the data, for later use
	 *
	 * @return	void
	 * @param	string $data
	 */
	private function setData($data = null)
	{
		// data given?
		if($data !== null)
		{
			// unserialize data
			$data = unserialize($data);

			// store
			$this->data = $data;
		}
	}


	/**
	 * Set the module, for later use
	 *
	 * @return	void
	 * @param	string $module
	 */
	private function setModule($module)
	{
		$this->module = (string) $module;
	}


	/**
	 * Set the path for the template to include or to replace the current one
	 *
	 * @return	void
	 * @param	string $path
	 */
	private function setTemplatePath($path)
	{
		$this->templatePath = (string) $path;
	}
}

?>