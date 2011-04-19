<?php

/**
 * This class will be the base of the objects used in onsite
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendBaseObject
{
	/**
	 * Template instance
	 *
	 * @var	FrontendTemplate
	 */
	protected $tpl;


	/**
	 * URL instance
	 *
	 * @var	FrontendURL
	 */
	protected $URL;


	/**
	 * Default constructor.
	 * It will grab stuff from the reference.
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// get template from reference
		$this->tpl = Spoon::get('template');

		// get URL from reference
		$this->URL = Spoon::get('url');
	}
}


/**
 * This is the base-object for config-files. The module-specific config-files can extend the functionality from this class.
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendBaseConfig
{
	/**
	 * The default action
	 *
	 * @var	string
	 */
	protected $defaultAction = 'index';


	/**
	 * The disabled actions
	 *
	 * @var	array
	 */
	protected $disabledActions = array();


	/**
	 * The disabled AJAX-actions
	 *
	 * @var	array
	 */
	protected $disabledAJAXActions = array();


	/**
	 * The current loaded module
	 *
	 * @var	string
	 */
	protected $module;


	/**
	 * All the possible actions
	 *
	 * @var	array
	 */
	protected $possibleActions = array();


	/**
	 * All the possible AJAX actions
	 *
	 * @var	array
	 */
	protected $possibleAJAXActions = array();


	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param	string $module	The module wherefor this is the configuration-file.
	 */
	public function __construct($module)
	{
		// set module
		$this->module = (string) $module;

		// check if model exists
		if(SpoonFile::exists(FRONTEND_MODULES_PATH . '/' . $this->getModule() . '/engine/model.php'))
		{
			// the model exists, so we require it
			require_once FRONTEND_MODULES_PATH . '/' . $this->getModule() . '/engine/model.php';
		}

		// read the possible actions based on the files
		$this->setPossibleActions();
	}


	/**
	 * Get the default action
	 *
	 * @return	string
	 */
	public function getDefaultAction()
	{
		return $this->defaultAction;
	}


	/**
	 * Get the current loaded module
	 *
	 * @return	string
	 */
	public function getModule()
	{
		return $this->module;
	}


	/**
	 * Get the possible actions
	 *
	 * @return	array
	 */
	public function getPossibleActions()
	{
		return $this->possibleActions;
	}


	/**
	 * Get the possible AJAX actions
	 *
	 * @return	array
	 */
	public function getPossibleAJAXActions()
	{
		return $this->possibleAJAXActions;
	}


	/**
	 * Set the possible actions, based on files in folder.
	 * You can disable action in the config file. (Populate $disabledActions)
	 *
	 * @return	void
	 */
	protected function setPossibleActions()
	{
		// build path to the module
		$frontendModulePath = FRONTEND_MODULES_PATH . '/' . $this->getModule();

		// get filelist (only those with .php-extension)
		$actionFiles = (array) SpoonFile::getList($frontendModulePath . '/actions', '/(.*).php/');

		// loop filelist
		foreach($actionFiles as $file)
		{
			// get action by removing the extension, actions should not contain spaces (use _ instead)
			$action = strtolower(str_replace('.php', '', $file));

			// if the action isn't disabled add it to the possible actions
			if(!in_array($action, $this->disabledActions)) $this->possibleActions[$file] = $action;
		}

		// get filelist (only those with .php-extension)
		$AJAXActionFiles = (array) SpoonFile::getList($frontendModulePath . '/ajax', '/(.*).php/');

		// loop filelist
		foreach($AJAXActionFiles as $file)
		{
			// get action by removing the extension, actions should not contain spaces (use _ instead)
			$action = strtolower(str_replace('.php', '', $file));

			// if the action isn't disabled add it to the possible actions
			if(!in_array($action, $this->disabledAJAXActions)) $this->possibleAJAXActions[$file] = $action;
		}
	}
}


