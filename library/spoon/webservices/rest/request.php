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
 * @author			Davy Hellemans <davy@netlash.com>
 * @author 			Tijs Verkoyen <tijs@netlash.com>
 * @since			1.0.0
 */


/** Spoon class */
require_once 'spoon/spoon.php';

/** SpoonRESTException class */
require_once 'spoon/webservices/rest/exception.php';

/** SpoonRESTResponse class */
require_once 'spoon/webservices/rest/response.php';


/**
 * This base class provides all the methods used by a REST-request.
 *
 * @package			webservices
 * @subpackage		rest
 *
 * @author 			Tijs Verkoyen <tijs@netlash.com>
 * @since			1.0.0
 */
class SpoonRESTRequest
{
	/**
	 * Host
	 *
	 * @var	string
	 */
	private $host;


	/**
	 * Method
	 *
	 * @var	method
	 */
	private $method;


	/**
	 * Parameters
	 *
	 * @var	array
	 */
	private $parameters = array();


	/**
	 * Path
	 *
	 * @var	string
	 */
	private $path;


	/**
	 * Portnumber
	 *
	 * @var	int
	 */
	private $port = 80;


	/**
	 * Timeout
	 *
	 * @var	int
	 */
	private $timeOut = 10;


	/**
	 * UserAgent
	 *
	 * @var	string
	 */
	private $userAgent;


	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param	string $method
	 * @param	array[optional] $parameters
	 */
	public function __construct($url, $method, $parameters = array(), $port = 80)
	{
		// set properties
		$this->setUrl($url);
		$this->setMethod($method);
		$this->setPort($port);

		// add parameters
		foreach ((array) $parameters as $key => $value) $this->addParameter($key, $value);
	}


	/**
	 * Adds a parameter
	 *
	 * @return	void
	 * @param	array $parameter
	 */
	public function addParameter($key, $value)
	{
		$this->parameters[$key] = $value;
	}


	/**
	 * Makes the call
	 *
	 * @return	mixed
	 */
	public function execute()
	{
		// init vars
		$parameters = $this->getParameters();
		$parameterString = '';
		$queryString = '/'. $this->path;
		$response = '';

		// get body
		$requestBody = '';

		foreach ($parameters as $key => $value) $parameterString .= '&'. $key .'='. $value;
		$parameterString = trim($parameterString, '&');

		if($this->method == 'GET') $queryString .= '?'. $parameterString;
		elseif ($this->method == 'POST') $requestBody .= ''. $parameterString;
		else throw new SpoonRESTException('Invalid method ('. $this->method .').');

		// build headers
		$requestHeaders = strtoupper($this->getMethod()) .' '. $queryString .' HTTP/1.0'."\r\n";
		$requestHeaders .= 'User-Agent: '. $this->getUserAgent()  ."\r\n";
		$requestHeaders .= 'Host: '. $this->host ."\r\n";

		// add post if needed
		if($this->method == 'POST') $requestHeaders .= 'Content-Type: application/x-www-form-urlencoded'."\r\n";

		// build headers
		$requestHeaders .= 'Content-length: '. strlen($requestBody) ."\r\n";
		$requestHeaders .= "\r\n";

		// create socket
		$null = null;
		$socket = @fsockopen($this->host, $this->getPort(), &$null, &$null, $this->timeOut);
		if($socket === false) throw new SpoonRESTException('The client couldn\'t connect.');

		// write to socket
		@fwrite($socket, $requestHeaders.$requestBody);

		// read response
		while (!feof($socket)) $response .= @fread($socket, 8192);

		@fclose($socket);

		// create new response
		return new SpoonRESTResponse($response);
	}


	/**
	 * Get status for include response header
	 *
	 * @return	bool
	 */
	public function getIncludeResponseHeaders()
	{
		return $this->includeResponseHeaders;
	}


	/**
	 * Get method
	 *
	 * @return	string
	 */
	public function getMethod()
	{
		return (string) $this->method;
	}


	/**
	 * Gets the parameterlist
	 *
	 * @return	array
	 */
	public function getParameters()
	{
		return (array) $this->parameters;
	}


	/**
	 * Get port
	 *
	 * @return	int
	 */
	public function getPort()
	{
		return (int) $this->port;
	}


	/**
	 * Get url
	 *
	 * @return	string
	 */
	public function getUrl()
	{
		return (string) $this->url;
	}


	/**
	 * Get useragent
	 *
	 * @return	string
	 */
	public function getUserAgent()
	{
		if($this->userAgent == '') return 'Spoon '. SPOON_VERSION . 'REST client';
		return $this->userAgent;
	}


	/**
	 * Set include response headers
	 *
	 * @return	void
	 * @param	bool[optional] $includeHeaders
	 */
	public function setIncludeResponseHeaders($includeHeaders = false)
	{
		$this->includeResponseHeaders = (bool) $includeHeaders;
	}


	/**
	 * Set the method
	 *
	 * @param	string$method
	 */
	private function setMethod($method)
	{
		// possible methods
		$aPossibleMethods = array('GET', 'POST');

		// redefine var
		$method = (string) strtoupper($method);

		// validate
		if(!in_array($method, $aPossibleMethods)) throw new SpoonRESTException('This method ('. $method .') is not allowed. Only '. join(', ', $aPossibleMethods) .' are allowed.');

		// set property
		$this->method = $method;
	}


	/**
	 * Set the port
	 *
	 * @return	void
	 * @param	int $port
	 */
	private function setPort($port)
	{
		$this->port = (int) $port;
	}


	/**
	 * Set timeout
	 *
	 * @return	void
	 * @param	int $seconds
	 */
	public function setTimeOut($seconds)
	{
		$this->timeOut = (int) $seconds;
	}


	/**
	 * Set the url
	 *
	 * @return	void
	 * @param	string $url
	 */
	private function setUrl($url)
	{
		// redefine var
		$url = (string) $url;

		// strip protocol
		$parts = split('//', $url, 2);

		if(count($parts) != 2) $serverAddress = $parts[0];
		else $serverAddress = $parts[1];

		// get requestUri
		$parts = split('/', $serverAddress);

		// set properties
		$this->host = $parts[0];
		array_shift($parts);
		$this->path = join('/', $parts);
	}


	/**
	 * Set useragent
	 *
	 * @return	vo
	 * @param	string $userAgent
	 */
	public function setUserAgent($userAgent)
	{
		$this->userAgent = (string) $userAgent;
	}

}

?>