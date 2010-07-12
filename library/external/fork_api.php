<?php
/**
 * ForkAPI class
 *
 * This source file can be used to communicate with ForkAPI (http://api.fork-cms.be)
 *
 * The class is documented in the file itself. If you find any bugs help me out and report them. Reporting can be done by sending an email to php-fork-api-bugs[at]verkoyen[dot]eu.
 * If you report a bug, make sure you give me enough information (include your code).
 *
 *
 * License
 * Copyright (c) 2010, Tijs Verkoyen. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products derived from this software without specific prior written permission.
 *
 * This software is provided by the author "as is" and any express or implied warranties, including, but not limited to, the implied warranties of merchantability and fitness for a particular purpose are disclaimed. In no event shall the author be liable for any direct, indirect, incidental, special, exemplary, or consequential damages (including, but not limited to, procurement of substitute goods or services; loss of use, data, or profits; or business interruption) however caused and on any theory of liability, whether in contract, strict liability, or tort (including negligence or otherwise) arising in any way out of the use of this software, even if advised of the possibility of such damage.
 *
 * @author			Tijs Verkoyen <php-fork-api@verkoyen.eu>
 * @version			1.0.1
 *
 * @copyright		Copyright (c) 2008, Tijs Verkoyen. All rights reserved.
 * @license			BSD License
 */
class ForkAPI
{
	// internal constant to enable/disable debugging
	const DEBUG = false;

	// url for the fork-api
	const API_URL = 'http://api.fork-cms.be';

	// port for the fork-API
	const API_PORT = 80;

	// Fork-API version
	const API_VERSION = '2.0';

	// current version
	const VERSION = '1.0.0';


	/**
	 * The public key to use
	 *
	 * @var	string
	 */
	private $publicKey;


	/**
	 * The private key
	 *
	 * @var	string
	 */
	private $privateKey;


	/**
	 * The timeout
	 *
	 * @var	int
	 */
	private $timeOut = 20;


	/**
	 * The user agent
	 *
	 * @var	string
	 */
	private $userAgent;


// class methods
	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param	string[optional] $publicKey		The public-key of the keypair
	 * @param	string[optional] $privateKey	The private-key of the keypair
	 */
	public function __construct($publicKey = null, $privateKey = null)
	{
		if($publicKey !== null) $this->setPublicKey($publicKey);
		if($publicKey !== null) $this->setPrivateKey($privateKey);
	}


	/**
	 * Make the call
	 *
	 * @return	string
	 * @param	string $url
	 * @param	array[optional] $aParameters
	 */
	private function doCall($method, $aParameters = array(), $authenticate = true)
	{
		// redefine
		$method = (string) $method;
		$aParameters = (array) $aParameters;
		$authenticate = (bool) $authenticate;

		// add required parameters
		$aParameters['method'] = (string) $method;

		// authentication stuff
		if($authenticate)
		{
			// get keys
			$publicKey = $this->getPublicKey();
			$privateKey = $this->getPrivateKey();

			// validate
			if($publicKey == '' || $privateKey == '') throw new ForkAPIException('This method ('. $method .') requires authentication, provide a public and private key.');

			// add prams
			$aParameters['public_key'] = $publicKey;
			$aParameters['nonce'] = time();
			$aParameters['secret'] = md5($publicKey . $privateKey . $aParameters['nonce']);
		}

		// init var
		$queryString = '';

		// loop parameters and add them to the queryString
		foreach($aParameters as $key => $value) $queryString .= '&'. $key .'='. urlencode(utf8_encode($value));

		// cleanup querystring
		$queryString = trim($queryString, '&');

		// prepend
		$url = self::API_URL .'/'. self::API_VERSION .'/rest.php?'. $queryString;

		// set options
		$options[CURLOPT_URL] = $url;
		$options[CURLOPT_PORT] = self::API_PORT;
		$options[CURLOPT_USERAGENT] = $this->getUserAgent();
		$options[CURLOPT_FOLLOWLOCATION] = true;
		$options[CURLOPT_RETURNTRANSFER] = true;
		$options[CURLOPT_TIMEOUT] = (int) $this->getTimeOut();

		// init
		$curl = curl_init();

		// set options
		curl_setopt_array($curl, $options);

		// execute
		$response = curl_exec($curl);
		$headers = curl_getinfo($curl);

		// fetch errors
		$errorNumber = curl_errno($curl);
		$errorMessage = curl_error($curl);

		// close
		curl_close($curl);

		// we expect XML so decode it
		$xml = @simplexml_load_string($response, null, LIBXML_NOCDATA);

		// validate XML
		if($xml === false) throw new ForkAPIException('Invalid XML-response.');

		// is error?
		if(!isset($xml['status']) || (string) $xml['status'] != 'ok')
		{
			// is it a response error
			if(isset($xml->error)) throw new ForkAPIException((string) $xml->error);

			// invalid json?
			else throw new ForkAPIException('Invalid XML-response.');
		}

		// return
		return $xml;
	}


