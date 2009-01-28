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

/** SpoonRESTResponse class */
require_once 'spoon/webservices/rest/response.php';


/**
 * This base class provides all the methods used by a REST-request.
 *
 * @package			webservices
 * @subpackage		rest
 *
 * @author 			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			1.0.0
 */
class SpoonRESTRequest
{
	/**
	 * Should we authenticate?
	 *
	 * @var	bool
	 */
	private $authenticate = false;


	/**
	 * Host
	 *
	 * @var	string
	 */
	private $host;


	/**
	 * username
	 *
	 * @var	string
	 */
	private $login;


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
	 * password
	 *
	 * @var	string
	 */
	private $password;


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

		if($this->getMethod() == 'GET') $queryString .= '?'. $parameterString;
		elseif ($this->method == 'POST') $requestBody .= ''. $parameterString;
		else throw new SpoonRESTException('Invalid method ('. $this->method .').');

		// create instance
		$curl = curl_init();

		// set options
		curl_setopt($curl, CURLOPT_URL, $this->host . $queryString);
		curl_setopt($curl, CURLOPT_PORT, $this->port);

		// data
		if($this->getMethod() == 'POST')
		{
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $parameterString);
		}

		// authentication
		if($this->authenticate)
		{
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_USERPWD, $this->login .':'. $this->password);
		}

		// set useragent
		curl_setopt($curl, CURLOPT_USERAGENT, $this->getUserAgent());

		// follow redirect
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

		// return data
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, true);

		// exec
		$response = curl_exec($curl);

		$errorNr = curl_errno($curl);
		$errorMessage = curl_error($curl);

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
	 * Set credentials
	 *
	 * @return	void
	 * @param	string $login
	 * @param	string $password
	 */
	public function setCredentials($login, $password)
	{
		$this->login = (string) $login;
		$this->password = (string) $password;
		$this->authenticate = true;
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