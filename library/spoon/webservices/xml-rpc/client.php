<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package			webservices
 * @subpackage		xml-rpc
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author 			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			1.0.0
 */


/** Spoon class */
require_once 'spoon/spoon.php';

/** SpoonXMLRPCException class */
require_once 'spoon/webservices/xml-rpc/exception.php';

/** SpoonXMLRPCRequest class */
require_once 'spoon/webservices/xml-rpc/request.php';

/** SpoonXMLRPCResponse class */
require_once 'spoon/webservices/xml-rpc/response.php';


/**
 * This base class provides all the methods used by a XML-RPC-client.
 *
 * @package			webservices
 * @subpackage		xml-rpc
 *
 * @author 			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			1.0.0
 */
class SpoonXMLRPCClient
{
	/**
	 * The request uri
	 *
	 * @var	string
	 */
	private $requestURI = '';


	/**
	 * The Serverport
	 *
	 * @var	int
	 */
	private $serverPort = 80;


	/**
	 * The server address
	 *
	 * @var	string
	 */
	private $serverAddress = null;


	/**
	 * The user-agent
	 *
	 * @var	string
	 */
	private $userAgent = 'Spoon XML-RPC client';


	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param	string[optional] $serverAddress
	 */
	public function __construct($url, $port = 80)
	{
		$this->processServerURL($url);
		$this->setServerPort($port);
	}


	/**
	 * Makes the call
	 *
	 * @return	SpoonXMLRPCResponse
	 * @param	string $methodName
	 * @param	array $parameters
	 */
	public function execute($methodName, $parameters = array())
	{
		// create SpoonXMLRPCRequest object
		$request = new SpoonXMLRPCRequest($methodName, $parameters);
		$requestBody = $request->buildXML();

		// create curl
		$curl = curl_init();

		// set useragent
		curl_setopt($curl, CURLOPT_USERAGENT, $this->getUserAgent());

		// set options
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// set url
		curl_setopt($curl, CURLOPT_URL, $this->getServerAddress() . $this->getRequestURI());

		// set body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $requestBody);

		// get response
		$response = curl_exec($curl);

		// get errors
		$errorNumber = (int) curl_errno($curl);
		$errorString = curl_error($curl);

		// close
		curl_close($curl);

		// catch errors
		if($errorNumber != 0)
		{
			// create response
			$response = new SpoonXMLRPCResponse('');
			$response->setError(array('code' => $errorNumber, 'message' => $errorString));

			// return
			return $response;
		}

		// create SpoonXMLRPCResponse object
		$response = new SpoonXMLRPCResponse($response);

		// return
		return $response;
	}


	/**
	 * Gets the requestURI
	 *
	 * @return	string
	 */
	public function getRequestURI()
	{
		return $this->requestURI;
	}


	/**
	 * Gets the serverport
	 *
	 * @return	int
	 */
	public function getServerPort()
	{
		return $this->serverPort;
	}


	/**
	 * Gets the server address
	 *
	 * @return	string
	 */
	public function getServerAddress()
	{
		return $this->serverAddress;
	}


	/**
	 * Gets the useragent
	 *
	 * @return	string
	 */
	public function getUserAgent()
	{
		return $this->userAgent;
	}


	/**
	 * Sets the requestURI
	 *
	 * @return	void
	 * @param	string $uri
	 */
	private function setRequestURI($uri)
	{
		$this->requestURI = (string) $uri;
	}


	/**
	 * Process server url
	 *
	 * @return	void
	 * @param	string $url
	 */
	private function processServerURL($url)
	{
		// strip protocol
		$parts = split('//', $url, 2);

		if(count($parts) != 2) $serverAddress = $parts[0];
		else $serverAddress = $parts[1];

		// get requestUri
		$parts = split('/', $serverAddress);

		// set serveraddress
		$this->setServerAddress($parts[0]);

		// set requestURI
		array_shift($parts);
		$this->setRequestURI('/'. join('/', $parts));

	}


	/**
	 * Sets the serverport
	 *
	 * @return	void
	 * @param	int $port
	 */
	private function setServerPort($port)
	{
		$this->serverPort = (int) $port;
	}


	/**
	 * Sets the server address
	 *
	 * @return	void
	 * @param	string $serverAddress
	 */
	private function setServerAddress($serverAddress)
	{
		$this->serverAddress = (string) $serverAddress;
	}


	/**
	 * Sets the useragent
	 *
	 * @return	void
	 * @param	string[optional] $userAgent
	 */
	public function setUserAgent($userAgent = 'Spoon XML-RPC client')
	{
		$this->userAgent = (string) $userAgent;
	}
}

?>