	/**
	 * Get the public key
	 *
	 * @return	string
	 */
	private function getPublicKey()
	{
		return (string) $this->publicKey;
	}


	/**
	 * Get the private key
	 *
	 * @return	string
	 */
	private function getPrivateKey()
	{
		return (string) $this->privateKey;
	}


	/**
	 * Get the timeout that will be used
	 *
	 * @return	int
	 */
	public function getTimeOut()
	{
		return (int) $this->timeOut;
	}


	/**
	 * Get the useragent that will be used. Our version will be prepended to yours.
	 * It will look like: "PHP ForkAPI/<version> <your-user-agent>"
	 *
	 * @return	string
	 */
	public function getUserAgent()
	{
		return (string) 'PHP ForkAPI/'. self::VERSION .' '. $this->userAgent;
	}


	/**
	 * Set the public key
	 *
	 * @return	void
	 * @param	string $key
	 */
	public function setPublicKey($key)
	{
		$this->publicKey = (string) $key;
	}


	/**
	 * Set the private key
	 *
	 * @return	void
	 * @param	string $key
	 */
	public function setPrivateKey($key)
	{
		$this->privateKey = (string) $key;
	}


	/**
	 * Set the timeout
	 * After this time the request will stop. You should handle any errors triggered by this.
	 *
	 * @return	void
	 * @param	int $seconds	The timeout in seconds
	 */
	public function setTimeOut($seconds)
	{
		$this->timeOut = (int) $seconds;
	}


	/**
	 * Set the user-agent for you application
	 * It will be appended to ours, the result will look like: "PHP ForkAPI/<version> <your-user-agent>"
	 *
	 * @return	void
	 * @param	string $userAgent	Your user-agent, it should look like <app-name>/<app-version>
	 */
	public function setUserAgent($userAgent)
	{
		$this->userAgent = (string) $userAgent;
	}


// core methods

	/**
	 * Request public private key-pair
	 *
	 * @return	array
	 * @param	string $siteUrl
	 * @param	string $email
	 */
	public function coreRequestKeys($siteUrl, $email)
	{
		// build parameters
		$parameters['site_url'] = (string) $siteUrl;
		$parameters['email'] = (string) $email;

		// make the call
		$response = $this->doCall('core.requestKeys', $parameters, false);

		// init var
		$return = array();

		// validate response
		if(!isset($response->keys->public)) throw new ForkAPIException('Invalid XML-response.');
		if(!isset($response->keys->private)) throw new ForkAPIException('Invalid XML-response.');

		// loop services
		$return['public'] = (string) $response->keys->public;
		$return['private'] = (string) $response->keys->private;

		// return
		return $return;
	}



// message methods

	/**
	 * Get messages from the server
	 *
	 * @return	array
	 */
	public function messagesGet()
	{
		// make the call
		$response = $this->doCall('messages.get');

		// init var
		$return = array();

		// validate response
		if(!isset($response->messages->message)) throw new ForkAPIException('Invalid XML-response.');

		// loop services
		foreach($response->messages->message as $message)
		{
			// add into array
			$return[] = array(	'id' => (string) $message['id'],
								'sent_on' => (int) strtotime((string) $message['sent_on']),
								'subject' => (string) $message->subject,
								'body' => (string) $message->body
							);
		}

		// return
		return $return;
	}


// ping methods

	/**
	 * Get the ping services
	 *
	 * @return	array
	 */
	public function pingGetServices()
	{
		// make the call
		$response = $this->doCall('ping.getServices');

		// init var
		$return = array();

		// validate response
		if(!isset($response->services->service)) throw new ForkAPIException('Invalid XML-response.');

		// loop services
		foreach($response->services->service as $service) $return[] = array('url' => (string) $service['url'], 'port' => (int) $service['port'], 'type' => (string) $service['type']);

		// return
		return $return;
	}
}


/**
 * ForkAPI Exception class
 *
 * @author	Tijs Verkoyen <php-fork-api@verkoyen.eu>
 */
class ForkAPIException extends Exception
{
}

?>