<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class implements a lot of functionality that can be extended by a specific action
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendBaseAction
{
	/**
	 * The current action
	 *
	 * @var	string
	 */
	protected $action;

	/**
	 * The parameters (urldecoded)
	 *
	 * @var	array
	 */
	protected $parameters = array();

	/**
	 * The header object
	 *
	 * @var	BackendHeader
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
	 * @var	BackendTemplate
	 */
	public $tpl;

	/**
	 * A reference to the URL-instance
	 *
	 * @var	BackendURL
	 */
	public $URL;

	/**
	 * The constructor will set some properties. It populates the parameter array with urldecoded
	 * values for easy-use.
	 */
	public function __construct()
	{
		// get objects from the reference so they are accessable from the action-object
		$this->tpl = Spoon::get('template');
		$this->URL = Spoon::get('url');
		$this->header = Spoon::get('header');

		// store the current module and action (we grab them from the URL)
		$this->setModule($this->URL->getModule());
		$this->setAction($this->URL->getAction());

		// populate the parameter array, we loop GET and urldecode the values for usage later on
		foreach((array) $_GET as $key => $value) $this->parameters[$key] = $value;
	}

	/**
	 * Display, this wil output the template to the browser
	 * If no template is specified we build the path form the current module and action
	 *
	 * @param string[optional] $template The template to use, if not provided it will be based on the action.
	 */
	public function display($template = null)
	{
		// parse header
		$this->header->parse();

		/*
		 * If no template is specified, we have to build the path ourself. The default template is
		 * based on the name of the current action
		 */
		if($template === null)
		{
			$template = BACKEND_MODULE_PATH . '/layout/templates/' . $this->URL->getAction() . '.tpl';
		}

		$this->tpl->display($template);
	}

	/**
	 * Execute the action
	 */
	public function execute()
	{
		// add jquery, we will need this in every action, so add it globally
		$this->header->addJS('jquery/jquery.js', 'core', false);
		$this->header->addJS('jquery/jquery.ui.js', 'core', false);
		$this->header->addJS('jquery/jquery.ui.dialog.patch.js', 'core');
		$this->header->addJS('jquery/jquery.tools.js', 'core', false);
		$this->header->addJS('jquery/jquery.backend.js', 'core');

		// add items that always need to be loaded
		$this->header->addJS('utils.js', 'core');
		$this->header->addJS('backend.js', 'core', false, true);

		// add module js
		if(SpoonFile::exists(BACKEND_MODULE_PATH . '/js/' . $this->getModule() . '.js'))
		{
			$this->header->addJS($this->getModule() . '.js', null, false, true);
		}

		// add action js
		if(SpoonFile::exists(BACKEND_MODULE_PATH . '/js/' . $this->getAction() . '.js'))
		{
			$this->header->addJS($this->getAction() . '.js', null, false, true);
		}

		// add core css files
		$this->header->addCSS('reset.css', 'core');
		$this->header->addCSS('jquery_ui/fork/jquery_ui.css', 'core');
		$this->header->addCSS('screen.css', 'core');
		$this->header->addCSS('debug.css', 'core');

		// add module specific css
		if(SpoonFile::exists(BACKEND_MODULE_PATH . '/layout/css/' . $this->getModule() . '.css'))
		{
			$this->header->addCSS($this->getModule() . '.css');
		}

		// store var so we don't have to call this function twice
		$var = $this->getParameter('var', 'array');

		// is there a report to show?
		if($this->getParameter('report') !== null)
		{
			// show the report
			$this->tpl->assign('report', true);

			// camelcase the string
			$messageName = SpoonFilter::toCamelCase($this->getParameter('report'), '-');

			// if we have data to use it will be passed as the var parameter
			if(!empty($var)) $this->tpl->assign('reportMessage', vsprintf(BL::msg($messageName), $var));
			else $this->tpl->assign('reportMessage', BL::msg($messageName));

			// highlight an element with the given id if needed
			if($this->getParameter('highlight')) $this->tpl->assign('highlight', $this->getParameter('highlight'));
		}

		// is there an error to show?
		if($this->getParameter('error') !== null)
		{
			// camelcase the string
			$errorName = SpoonFilter::toCamelCase($this->getParameter('error'), '-');

			// if we have data to use it will be passed as the var parameter
			if(!empty($var)) $this->tpl->assign('errorMessage', vsprintf(BL::err($errorName), $var));
			else $this->tpl->assign('errorMessage', BL::err($errorName));
		}
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
	 * Get the module
	 *
	 * @return string
	 */
	public function getModule()
	{
		return $this->module;
	}

	/**
	 * Get a parameter for a given key
	 * The function will return null if the key is not available
	 * By default we will cast the return value into a string, if you want something else specify it by passing the wanted type.
	 *
	 * @param string $key The name of the parameter.
	 * @param string[optional] $type The return-type, possible values are: bool, boolean, int, integer, float, double, string, array.
	 * @param mixed[optional] $defaultValue The value that should be returned if the key is not available.
	 * @return mixed
	 */
	public function getParameter($key, $type = 'string', $defaultValue = null)
	{
		$key = (string) $key;

		// parameter exists
		if(isset($this->parameters[$key]) && $this->parameters[$key] != '')
		{
			return SpoonFilter::getValue($this->parameters[$key], null, null, $type);
		}

		return $defaultValue;
	}

	/**
	 * Parse to template
	 */
	protected function parse()
	{

	}

	/**
	 * Redirect to a given URL
	 *
	 * @param string $URL The URL to redirect to.
	 */
	public function redirect($URL)
	{
		SpoonHTTP::redirect(str_replace('&amp;', '&', (string) $URL));
	}

	/**
	 * Set the action, for later use
	 *
	 * @param string $action The action to load.
	 */
	private function setAction($action)
	{
		$this->action = (string) $action;
	}

	/**
	 * Set the module, for later use
	 *
	 * @param string $module The module to load.
	 */
	private function setModule($module)
	{
		$this->module = (string) $module;
	}
}

/**
 * This class implements a lot of functionality that can be extended by the real action.
 * In this case this is the base class for the index action
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendBaseActionIndex extends BackendBaseAction
{
	/**
	 * A datagrid instance
	 *
	 * @var	BackendDataGridDB
	 */
	protected $dataGrid;

	/**
	 * Execute the current action
	 * This method will be overwriten in most of the actions, but still be called to add general stuff
	 */
	public function execute()
	{
		parent::execute();
	}

	/**
	 * Parse to template
	 */
	protected function parse()
	{
		parent::parse();
	}
}

