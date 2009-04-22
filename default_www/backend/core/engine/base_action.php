<?php

/**
 * BackendBaseAction
 *
 * This class implements a lot of functionality that can be extended by a specific action
 *
 * @package		backend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
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
	 * A reference to the url-instance
	 *
	 * @var	BackendURL
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
	public function __construct()
	{
		// get objects from the reference so they are accessable from the action-object
		$this->tpl = Spoon::getObjectReference('template');
		$this->url = Spoon::getObjectReference('url');
		$this->header = Spoon::getObjectReference('header');

		// store the current module and action (we grab them from the url)
		$this->setModule($this->url->getModule());
		$this->setAction($this->url->getAction());

		// populate the parameter array, we loop GET and urldecode the values for usage later on
		foreach((array) $_GET as $key => $value)
		{
			// is the value an array?
			if(is_array($value))
			{
				// urldecode each element in the array (REMARK: we don't support multidim arrays)
				// arrays in GET are ugly and stupid
				$this->parameters[$key] = (array) array_map('urldecode', $value);
			}

			// it's just a string
			else $this->parameters[$key] = urldecode($value);
		}
	}


	/**
	 * Display, this wil output the template to the browser
	 * If no template is specified we build the path form the current module and action
	 *
	 * @return	void
	 * @param	string[optional] $template
	 */
	public function display($template = null)
	{
		// parse header
		$this->header->parse();

		// if no template is specified we have to build the path ourself
		if($template === null) $template = BACKEND_MODULE_PATH .'/layout/templates/'. $this->url->getAction() .'.tpl';

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
		// add jquery, we will need this in every action, so add it global
		$this->header->addJS('jquery/jquery.js', 'core');
		$this->header->addJS('jquery/jquery.hilight.js', 'core');
		$this->header->addJS('backend.js', 'core');
		$this->header->addCSS('screen.css', 'core');

		// this method will be overwritten by the childs so
	}


	/**
	 * Get the action
	 *
	 * @return	string
	 */
	public function getAction()
	{
		return (string) $this->action;
	}


	/**
	 * Get the module
	 *
	 * @return	string
	 */
	public function getModule()
	{
		return (string) $this->module;
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
		// init var
		$aAllowedTypes = array('bool', 'boolean',
								'int', 'integer',
								'float', 'double',
								'string',
								'array');

		// redefine
		$key = (string) $key;
		$type = (string) $type;

		// is this parameter available
		if(isset($this->parameters[$key]))
		{
			// cast it
			switch($type)
			{
				// boolean
				case 'bool':
				case 'boolean':
					return (bool) $this->parameters[$key];

				// integer
				case 'int':
				case 'integer':
					return (int) $this->parameters[$key];

				// float
				case 'float':
					return (float) $this->parameters[$key];

				// double
				case 'double':
					return (double) $this->parameters[$key];

				// string
				case 'string':
					return (string) $this->parameters[$key];

				// array
				case 'array':
					return (string) $this->parameters[$key];

				// invalid type
				default:
					throw new BackendException('Invalid type ('. $type .'). Possible values are: '. implode(', ', $aAllowedTypes)) .'.';
			}
		}

		// fallback
		return null;
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
	 * Set the module, for later use
	 *
	 * @return	void
	 * @param	string $module
	 */
	private function setModule($module)
	{
		$this->module = (string) $module;
	}
}


/**
 * BackendBaseActionIndex
 *
 * This class implements a lot of functionality that can be extended by the real action.
 * In this case this is the base class for the index action
 *
 * @package		Backend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendBaseActionIndex extends BackendBaseAction
{
	/**
	 * Execute the current action
	 * This method will be overwriten in most of the actions, but still be called to add general stuff
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, will add general CSS and JS
		parent::execute();

		// is there a report to show?
		if($this->getParameter('report') !== null)
		{
			// show the report
			$this->tpl->assign('report', true);

			// init var
			$messageName = '';

			// alter messageName
			switch($this->getParameter('report'))
			{
				case 'add':
					$messageName = 'added';
				break;

				case 'delete':
					$messageName = 'deleted';
				break;

				case 'edit':
					$messageName = 'edited';
				break;
			}

			// camelcase the string
			$messageName = SpoonFilter::toCamelCase($messageName);

			// if we have data to use it will be passed as the var-parameter, if so assign it
			if($this->getParameter('var') !== null) $this->tpl->assign('reportMessage', sprintf(BackendLanguage::getMessage($messageName), $this->getParameter('var')));
			else $this->tpl->assign('reporMessage', $messageName);

			// hilight an element with the given id if needed
			if($this->getParameter('hilight')) $this->tpl->assign('hilight', $this->getParameter('hilight'));
		}

		// is there an error to show?
		if($this->getParameter('error') !== null)
		{
			// show the error and the errormessage
			$this->tpl->assign('errorMessage', BackendLanguage::getError(SpoonFilter::toCamelCase($this->getParameter('error'), '-')));
		}

		// add default js file (if the file exists)
		if(SpoonFile::exists(BACKEND_MODULE_PATH .'/js/index.js')) $this->header->addJS('index.js', null, true);
	}
}


/**
 * BackendBaseActionAdd
 *
 * This class implements a lot of functionality that can be extended by the real action.
 * In this case this is the base class for the add action
 *
 * @package		Backend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendBaseActionAdd extends BackendBaseAction
{
	/**
	 * The form instance
	 *
	 * @var	SpoonForm
	 */
	protected $frm;


	/**
	 * Execute the current action
	 * This method will be overwriten in most of the actions, but still be called to add general stuff
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, will add general CSS and JS
		parent::execute();

		// add default js file (if the file exists)
		if(SpoonFile::exists(BACKEND_MODULE_PATH .'/js/add.js')) $this->header->addJS('add.js', null, true);
	}
}



/**
 * BackendBaseActionEdit
 *
 * This class implements a lot of functionality that can be extended by the real action.
 * In this case this is the base class for the edit action
 *
 * @package		Backend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendBaseActionEdit extends BackendBaseAction
{
	/**
	 * The form instance
	 *
	 * @var	SpoonForm
	 */
	protected $frm;


	/**
	 * The id of the item to edit
	 *
	 * @var	int
	 */
	protected $id;


	/**
	 * The data of the item to edit
	 *
	 * @var	array
	 */
	protected $record;


	/**
	 * Execute the current action
	 * This method will be overwriten in most of the actions, but still be called to add general stuff
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, will add general CSS and JS
		parent::execute();

		// add default js file (if the file exists)
		if(SpoonFile::exists(BACKEND_MODULE_PATH .'/js/edit.js')) $this->header->addJS('edit.js', null, true);
	}
}


/**
 * BackendBaseActionDelete
 *
 * This class implements a lot of functionality that can be extended by the real action.
 * In this case this is the base class for the delete action
 *
 * @package		Backend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
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
	 *
	 * @return	void
	 */
	public function execute()
	{
	}
}


?>