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

/** SpoonFilter class  */
require_once 'spoon/filter/filter.php';

/** SpoonRESTRequest class */
require_once 'spoon/webservices/rest/request.php';


/**
 * This base class provides all the methods used by a REST-client.
 *
 * @package			webservices
 * @subpackage		rest
 *
 * @author 			Tijs Verkoyen <tijs@netlash.com>
 * @since			1.0.0
 */
class SpoonRESTClient
{
	/**
	 * The server port
	 *
	 * @var	int
	 */
	private $serverPort = 80;


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
	 * Make the REST-call
	 *
	 * @return	mixed
	 * @param	string $url
	 * @param	string[optional] $method
	 * @param	array[optional] $parameters
	 * @param	int[optional] $port
	 */
	public function execute($url, $method = 'GET', $parameters = array(), $port = 80)
	{
		// init object
		$request = new SpoonRESTRequest($url, $method, $parameters, $port);

		// set options
		$request->setTimeOut($this->getTimeOut());
		$request->setUserAgent($this->getUserAgent());

		// return answer
		return $request->execute();
	}


	/**
	 * Get timeout
	 *
	 * @return	int
	 */
	public function getTimeOut()
	{
		return (int) $this->timeOut;
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
	 * Sets the useragent
	 *
	 * @return	void
	 * @param	string[optional] $userAgent
	 */
	public function setUserAgent($userAgent = null)
	{
		$userAgent = (string) ($userAgent === null) ? 'Spoon '. SPOON_VERSION .'/SpoonRESTclient' : 'Spoon '. SPOON_VERSION .'/'. $userAgent;
		$this->userAgent = $userAgent;
	}


	/**
	 * Set timeout
	 *
	 * @param	int $seconds
	 */
	public function setTimeOut($seconds)
	{
		$this->timeOut = (int) $seconds;
	}

}

?>