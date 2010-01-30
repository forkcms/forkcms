<?php

/**
 * FrontendBaseObject
 *
 * This class will be the base of the objects used in onsite
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
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
	 * Class constructor.
	 * It will grab stuff from the reference.
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// get template from reference
		$this->tpl = Spoon::getObjectReference('template');

		// get URL from reference
		$this->URL = Spoon::getObjectReference('url');
	}
}


/**
 * FrontendBaseConfig
 *
 * This is the base-object for config-files. The module-specific config-files can extend the functionality from this class.
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
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
	 * Class constructor.
	 *
	 * @return	void
	 * @param	string $module
	 */
	public function __construct($module)
	{
		// set module
		$this->module = (string) $module;

		// check if model exists
		if(SpoonFile::exists(FRONTEND_MODULES_PATH .'/'. $this->getModule() .'/engine/model.php'))
		{
			// the model exists, so we require it
			require_once FRONTEND_MODULES_PATH .'/'. $this->getModule() .'/engine/model.php';
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
		$frontendModulePath = FRONTEND_MODULES_PATH .'/'. $this->getModule();

		// get filelist (only those with .php-extension)
		$actionFiles = (array) SpoonFile::getList($frontendModulePath .'/actions', '/(.*).php/');

		// loop filelist
		foreach($actionFiles as $file)
		{
			// get action by removing the extension, actions should not contain spaces (use _ instead)
			$action = strtolower(str_replace('.php', '', $file));

			// if the action isn't disabled add it to the possible actions
			if(!in_array($action, $this->disabledActions)) $this->possibleActions[$file] = $action;
		}

		// get filelist (only those with .php-extension)
		$AJAXActionFiles = (array) SpoonFile::getList($frontendModulePath .'/ajax', '/(.*).php/');

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
 * FrontendBaseBlock
 *
 * This class implements a lot of functionality that can be extended by a specific block
 *
 * @todo Check which methods are the same in FrontendBaseWidget, maybe we should extend from a general class
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
	 *
	 * @return	void
	 * @param	string $action
	 * @param	string $module
	 * @param	string[optional] $data
	 */
	public function __construct($module, $action, $data = null)
	{
		// get objects from the reference so they are accessable
		$this->tpl = Spoon::getObjectReference('template');
		$this->header = Spoon::getObjectReference('header');
		$this->URL = Spoon::getObjectReference('url');
		$this->breadcrumb = Spoon::getObjectReference('breadcrumb');

		// set properties
		$this->setModule($module);
		$this->setAction($action);
		$this->setData($data);
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
		$frontendModulePath = FRONTEND_MODULES_PATH .'/'. $this->getModule();

		// buil URL to the module
		$frontendModuleURL = '/frontend/modules/'. $this->getModule() .'/js';

		// add javascriptfile with same name as module (if the file exists)
		if(SpoonFile::exists($frontendModulePath .'/js/'. $this->getModule() .'.js')) $this->header->addJavascript($frontendModuleURL .'/'. $this->getModule() .'.js', false, true);

		// add javascriptfile with same name as the action (if the file exists)
		if(SpoonFile::exists($frontendModulePath .'/js/'. $this->getAction() .'.js')) $this->header->addJavascript($frontendModuleURL .'/'. $this->getAction() .'.js', false, true);
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
	 * @param	string[optional] $template
	 * @param	bool[optional] $overwrite
	 */
	protected function loadTemplate($template = null, $overwrite = false)
	{
		// redefine
		$overwrite = (bool) $overwrite;

		// if no template is passed we should build the path
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
		if(!SpoonFile::exists($template)) throw new FrontendException('The template ('. $template .') doesn\'t exist.');

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

		// validate pagination array
		if(!isset($this->pagination['limit'])) throw new FrontendException('no limit limit in the pagination-property.');
		if(!isset($this->pagination['offset'])) throw new FrontendException('no limit offset in the pagination-property.');
		if(!isset($this->pagination['requested_page'])) throw new FrontendException('no requested_page available in the pagination-property.');
		if(!isset($this->pagination['item_count'])) throw new FrontendException('no item_count available in the pagination-property.');
		if(!isset($this->pagination['url'])) throw new FrontendException('no url available in the pagination-property.');

		// no pagination needed
		if($this->pagination['pages_count'] < 1) return;

		// populate count fields
		$pagination['pages_count'] = $this->pagination['pages_count'];
		$pagination['current_page'] = $this->pagination['requested_page'];

		// as long as we are below page 7 we should show all pages starting from 1
		if($this->pagination['requested_page'] < 8)
		{
			$pagesStart = 1;
			$pagesEnd = ($this->pagination['pages_count'] >= 8) ? 8 : $this->pagination['pages_count'];

			// show last pages
			if($this->pagination['pages_count'] > 8) $showLastPages = true;
		}

		// as long as we are 7 pages from the end we should show all pages till the end
		elseif($this->pagination['requested_page'] >= ($this->pagination['pages_count'] - 8))
		{
			$pagesStart = ($this->pagination['pages_count'] - 7);
			$pagesEnd = $this->pagination['pages_count'];

			$showFirstPages = true;
		}

		// page 7
		else
		{
			$pagesStart = $this->pagination['requested_page'] - 2;
			$pagesEnd = $this->pagination['requested_page'] + 2;
			$showFirstPages = true;
			$showLastPages = true;
		}

		// show previous
		if($this->pagination['requested_page'] > 1)
		{
			$pagination['show_previous'] = true;
			$pagination['previous_url'] = $this->pagination['url'] .'/'. ($this->pagination['requested_page'] - 1);
		}

		// show first pages?
		if($showFirstPages)
		{
			// init var
			$pagesFirstStart = 1;
			$pagesFirstEnd = 2;

			// loop pages
			for($i = $pagesFirstStart; $i <= $pagesFirstEnd; $i++)
			{
				// init var
				$URL = $this->pagination['url'] .'/'. $i;

				// add
				$pagination['pages_first'][] = array('url' => $URL, 'label' => $i);
			}
		}

		// build array
		for($i = $pagesStart; $i <= $pagesEnd; $i++)
		{
			// init var
			$current = ($i == $this->pagination['requested_page']);
			$URL = $this->pagination['url'] .'/'. $i;

			// add
			$pagination['pages'][] = array('url' => $URL, 'label' => $i, 'current' => $current);
		}

		// show last pages?
		if($showLastPages)
		{
			// init var
			$pagesLastStart = $this->pagination['pages_count'] - 1;
			$pagesLastEnd = $this->pagination['pages_count'];

			// loop pages
			for($i = $pagesLastStart; $i <= $pagesLastEnd; $i++)
			{
				// init var
				$URL = $this->pagination['url'] .'/'. $i;

				// add
				$pagination['pages_last'][] = array('url' => $URL, 'label' => $i);
			}
		}

		// show next
		if($this->pagination['requested_page'] < $this->pagination['pages_count'])
		{
			$pagination['show_next'] = true;
			$pagination['next_url'] = $this->pagination['url'] .'/'. ($this->pagination['requested_page'] + 1);
		}

		// assign pagination
		// @todo we should do this in a decent way...
		// loop values
		foreach($pagination as $key => $value) $this->tpl->assign('pagination'. SpoonFilter::toCamelCase($key), $value);
		$this->tpl->assign('pagination', $pagination);
	}

	/**
	 * Redirect to a given URL
	 *
	 * @return	void
	 * @param	string $URL
	 * @param	int[optional] $code
	 */
	public function redirect($URL, $code = 302)
	{
		SpoonHTTP::redirect((string) $URL, (int) $code);
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
 * This class implements a lot of functionality that can be extended by a specific widget
 *
 * @todo Check which methods are the same in FrontendBaseBlock, maybe we should extend from a general class
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
	public $URL;


	/**
	 * Default constructor
	 * The constructor will set some properties.
	 *
	 * @return	void
	 * @param	string $action
	 * @param	string $module
	 * @param	string[optional] $data
	 */
	public function __construct($module, $action, $data = null)
	{
		// get objects from the reference so they are accessable
		$this->tpl = Spoon::getObjectReference('template');
		$this->header = Spoon::getObjectReference('header');
		$this->URL = Spoon::getObjectReference('url');

		// set properties
		$this->setModule($module);
		$this->setAction($action);
		$this->setData($data);
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
		$frontendModulePath = FRONTEND_MODULES_PATH .'/'. $this->getModule();

		// buil url to the module
		$frontendModuleURL = '/frontend/modules/'. $this->getModule() .'/js';

		// add javascriptfile with same name as module (if the file exists)
		if(SpoonFile::exists($frontendModulePath .'/js/'. $this->getModule() .'.js')) $this->header->addJavascript($frontendModuleURL .'/'. $this->getModule() .'.js', false, true);

		// add javascriptfile with same name as the action (if the file exists)
		if(SpoonFile::exists($frontendModulePath .'/js/'. $this->getAction() .'.js')) $this->header->addJavascript($frontendModuleURL .'/'. $this->getAction() .'.js', false, true);
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
	 * @param	string[optional] $template
	 */
	protected function loadTemplate($template = null)
	{
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
	 * Redirect to a given URL
	 *
	 * @return	void
	 * @param	string $URL
	 * @param	int[optional] $code
	 */
	public function redirect($URL, $code = 302)
	{
		SpoonHTTP::redirect((string) $URL, (int) $code);
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


/**
 * FrontendBaseAJAXAction
 *
 * This class implements a lot of functionality that can be extended by a specific AJAX action
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
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
	 * @param	string $action
	 * @param	string $module
	 */
	public function __construct($action, $module)
	{
		// store the current module and action (we grab them from the url)
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
	 * @param	int $statusCode
	 * @param	mixed[optional] $data
	 * @param	string[optional] $message
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
		SpoonHTTP::setHeaders('content-type: application/json');

		// output to the browser
		echo json_encode($response);
		exit;
	}


	/**
	 * Set the action, for later use
	 *
	 * @return	void
	 * @param	string $action
	 */
	protected function setAction($action)
	{
		$this->action = (string) $action;
	}


	/**
	 * Set the module, for later use
	 *
	 * @return	void
	 * @param	string $module
	 */
	protected function setModule($module)
	{
		$this->module = (string) $module;
	}
}

?>