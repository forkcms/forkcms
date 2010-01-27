<?php

/**
 * BackendBaseAJAXAction
 *
 * This class implements a lot of functionality that can be extended by a specific AJAX action
 *
 * @package		backend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
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
	 * Default constructor
	 * The constructor will set some properties. It populates the parameter array with urldecoded values for easy-use.
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
		// this method will be overwritten by the childs so
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
	 * @todo	We use UTF, so utf8_decode isn't needed
	 *
	 * @return	void
	 * @param	int $statusCode
	 * @param	mixed[optional] $data
	 * @param	string[optional] $message
	 * @param	bool[optional] $utf8decode	@todo tijs - deze parameter mag eigenlijk weg, denk ik (davy)
	 * @param	bool[optional] $htmlentities @todo tijs - als we met utf8 werken moeten zaken zoals ûû eigenlijk niet omgezet worden.
	 */
	public function output($statusCode, $data = null, $message = null, $utf8decode = true, $htmlentities = false)
	{
		// redefine
		$statusCode = (int) $statusCode;
		if($message !== null) $message = (string) $message;
		$utf8decode = (bool) $utf8decode;
		$htmlentities = (bool) $htmlentities;

		// should the values in the data-array be utf8-decoded?
		if($utf8decode)
		{
			if(!empty($data)) $data = self::recursiveMap('utf8_decode', $data);
			$message = utf8_decode($message);
		}

		// should the values in the data-array be htmlentities?
		if($htmlentities)
		{
			if(!empty($data)) $data = self::recursiveMap(array('SpoonFilter', 'htmlentities'), $data);
			$message = SpoonFilter::htmlentities($message);
		}

		// create response array
		$response = array('code' => $statusCode, 'data' => $data, 'message' => $message);

		// set correct headers
		SpoonHTTP::setHeaders('content-type: application/json');

		// output to the browser
		echo json_encode($response);
		exit;
	}


	/**
	 * Recursively maps a function to a value of an array
	 *
	 * @return	array
	 * @param	string $function
	 * @param	array $array
	 */
	private static function recursiveMap($function, array $array)
	{
		// redefine
		$function = (string) $function;

		// init var
		$newArray = array();

		// loop values
		foreach($array as $key => $value)
		{
			// add recursive of execute function
			$newArray[$key] = (is_array($value)) ? self::recursiveMap($function, $value) : call_user_func($function, $value);
		}

		// return
		return $newArray;
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