/**
 * This class implements a lot of functionality that can be extended by a specific block
 * @later Check which methods are the same in FrontendBaseWidget, maybe we should extend from a general class
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@dieterve.be>
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
	 * The breadcrumb object
	 *
	 * @var	FrontendBreadcrumb
	 */
	protected $breadcrumb;


	/**
	 * The data
	 *
	 * @var	mixed
	 */
	protected $data;


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
	 * Should the current template be replaced with the blocks one?
	 *
	 * @var	bool
	 */
	private $overwrite;


	/**
	 * Pagination array
	 *
	 * @var	array
	 */
	protected $pagination;


	/**
	 * A reference to the current template
	 *
	 * @var	FrontendTemplate
	 */
	public $tpl;


	/**
	 * The path of the template to include, or that replaced the current one
	 *
	 * @var	string
	 */
	private $templatePath;


	/**
	 * A reference to the URL-instance
	 *
	 * @var	FrontendURL
	 */
	public $URL;


	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param	string $module				The name of the module.
	 * @param	string $action				The name of the action.
	 * @param	string[optional] $data		The data that should be available in this block.
	 */
	public function __construct($module, $action, $data = null)
	{
		// get objects from the reference so they are accessable
		$this->tpl = Spoon::get('template');
		$this->header = Spoon::get('header');
		$this->URL = Spoon::get('url');
		$this->breadcrumb = Spoon::get('breadcrumb');

		// set properties
		$this->setModule($module);
		$this->setAction($action);
		$this->setData($data);
	}


	/**
	 * Add a CSS file into the array
	 *
	 * @return	void
	 * @param 	string $file					The path for the CSS-file that should be loaded.
	 * @param	bool[optional] $overwritePath	Whether or not to add the module to this path. Module path is added by default.
	 * @param	bool[optional] $minify			Should the CSS be minified?
	 * @param	bool[optional] $addTimestamp	May we add a timestamp for caching purposes?
	 */
	public function addCSS($file, $overwritePath = false, $minify = true, $addTimestamp = null)
	{
		// redefine
		$file = (string) $file;
		$overwritePath = (bool) $overwritePath;

		// use module path
		if(!$overwritePath) $file = '/frontend/modules/' . $this->getModule() . '/layout/css/' . $file;

		// add css to the header
		$this->header->addCSS($file, $minify, $addTimestamp);
	}


	/**
	 * Add a javascript file into the array
	 *
	 * @deprecated	Deprecated since version 2.2.0. Will be removed in the next version.
	 *
	 * @return	void
	 * @param 	string $file						The path to the javascript-file that should be loaded.
	 * @param 	bool[optional] $overwritePath		Whether or not to add the module to this path. Module path is added by default.
	 * @param	bool[optional] $minify				Should the file be minified?
	 * @param	bool[optional] $parseThroughPHP		Should the file be parsed through PHP?
	 * @param	bool[optional] $addTimestamp		May we add a timestamp for caching purposes?
	 */
	public function addJavascript($file, $overwritePath = false, $minify = true, $parseThroughPHP = false, $addTimestamp = null)
	{
		$this->addJS($file, $overwritePath, $minify, $parseThroughPHP, $addTimestamp);
	}


	/**
	 * Add a javascript file into the array
	 *
	 * @return	void
	 * @param 	string $file						The path to the javascript-file that should be loaded.
	 * @param 	bool[optional] $overwritePath		Whether or not to add the module to this path. Module path is added by default.
	 * @param	bool[optional] $minify				Should the file be minified?
	 * @param	bool[optional] $parseThroughPHP		Should the file be parsed through PHP?
	 * @param	bool[optional] $addTimestamp		May we add a timestamp for caching purposes?
	 */
	public function addJS($file, $overwritePath = false, $minify = true, $parseThroughPHP = false, $addTimestamp = null)
	{
		// redefine
		$file = (string) $file;
		$overwritePath = (bool) $overwritePath;

		// use module path
		if(!$overwritePath) $file = '/frontend/modules/' . $this->getModule() . '/js/' . $file;

		// add js to the header
		$this->header->addJS($file, $minify, $parseThroughPHP, $addTimestamp);
	}


	/**
	 * Execute the action
	 * If a javascript file with the name of the module or action exists it will be loaded.
	 *
	 * @return	void
	 */
	public function execute()
	{
		// build path to the module
		$frontendModulePath = FRONTEND_MODULES_PATH . '/' . $this->getModule();

		// buil URL to the module
		$frontendModuleURL = '/frontend/modules/' . $this->getModule() . '/js';

		// add javascriptfile with same name as module (if the file exists)
		if(SpoonFile::exists($frontendModulePath . '/js/' . $this->getModule() . '.js')) $this->header->addJS($frontendModuleURL . '/' . $this->getModule() . '.js', false, true);

		// add javascriptfile with same name as the action (if the file exists)
		if(SpoonFile::exists($frontendModulePath . '/js/' . $this->getAction() . '.js')) $this->header->addJS($frontendModuleURL . '/' . $this->getAction() . '.js', false, true);
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
	 * Load the template
	 *
	 * @return	void
	 * @param	string[optional] $template		The path for the template to use.
	 * @param	bool[optional] $overwrite		Should the template overwrite the default?
	 */
	protected function loadTemplate($template = null, $overwrite = false)
	{
		// redefine
		$overwrite = (bool) $overwrite;

		// if no template is passed we should build the path
		if($template === null)
		{
			// build path to the module
			$frontendModulePath = FRONTEND_MODULES_PATH . '/' . $this->getModule();

			// build template path
			$template = $frontendModulePath . '/layout/templates/' . $this->getAction() . '.tpl';
		}

		// redefine
		else $template = (string) $template;

		// set properties
		$this->setOverwrite($overwrite);
		$this->setTemplatePath($template);
	}


	/**
	 * Parse pagination
	 *
	 * @return	void
	 */
	protected function parsePagination()
	{
		// init var
		$pagination = null;
		$showFirstPages = false;
		$showLastPages = false;
		$useQuestionMark = true;

		// validate pagination array
		if(!isset($this->pagination['limit'])) throw new FrontendException('no limit in the pagination-property.');
		if(!isset($this->pagination['offset'])) throw new FrontendException('no offset in the pagination-property.');
		if(!isset($this->pagination['requested_page'])) throw new FrontendException('no requested_page available in the pagination-property.');
		if(!isset($this->pagination['num_items'])) throw new FrontendException('no num_items available in the pagination-property.');
		if(!isset($this->pagination['num_pages'])) throw new FrontendException('no num_pages available in the pagination-property.');
		if(!isset($this->pagination['url'])) throw new FrontendException('no URL available in the pagination-property.');

		// should we use a questionmark or an ampersand
		if(mb_strpos($this->pagination['url'], '?') > 0) $useQuestionMark = false;

		// no pagination needed
		if($this->pagination['num_pages'] < 1) return;

		// populate count fields
		$pagination['num_pages'] = $this->pagination['num_pages'];
		$pagination['current_page'] = $this->pagination['requested_page'];

		// as long as we are below page 5 we should show all pages starting from 1
		if($this->pagination['requested_page'] <= 6)
		{
			// init vars
			$pagesStart = 1;
			$pagesEnd = ($this->pagination['num_pages'] >= 6) ? 7 : $this->pagination['num_pages'];

			// show last pages
			if($this->pagination['num_pages'] > 6) $showLastPages = true;
		}

		// as long as we are 5 pages from the end we should show all pages till the end
		elseif($this->pagination['requested_page'] >= ($this->pagination['num_pages'] - 4))
		{
			// init vars
			$pagesStart = ($this->pagination['num_pages'] - 5);
			$pagesEnd = $this->pagination['num_pages'];

			// show first pages
			if($this->pagination['num_pages'] > 5) $showFirstPages = true;
		}

		// page 7
		else
		{
			// init vars
			$pagesStart = $this->pagination['requested_page'] - 2;
			$pagesEnd = $this->pagination['requested_page'] + 2;
			$showFirstPages = true;
			$showLastPages = true;
		}

		// show previous
		if($this->pagination['requested_page'] > 1)
		{
			// build URL
			if($useQuestionMark) $URL = $this->pagination['url'] . '?page=' . ($this->pagination['requested_page'] - 1);
			else $URL = $this->pagination['url'] . '&amp;page=' . ($this->pagination['requested_page'] - 1);

			// set
			$pagination['show_previous'] = true;
			$pagination['previous_url'] = $URL;
		}

		// show first pages?
		if($showFirstPages)
		{
			// init var
			$pagesFirstStart = 1;
			$pagesFirstEnd = 1;

			// loop pages
			for($i = $pagesFirstStart; $i <= $pagesFirstEnd; $i++)
			{
				// build URL
				if($useQuestionMark) $URL = $this->pagination['url'] . '?page=' . $i;
				else $URL = $this->pagination['url'] . '&amp;page=' . $i;

				// add
				$pagination['first'][] = array('url' => $URL, 'label' => $i);
			}
		}

		// build array
		for($i = $pagesStart; $i <= $pagesEnd; $i++)
		{
			// init var
			$current = ($i == $this->pagination['requested_page']);

			// build URL
			if($useQuestionMark) $URL = $this->pagination['url'] . '?page=' . $i;
			else $URL = $this->pagination['url'] . '&amp;page=' . $i;

			// add
			$pagination['pages'][] = array('url' => $URL, 'label' => $i, 'current' => $current);
		}

		// show last pages?
		if($showLastPages)
		{
			// init var
			$pagesLastStart = $this->pagination['num_pages'];
			$pagesLastEnd = $this->pagination['num_pages'];

			// loop pages
			for($i = $pagesLastStart; $i <= $pagesLastEnd; $i++)
			{
				// build URL
				if($useQuestionMark) $URL = $this->pagination['url'] . '?page=' . $i;
				else $URL = $this->pagination['url'] . '&amp;page=' . $i;

				// add
				$pagination['last'][] = array('url' => $URL, 'label' => $i);
			}
		}

		// show next
		if($this->pagination['requested_page'] < $this->pagination['num_pages'])
		{
			// build URL
			if($useQuestionMark) $URL = $this->pagination['url'] . '?page=' . ($this->pagination['requested_page'] + 1);
			else $URL = $this->pagination['url'] . '&amp;page=' . ($this->pagination['requested_page'] + 1);

			// set
			$pagination['show_next'] = true;
			$pagination['next_url'] = $URL;
		}

		// multiple pages
		$pagination['multiple_pages'] = ($pagination['num_pages'] == 1) ? false : true;

		// assign pagination
		$this->tpl->assign('pagination', $pagination);
	}


	/**
	 * Redirect to a given URL
	 *
	 * @return	void
	 * @param	string $URL				The URL whereto will be redirected.
	 * @param	int[optional] $code		The redirect code, default is 307 which means this is a temporary redirect.
	 */
	public function redirect($URL, $code = 302)
	{
		SpoonHTTP::redirect((string) $URL, (int) $code);
	}


	/**
	 * Set the action, for later use
	 *
	 * @return	void
	 * @param	string $action		The action to set.
	 */
	private function setAction($action)
	{
		$this->action = (string) $action;
	}


	/**
	 * Set the data, for later use
	 *
	 * @return	void
	 * @param	string[optional] $data	The data that should be available.
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
	 * @param	string $module	The module that should be used.
	 */
	private function setModule($module)
	{
		$this->module = (string) $module;
	}


	/**
	 * Set overwrite mode
	 *
	 * @return	void
	 * @param	bool $overwrite		true if the template should overwrite the current template, false if not.
	 */
	protected function setOverwrite($overwrite)
	{
		$this->overwrite = (bool) $overwrite;
	}


	/**
	 * Set the path for the template to include or to replace the current one
	 *
	 * @return	void
	 * @param	string $path	The path to the template that should be loaded.
	 */
	protected function setTemplatePath($path)
	{
		$this->templatePath = (string) $path;
	}
}


