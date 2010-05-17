<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package		spoon
 * @subpackage	rest
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @author 		Tijs Verkoyen <tijs@spoon-library.com>
 * @author		Dave Lens <dave@spoon-library.com>
 * @since		1.1.2
 */


/**
 * This base class provides all the methods used by a REST-client.
 *
 * @package		spoon
 * @subpackage	rest
 *
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @since		1.1.1
 */
class SpoonRESTClient
{
	/**
	 * The headers
	 *
	 * @var	array
	 */
	private $headers = array();


	/**
	 * The port
	 *
	 * @var	int
	 */
	private $port = 80;


	/**
	 * The timeout in seconds
	 *
	 * @var	int
	 */
	private $timeout = 10;


	/**
	 * The user-agent
	 *
	 * @var	string
	 */
	private $userAgent;


	/**
	 * Make the call.
	 *
	 * @return	string
	 * @param	string $url
	 * @param	array[optional] $parameters
	 * @param	string[optional] $method
	 */
	public function execute($url, array $parameters = null, $method = 'GET')
	{
		// check if curl is available
		if(!function_exists('curl_init')) throw new SpoonFileException('This method requires cURL (http://php.net/curl), it seems like the extension isn\'t installed.');

		// init var
		$allowedMethods = array('GET', 'POST');

		// redefine
		$url = (string) $url;
		$parameters = (array) $parameters;
		$method = strtoupper((string) $method);

		// validate
		if(!in_array($method, $allowedMethods)) throw new SpoonRESTException('Invalid method ('. $method .'). Possible methods are: '. implode(', ', $allowedMethods).'.');

		// init curl options
		$options[CURLOPT_PORT] = $this->getPort();
		$options[CURLOPT_USERAGENT] = $this->getUserAgent();
		$options[CURLOPT_TIMEOUT] = $this->getTimeout();
		$options[CURLOPT_RETURNTRANSFER] = true;

		// set headers
		$headers = $this->getCustomHeaders();
		if(!empty($headers)) $options[CURLOPT_HTTPHEADER] = $headers;

		// specific when using GET
		if($method == 'GET')
		{
			// init var
			$queryString = '';

			// loop parameters and append them to the url
			foreach($parameters as $key => $value) $queryString .= '&'. $key .'='. urlencode($value);

			// cleanup
			$queryString = trim($queryString, '&');

			// is there really a querystring
			if($queryString != '')
			{
				// find ? in url
				if(strpos($url, '?') > 0) $url .= '&'. $queryString;
				else $url .= '?'. $queryString;
			}
		}

		// specific when using POST
		if($method == 'POST')
		{
			$options[CURLOPT_POST] = true;
			$options[CURLOPT_POSTFIELDS] = $parameters;
		}

		// init curl
		$curl = curl_init($url);

		// set options
		curl_setopt_array($curl, $options);

		// execute
		$response = curl_exec($curl);
		$headers = curl_getinfo($curl);

		// fetch errors
		$errorNumber = curl_errno($curl);
		$errorMessage = curl_error($curl);

		// close curl
		curl_close($curl);

		// validate errors
		if($errorNumber != 0) throw new SpoonRESTException('An error occured with the following message: ('. $errorNumber .')'. $errorMessage .'.');

		// validate headers
		if($headers['http_code'] != 200) throw new SpoonRESTException('Invalid headers, a header with status-code '. $headers['http_code'] .' was returned.');

		// return the response
		return (string) $response;
	}


	/**
	 * Get the headers.
	 *
	 * @return	array
	 */
	public function getCustomHeaders()
	{
		return $this->headers;
	}


	/**
	 * Get the port that will be used.
	 *
	 * @return	int
	 */
	public function getPort()
	{
		return $this->port;
	}


	/**
	 * Get the timeout in seconds that will be used.
	 *
	 * @return	int
	 */
	public function getTimeout()
	{
		return $this->timeout;
	}


	/**
	 * Get the user-agent that will be used. Keep in mind that a spoon header will be prepended.
	 *
	 * @return	string
	 */
	public function getUserAgent()
	{
		// prepend SpoonHeader
		$userAgent = 'Spoon '. SPOON_VERSION .'/';
		$userAgent .= ($this->userAgent === null) ? 'SpoonRESTClient' : $this->userAgent;

		// return
		return $userAgent;
	}


	/**
	 * Add custom headers that will be sent with each request.
	 *
	 * @return	void
	 * @param	array $headers		The header, passed as key-value pairs.
	 */
	public function setCustomHeader(array $headers)
	{
		foreach($headers as $name => $value) $this->headers[(string) $name] = (string) $value;
	}


	/**
	 * Set the port for the REST-server, default is 80.
	 *
	 * @return	void
	 * @param	int $port
	 */
	public function setPort($port)
	{
		$this->port = (int) $port;
	}


	/**
	 * Set timeout.
	 *
	 * @return	void
	 * @param	int $seconds
	 */
	public function setTimeout($seconds)
	{
		$this->timeout = (int) $seconds;
	}


	/**
	 * Set a custom user-agent.
	 *
	 * @return	void
	 * @param	string $userAgent
	 */
	public function setUserAgent($userAgent)
	{
		$this->userAgent = (string) $userAgent;
	}
}


/**
 * This exception is used to handle REST related exceptions.
 *
 * @package		spoon
 * @subpackage	rest
 *
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @since		1.1.1
 */
class SpoonRESTException extends SpoonException {}

?>