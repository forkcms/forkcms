<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package			webservices
 * @subpackage		rest
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author 			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			1.0.0
 */


/** Spoon class */
require_once 'spoon/spoon.php';

/** SpoonRESTException class */
require_once 'spoon/webservices/rest/exception.php';


/**
 * This base class provides all the methods used by cookies.
 *
 * @package			webservices
 * @subpackage		rest
 *
 * @author 			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			1.0.0
 */
class SpoonRESTResponse
{
	/**
	 * The body
	 *
	 * @var	string
	 */
	private $body;


	/**
	 * The error array
	 *
	 * @var	array
	 */
	private $error;


	/**
	 * The headers
	 *
	 * @var	string
	 */
	private $headers;


	/**
	 * The faultstatus
	 *
	 * @var unknown_type
	 */
	private $isError = false;


	/**
	 * The raw response
	 *
	 * @var	string
	 */
	private $response = '';


	/**
	 * The values
	 *
	 * @var	array
	 */
	private $value;


	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param	string
	 */
	public function __construct($response)
	{
		// set properties
		$this->setResponse($response);

		// process response
		$this->processResponse();

		// process body
		$this->processHeader();
	}


	/**
	 * Gets the response body
	 *
	 * @return	string
	 */
	public function getBody()
	{
		return $this->body;
	}


	/**
	 * Gets the error-array
	 *
	 * @return	array
	 */
	public function getError()
	{
		return $this->error;
	}


	/**
	 * Get the response headers
	 *
	 * @return	string
	 */
	public function getHeader()
	{
		return $this->headers;
	}


	/**
	 * Gets the raw response
	 *
	 * @return	string
	 */
	public function getResponse()
	{
		return $this->response;
	}


	/**
	 * Get the values
	 *
	 * @return	array
	 */
	public function getValue()
	{
		return $this->value;
	}


	/**
	 * is this a fault?
	 *
	 * @return	bool
	 */
	public function isError()
	{
		return $this->isError;
	}


	/**
	 * Process the header
	 *
	 * @return	void
	 */
	private function processHeader()
	{
		if(substr_count(strtolower($this->headers), '404') >= 1) $this->setError(array('code' => 404, 'message' => '404 Not Found'));
	}


	/**
	 * Processes the response
	 *
	 * @return	void
	 */
	private function processResponse()
	{
		$parts = explode("\r\n\r\n", $this->getResponse());
		$this->setHeaders($parts[0]);
		$this->setBody($parts[1]);
	}


	/**
	 * Set the body
	 *
	 * @return	void
	 * @param	string $body
	 */
	private function setBody($body)
	{
		$this->body = (string) $body;
	}


	/**
	 * Set an error
	 *
	 * @return	void
	 * @param	array $aError
	 */
	private function setError($aError)
	{
		// validate
		if(!isset($aError['code']) || !isset($aError['message'])) throw new SpoonXMLRPCException('This isn\'t a valid error-array.');

		// set properties
		$this->error = (array) $aError;
		$this->isError = true;
	}


	/**
	 * Set the headers
	 *
	 * @return	void
	 * @param	string $headers
	 */
	private function setHeaders($headers)
	{
		$this->headers = (string) $headers;
	}


	/**
	 * Sets the raw response
	 *
	 * @return	void
	 * @param	string $response
	 */
	private function setResponse($response)
	{
		$this->response = (string) $response;
	}

}

?>