/**
 * This class implements a lot of functionality that can be extended by a specific widget
 * @later Check which methods are the same in FrontendBaseBlock, maybe we should extend from a general class
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@dieterve.be>
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
	 * Path to the template
	 *
	 * @var	string
	 */
	protected $templatePath;


	/**
	 * A reference to the current template
	 *
	 * @var	FrontendTemplate
	 */
	public $tpl;


	/**
	 * A reference to the URL-instance
	 *
	 * @var	FrontendURL
	 */
	public $URL;


	/**
	 * Default constructor
	 * The constructor will set some properties.
	 *
	 * @return	void
	 * @param	string $module				The module to use.
	 * @param	string $action				The action to use.
	 * @param	string[optional] $data		The data that should be available.
	 */
	public function __construct($module, $action, $data = null)
	{
		// get objects from the reference so they are accessable
		$this->tpl = Spoon::get('template');
		$this->header = Spoon::get('header');
		$this->URL = Spoon::get('url');

		// set properties
		$this->setModule($module);
		$this->setAction($action);
		$this->setData($data);
	}


	/**
	 * Add a CSS file into the array
	 *
	 * @return	void
	 * @param 	string $file					The path for the CSS-file that should be loaded.
	 * @param	bool[optional] $overwritePath	Whether or not to add the module to this path. Module path is added by default.
	 * @param	bool[optional] $minify			Should the CSS be minified?
	 * @param	bool[optional] $addTimestamp	May we add a timestamp for caching purposes?
	 */
	public function addCSS($file, $overwritePath = false, $minify = true, $addTimestamp = null)
	{
		// redefine
		$file = (string) $file;
		$overwritePath = (bool) $overwritePath;

		// use module path
		if(!$overwritePath) $file = '/frontend/modules/' . $this->getModule() . '/layout/css/' . $file;

		// add css to the header
		$this->header->addCSS($file, $minify, $addTimestamp);
	}


	/**
	 * Add a javascript file into the array
	 *
	 * @deprecated	Deprecated since version 2.2.0. Will be removed in the next version.
	 *
	 * @return	void
	 * @param 	string $file						The path to the javascript-file that should be loaded.
	 * @param 	bool[optional] $overwritePath		Whether or not to add the module to this path. Module path is added by default.
	 * @param	bool[optional] $minify				Should the file be minified?
	 * @param	bool[optional] $parseThroughPHP		Should the file be parsed through PHP?
	 */
	public function addJavascript($file, $overwritePath = false, $minify = true, $parseThroughPHP = false)
	{
		$this->addJS($file, $overwritePath, $minify, $parseThroughPHP);
	}


	/**
	 * Add a javascript file into the array
	 *
	 * @return	void
	 * @param 	string $file						The path to the javascript-file that should be loaded.
	 * @param 	bool[optional] $overwritePath		Whether or not to add the module to this path. Module path is added by default.
	 * @param	bool[optional] $minify				Should the file be minified?
	 * @param	bool[optional] $parseThroughPHP		Should the file be parsed through PHP?
	 */
	public function addJS($file, $overwritePath = false, $minify = true, $parseThroughPHP = false)
	{
		// redefine
		$file = (string) $file;
		$overwritePath = (bool) $overwritePath;

		// use module path
		if(!$overwritePath) $file = '/frontend/modules/' . $this->getModule() . '/js/' . $file;

		// add js to the header
		$this->header->addJS($file, $minify, $parseThroughPHP);
	}


	/**
	 * Execute the action
	 * If a javascript file with the name of the module or action exists it will be loaded.
	 *
	 * @return	void
	 */
	public function execute()
	{
		// build path to the module
		$frontendModulePath = FRONTEND_MODULES_PATH . '/' . $this->getModule();

		// buil URL to the module
		$frontendModuleURL = '/frontend/modules/' . $this->getModule() . '/js';

		// add javascriptfile with same name as module (if the file exists)
		if(SpoonFile::exists($frontendModulePath . '/js/' . $this->getModule() . '.js')) $this->header->addJS($frontendModuleURL . '/' . $this->getModule() . '.js', false, true);

		// add javascriptfile with same name as the action (if the file exists)
		if(SpoonFile::exists($frontendModulePath . '/js/' . $this->getAction() . '.js')) $this->header->addJS($frontendModuleURL . '/' . $this->getAction() . '.js', false, true);
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
	 * Get path for the template
	 *
	 * @return	string
	 */
	public function getTemplatePath()
	{
		return $this->templatePath;
	}


	/**
	 * Load the template
	 *
	 * @return	void
	 * @param	string[optional] $path		The path for the template to use.
	 */
	protected function loadTemplate($path = null)
	{
		// no template given, so we should build the path
		if($path === null)
		{
			// build path to the module
			$frontendModulePath = FRONTEND_MODULES_PATH . '/' . $this->getModule();

			// build template path
			$path = $frontendModulePath . '/layout/widgets/' . $this->getAction() . '.tpl';
		}

		// redefine
		else $template = (string) $path;

		// set template
		$this->setTemplatePath($path);
	}


	/**
	 * Set the action, for later use
	 *
	 * @return	void
	 * @param	string $action		The action to use.
	 */
	private function setAction($action)
	{
		$this->action = (string) $action;
	}


	/**
	 * Set the data, for later use
	 *
	 * @return	void
	 * @param	string[optional] $data	The data that should available.
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
	 * @param	string $module	The module to use.
	 */
	private function setModule($module)
	{
		$this->module = (string) $module;
	}


	/**
	 * Set the path for the template to include or to replace the current one
	 *
	 * @return	void
	 * @param	string $path	The path to the template that should be loaded.
	 */
	private function setTemplatePath($path)
	{
		$this->templatePath = (string) $path;
	}
}