/**
 * This class implements a lot of functionality that can be extended by the real action.
 * In this case this is the base class for the add action
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendBaseActionAdd extends BackendBaseAction
{
	/**
	 * The form instance
	 *
	 * @var	BackendForm
	 */
	protected $frm;

	/**
	 * The backends meta-object
	 *
	 * @var	BackendMeta
	 */
	protected $meta;

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		parent::parse();

		if($this->frm) $this->frm->parse($this->tpl);
	}
}

/**
 * This class implements a lot of functionality that can be extended by the real action.
 * In this case this is the base class for the edit action
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendBaseActionEdit extends BackendBaseAction
{
	/**
	 * DataGrid with the revisions
	 *
	 * @var	BackendDataGridDB
	 */
	protected $dgRevisions;

	/**
	 * The form instance
	 *
	 * @var	BackendForm
	 */
	protected $frm;

	/**
	 * The id of the item to edit
	 *
	 * @var	int
	 */
	protected $id;

	/**
	 * The backends meta-object
	 *
	 * @var	BackendMeta
	 */
	protected $meta;

	/**
	 * The data of the item to edit
	 *
	 * @var	array
	 */
	protected $record;

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		parent::parse();

		if($this->frm) $this->frm->parse($this->tpl);
	}
}

/**
 * This class implements a lot of functionality that can be extended by the real action.
 * In this case this is the base class for the delete action
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendBaseActionDelete extends BackendBaseAction
{
	/**
	 * The id of the item to edit
	 *
	 * @var	int
	 */
	protected $id;

	/**
	 * The data of the item to edite
	 *
	 * @var	array
	 */
	protected $record;

	/**
	 * Execute the current action
	 * This method will be overwriten in most of the actions, but still be called to add general stuff
	 */
	public function execute()
	{
		parent::parse();
	}
}

