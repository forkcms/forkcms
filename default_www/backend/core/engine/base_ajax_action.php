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