/**
 * This class implements a lot of functionality that can be extended by a specific AJAX action
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendBaseAJAXAction
{
	// status codes
	const OK = 200;
	const BAD_REQUEST = 400;
	const FORBIDDEN = 403;
	const ERROR = 500;


	/**
	 * The current action
	 *
	 * @var	string
	 */
	protected $action;


	/**
	 * The current module
	 *
	 * @var	string
	 */
	protected $module;


	/**
	 * Default constructor
	 * The constructor will set some properties
	 *
	 * @return	void
	 * @param	string $action		The action to use.
	 * @param	string $module		The module to use.
	 */
	public function __construct($action, $module)
	{
		// store the current module and action (we grab them from the URL)
		$this->setModule($module);
		$this->setAction($action);
	}


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// this method will be overwritten by the children
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
	 * Output an answer to the browser
	 *
	 * @return	void
	 * @param	int $statusCode				The status code to use, use one of the available constants (self::OK, self::BAD_REQUEST, self::FORBIDDEN, self::ERROR).
	 * @param	mixed[optional] $data		The data to be returned (will be encoded as JSON).
	 * @param	string[optional] $message	A text-message.
	 */
	public function output($statusCode, $data = null, $message = null)
	{
		// redefine
		$statusCode = (int) $statusCode;
		if($message !== null) $message = (string) $message;

		// create response array
		$response = array('code' => $statusCode, 'data' => $data, 'message' => $message);

		// set correct headers
		SpoonHTTP::setHeadersByCode($statusCode);
		SpoonHTTP::setHeaders('content-type: application/json;charset=utf-8');

		// output JSON to the browser
		echo json_encode($response);

		// stop script execution
		exit;
	}


	/**
	 * Set the action, for later use
	 *
	 * @return	void
	 * @param	string $action	The action to use.
	 */
	protected function setAction($action)
	{
		$this->action = (string) $action;
	}


	/**
	 * Set the module, for later use
	 *
	 * @return	void
	 * @param	string $module	The module to use.
	 */
	protected function setModule($module)
	{
		$this->module = (string) $module;
	}
}

?>