/**
 * This class implements a lot of functionality that can be extended by a specific AJAX action
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendBaseAJAXAction
{
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
	 * The constructor will set some properties. It populates the parameter array with urldecoded values for easy-use.
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
	 */
	public function execute()
	{
		// this method will be overwritten by the children
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
	 * Get the module
	 *
	 * @return string
	 */
	public function getModule()
	{
		return $this->module;
	}

	/**
	 * Output an answer to the browser
	 *
	 * @param int $statusCode The status code for the response, use the available constants. (self::OK, self::BAD_REQUEST, self::FORBIDDEN, self::ERROR).
	 * @param mixed[optional] $data The data to output.
	 * @param string[optional] $message The text-message to send.
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

		// output JSON to the browser
		echo json_encode($response);
		exit;
	}

	/**
	 * Set the action, for later use
	 *
	 * @param string $action The action to load.
	 */
	protected function setAction($action)
	{
		$this->action = (string) $action;
	}

	/**
	 * Set the module, for later use
	 *
	 * @param string $module The module to load.
	 */
	protected function setModule($module)
	{
		$this->module = (string) $module;
	}
}

/**
 * This is the base-object for config-files. The module-specific config-files can extend the functionality from this class
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendBaseConfig
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
	 * @param string $module The module wherefor this is the configuration-file.
	 */
	public function __construct($module)
	{
		$this->module = (string) $module;

		// require the model if it exists
		if(SpoonFile::exists(BACKEND_MODULES_PATH . '/' . $this->getModule() . '/engine/model.php'))
		{
			require_once BACKEND_MODULES_PATH . '/' . $this->getModule() . '/engine/model.php';
		}

		// read the possible actions based on the files
		$this->setPossibleActions();
	}

	/**
	 * Get the default action
	 *
	 * @return string
	 */
	public function getDefaultAction()
	{
		return $this->defaultAction;
	}

	/**
	 * Get the current loaded module
	 *
	 * @return string
	 */
	public function getModule()
	{
		return $this->module;
	}

	/**
	 * Get the possible actions
	 *
	 * @return array
	 */
	public function getPossibleActions()
	{
		return $this->possibleActions;
	}

	/**
	 * Get the possible AJAX actions
	 *
	 * @return array
	 */
	public function getPossibleAJAXActions()
	{
		return $this->possibleAJAXActions;
	}

	/**
	 * Set the possible actions, based on files in folder
	 * You can disable action in the config file. (Populate $disabledActions)
	 */
	protected function setPossibleActions()
	{
		// get filelist (only those with .php-extension)
		$actionFiles = (array) SpoonFile::getList(BACKEND_MODULE_PATH . '/actions', '/(.*).php/');

		// loop filelist
		foreach($actionFiles as $file)
		{
			// get action by removing the extension, actions should not contain spaces (use _ instead)
			$action = strtolower(str_replace('.php', '', $file));

			// if the action isn't disabled add it to the possible actions
			if(!in_array($action, $this->disabledActions)) $this->possibleActions[$file] = $action;
		}

		// get filelist (only those with .php-extension)
		$AJAXActionFiles = (array) SpoonFile::getList(BACKEND_MODULE_PATH . '/ajax', '/(.*).php/');

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
 * This is the base-object for cronjobs. The module-specific cronjob-files can extend the functionality from this class
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendBaseCronjob
{
	/**
	 * The current action
	 *
	 * @var	string
	 */
	protected $action;

	/**
	 * The current id
	 *
	 * @var	int
	 */
	protected $id;

	/**
	 * The current module
	 *
	 * @var	string
	 */
	protected $module;

	/**
	 * @param string $action The action to load.
	 * @param string $module The module to load.
	 */
	public function __construct($action, $module)
	{
		// store the current module and action (we grab them from the URL)
		$this->setModule($module);
		$this->setAction($action);
	}

	/**
	 * Clear/removed the busy file
	 */
	protected function clearBusyFile()
	{
		// build path
		$path = BACKEND_CACHE_PATH . '/cronjobs/' . $this->getId() . '.busy';

		// remove the file
		SpoonFile::delete($path);
	}

	/**
	 * Execute the action
	 */
	public function execute()
	{
		// check if model exists
		if(SpoonFile::exists(BACKEND_MODULES_PATH . '/' . $this->getModule() . '/engine/model.php'))
		{
			// the model exists, so we require it
			require_once BACKEND_MODULES_PATH . '/' . $this->getModule() . '/engine/model.php';
		}
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
	 * Get the id
	 *
	 * @return int
	 */
	public function getId()
	{
		return strtolower($this->getModule() . '_' . $this->getAction());
	}

	/**
	 * Get the module
	 *
	 * @return string
	 */
	public function getModule()
	{
		return $this->module;
	}

	/**
	 * Set the action, for later use
	 *
	 * @param string $action The action to load.
	 */
	protected function setAction($action)
	{
		$this->action = (string) $action;
	}

	/**
	 * Set the busy file
	 */
	protected function setBusyFile()
	{
		// do not set busy file in debug mode
		if(SPOON_DEBUG) return;

		// build path
		$path = BACKEND_CACHE_PATH . '/cronjobs/' . $this->getId() . '.busy';

		// init var
		$isBusy = false;

		// does the busy file already exists.
		if(SpoonFile::exists($path))
		{
			$isBusy = true;

			// grab counter
			$counter = (int) SpoonFile::getContent($path);

			// check the counter
			if($counter > 9)
			{
				// build class name
				$className = 'Backend' . SpoonFilter::toCamelCase($this->getModule() . '_cronjob_' . $this->getAction());

				// notify user
				throw new BackendException('Cronjob (' . $className . ') is still busy after 10 runs, check it out!');
			}
		}

		// set counter
		else $counter = 0;

		// increment counter
		$counter++;

		// store content
		SpoonFile::setContent($path, $counter, true, false);

		// if the cronjob is busy we should NOT proceed
		if($isBusy) exit;
	}

	/**
	 * Set the module, for later use
	 *
	 * @param string $module The module to load.
	 */
	protected function setModule($module)
	{
		$this->module = (string) $module;
	}
}

/**
 * This is the base-object for widgets
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendBaseWidget
{
	/**
	 * The column wherin the widget should be shown
	 *
	 * @var	string
	 */
	private $column = 'left';

	/**
	 * The header object
	 *
	 * @var	BackendHeader
	 */
	protected $header;

	/**
	 * The position in the column where the widget should be shown
	 *
	 * @var	int
	 */
	private $position;

	/**
	 * Required rights needed for this widget.
	 *
	 * @var	array
	 */
	protected $rights = array();

	/**
	 * The template to use
	 *
	 * @var	string
	 */
	private $templatePath;

	/**
	 * A reference to the current template
	 *
	 * @var	BackendTemplate
	 */
	public $tpl;

	/**
	 * The constructor will set some properties, it populates the parameter array with urldecoded
	 * values for ease of use.
	 */
	public function __construct()
	{
		$this->tpl = Spoon::get('template');
		$this->header = Spoon::get('header');
	}

	/**
	 * Display, this wil output the template to the browser
	 * If no template is specified we build the path form the current module and action
	 *
	 * @param string[optional] $template The template to use.
	 */
	protected function display($template = null)
	{
		if($template !== null) $this->templatePath = (string) $template;
	}

	/**
	 * Get the column
	 *
	 * @return string
	 */
	public function getColumn()
	{
		return $this->column;
	}

	/**
	 * Get the position
	 *
	 * @return mixed
	 */
	public function getPosition()
	{
		return $this->position;
	}

	/**
	 * Get the template path
	 *
	 * @return mixed
	 */
	public function getTemplatePath()
	{
		return $this->templatePath;
	}

	/**
	 * Is this widget allowed for this user?
	 *
	 * @return bool
	 */
	public function isAllowed()
	{
		foreach($this->rights as $rights)
		{
			list($module, $action) = explode('/', $rights);

			// check action rights
			if(isset($module) && isset($action))
			{
				if(!BackendAuthentication::isAllowedAction($action, $module)) return false;
			}
		}

		return true;
	}

	/**
	 * Set column for the widget
	 *
	 * @param string $column Possible values are: left, middle, right.
	 */
	protected function setColumn($column)
	{
		$allowedColumns = array('left', 'middle', 'right');
		$this->column = SpoonFilter::getValue((string) $column, $allowedColumns, 'left');
	}

	/**
	 * Set the position for the widget
	 *
	 * @param int $position The position for the widget.
	 */
	protected function setPosition($position)
	{
		$this->position = (int) $position;
	}
}
