<?php

/**
 * Akismet class
 *
 * This source file can be used to communicate with Akismet (http://akismet.com)
 *
 * The class is documented in the file itself. If you find any bugs help me out and report them. Reporting can be done by sending an email to php-akismet-bugs[at]verkoyen[dot]eu.
 * If you report a bug, make sure you give me enough information (include your code).
 *
 * Changelog since 1.0.6
 * - implemented the new styleguide
 *
 * Changelog since 1.0.5
 * - implemented the new styleguide
 *
 * Changelog since 1.0.4
 * - fallback for when IP isn't available
 * - codestyling
 *
 * Changelog since 1.0.3
 * - added a check for safe-mode
 * - fixed some code-styling issues
 *
 * Changelog since 1.0.2
 * - when authenticating the key will be validate if it is not empty
 *
 * Changelog since 1.0.1
 * - fixed some comments
 *
 * Changelog since 1.0.0
 * - some fields (blog) don't have to be urlencoded
 * - submitHam and submitSpam return a boolean instead of void. When an error occors it will still throw an exception
 *
 * License
 * Copyright (c) Tijs Verkoyen. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products derived from this software without specific prior written permission.
 *
 * This software is provided by the author "as is" and any express or implied warranties, including, but not limited to, the implied warranties of merchantability and fitness for a particular purpose are disclaimed. In no event shall the author be liable for any direct, indirect, incidental, special, exemplary, or consequential damages (including, but not limited to, procurement of substitute goods or services; loss of use, data, or profits; or business interruption) however caused and on any theory of liability, whether in contract, strict liability, or tort (including negligence or otherwise) arising in any way out of the use of this software, even if advised of the possibility of such damage.
 *
 * @author Tijs Verkoyen <php-akismet@verkoyen.eu>
 * @version 1.0.7
 * @copyright Copyright (c) Tijs Verkoyen. All rights reserved.
 * @license BSD License
 */
class Akismet
{
	// internal constant to enable/disable debugging
	const DEBUG = false;

	// url for the api
	const API_URL = 'http://rest.akismet.com';

	// port for the api
	const API_PORT = 80;

	// version of the api
	const API_VERSION = '1.1';

	// current version
	const VERSION = '1.0.7';

	/**
	 * The key for the API
	 *
	 * @var string
	 */
	private $apiKey;

	/**
	 * The timeout
	 *
	 * @var int
	 */
	private $timeOut = 60;

	/**
	 * The user agent
	 *
	 * @var string
	 */
	private $userAgent;

	/**
	 * The url
	 *
	 * @var string
	 */
	private $url;

	// class methods
	/**
	 * Default constructor
	 * Creates an instance of the Akismet Class.
	 *
	 * @param string $apiKey	API key being verified for use with the API.
	 * @param string $url		The front page or home URL of the instance making the request. For a blog or wiki this would be the front page. Note: Must be a full URI, including http://.
	 */
	public function __construct($apiKey, $url)
	{
		$this->setApiKey($apiKey);
		$this->setUrl($url);
	}

	/**
	 * Make the call
	 *
	 * @param string $url 					URL to call.
	 * @param array[optional] $aParameters	The parameters to pass.
	 * @param bool[optional] $authenticate	Should we authenticate?
	 * @return string
	 */
	private function doCall($url, $aParameters = array(), $authenticate = true)
	{
		// redefine
		$url = (string) $url;
		$aParameters = (array) $aParameters;
		$authenticate = (bool) $authenticate;

		// build url
		$url = self::API_URL . '/' . self::API_VERSION . '/' . $url;

		// add key in front of url
		if($authenticate)
		{
			// get api key
			$apiKey = $this->getApiKey();

			// validate apiKey
			if($apiKey == '') throw new AkismetException('Invalid API-key');

			// prepend key
			$url = str_replace('http://', 'http://' . $apiKey . '.', $url);
		}

		// add url into the parameters
		$aParameters['blog'] = $this->getUrl();

		// set options
		$options[CURLOPT_URL] = $url;
		$options[CURLOPT_PORT] = self::API_PORT;
		$options[CURLOPT_USERAGENT] = $this->getUserAgent();
		if(ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) $options[CURLOPT_FOLLOWLOCATION] = true;
		$options[CURLOPT_RETURNTRANSFER] = true;
		$options[CURLOPT_TIMEOUT] = (int) $this->getTimeOut();
		$options[CURLOPT_POST] = true;
		$options[CURLOPT_POSTFIELDS] = $aParameters;

		// speed up things, use HTTP 1.0
		$options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_0;

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

		// invalid headers
		if(!in_array($headers['http_code'], array(0, 200)))
		{
			// should we provide debug information
			if(self::DEBUG)
			{
				// make it output proper
				echo '<pre>';

				// dump the header-information
				var_dump($headers);

				// dump the raw response
				var_dump($response);

				// end proper format
				echo '</pre>';

				// stop the script
				exit();
			}

			// throw error
			throw new AkismetException(null, (int) $headers['http_code']);
		}

		// error?
		if($errorNumber != '') throw new AkismetException($errorMessage, $errorNumber);

		// return
		return $response;
	}

	/**
	 * Get the API-key that will be used
	 *
	 * @return string
	 */
	private function getApiKey()
	{
		return (string) $this->apiKey;
	}

	/**
	 * Get the timeout that will be used
	 *
	 * @return int
	 */
	public function getTimeOut()
	{
		return (int) $this->timeOut;
	}

	/**
	 * Get the url of the instance making the request
	 *
	 * @return string
	 */
	public function getUrl()
	{
		return (string) $this->url;
	}

	/**
	 * Get the useragent that will be used.
	 * Our version will be prepended to yours.
	 * It will look like: "PHP Akismet/<version> <your-user-agent>"
	 *
	 * @return string
	 */
	public function getUserAgent()
	{
		return (string) 'PHP Akismet/' . self::VERSION . ' ' . $this->userAgent;
	}

	/**
	 * Set API key that has to be used
	 *
	 * @param string $apiKey	API key to use.
	 */
	private function setApiKey($apiKey)
	{
		$this->apiKey = (string) $apiKey;
	}

	/**
	 * Set the timeout
	 * After this time the request will stop.
	 * You should handle any errors triggered by this.
	 *
	 * @param int $seconds	The timeout in seconds.
	 */
	public function setTimeOut($seconds)
	{
		$this->timeOut = (int) $seconds;
	}

	/**
	 * Set the url of the instance making the request
	 *
	 * @param string $url	The URL making the request.
	 */
	private function setUrl($url)
	{
		$this->url = (string) $url;
	}

	/**
	 * Set the user-agent for you application
	 * It will be appended to ours, the result will look like: "PHP Akismet/<version> <your-user-agent>"
	 *
	 * @param string $userAgent		The user-agent, it should look like <app-name>/<app-version>.
	 */
	public function setUserAgent($userAgent)
	{
		$this->userAgent = (string) $userAgent;
	}

	// api methods
	/**
	 * Verifies the key
	 *
	 * @return bool		if the key is valid it will return true, otherwise false will be returned.
	 */
	public function verifyKey()
	{
		// possible answers
		$aPossibleResponses = array('valid', 'invalid');

		// build parameters
		$aParameters['key'] = $this->getApiKey();

		// make the call
		$response = $this->doCall('verify-key', $aParameters, false);

		// validate response
		if(!in_array($response, $aPossibleResponses)) throw new AkismetException($response, 400);

		// valid key
		if($response == 'valid') return true;

		// fallback
		return false;
	}

	/**
	 * Check if the comment is spam or not
	 * This is basically the core of everything.
	 * This call takes a number of arguments and characteristics about the submitted content and then returns a thumbs up or thumbs down.
	 * Almost everything is optional, but performance can drop dramatically if you exclude certain elements.
	 * REMARK: If you are having trouble triggering you can send "viagra-test-123" as the author and it will trigger a true response, always.
	 *
	 * @param string[optional] $content		The content that was submitted.
	 * @param string[optional] $author		The name.
	 * @param string[optional] $email		The email address.
	 * @param string[optional] $url			The URL.
	 * @param string[optional] $permalink	The permanent location of the entry the comment was submitted to.
	 * @param string[optional] $type		The type, can be blank, comment, trackback, pingback, or a made up value like "registration".
	 * @return bool 						If the comment is spam true will be returned, otherwise false.
	 */
	public function isSpam($content, $author = null, $email = null, $url = null, $permalink = null, $type = null)
	{
		// possible answers
		$aPossibleResponses = array('true', 'false');

		// redefine
		$content = (string) $content;
		$author = (string) $author;
		$email = (string) $email;
		$url = (string) $url;
		$permalink = (string) $permalink;
		$type = (string) $type;

		// get stuff from the $_SERVER-array
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		elseif(isset($_SERVER['REMOTE_ADDR'])) $ip = $_SERVER['REMOTE_ADDR'];
		else $ip = '';
		$userAgent = (isset($_SERVER['HTTP_USER_AGENT'])) ? (string) $_SERVER['HTTP_USER_AGENT'] : '';
		$referrer = (isset($_SERVER['HTTP_REFERER'])) ? (string) $_SERVER['HTTP_REFERER'] : '';

		// build parameters
		$aParameters['user_ip'] = $ip;
		$aParameters['user_agent'] = $userAgent;
		if($referrer != '') $aParameters['referrer'] = $referrer;
		if($permalink != '') $aParameters['permalink'] = $permalink;
		if($type != '') $aParameters['comment_type'] = $type;
		if($author != '') $aParameters['comment_author'] = $author;
		if($email != '') $aParameters['comment_author_email'] = $email;
		if($url != '') $aParameters['comment_author_url'] = $url;
		$aParameters['comment_content'] = $content;

		// add all stuff from $_SERVER
		foreach($_SERVER as $key => $value)
		{
			// keys to ignore
			$aKeysToIgnore = array('HTTP_COOKIE', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED_HOST', 'HTTP_MAX_FORWARDS', 'HTTP_X_FORWARDED_SERVER', 'REDIRECT_STATUS', 'SERVER_PORT', 'PATH', 'DOCUMENT_ROOT', 'SERVER_ADMIN', 'QUERY_STRING', 'PHP_SELF', 'argv', 'argc', 'SCRIPT_FILENAME', 'SCRIPT_NAME');

			// add to parameters if not in ignore list
			if(!in_array($key, $aKeysToIgnore)) $aParameters[$key] = $value;
		}

		// make the call
		$response = $this->doCall('comment-check', $aParameters);

		// validate response
		if(!in_array($response, $aPossibleResponses)) throw new AkismetException($response, 400);

		// process response
		if($response == 'true') return true;

		// fallback
		return false;
	}

	/**
	 * Submit ham to Akismet
	 * This call is intended for the marking of false positives, things that were incorrectly marked as spam.
	 *
	 * @param string $userIp				The address of the comment submitter.
	 * @param string $userAgent				The agent information.
	 * @param string[optional] $content		The content that was submitted.
	 * @param string[optional] $author		The name of the author.
	 * @param string[optional] $email		The email address.
	 * @param string[optional] $url			The URL.
	 * @param string[optional] $permalink	The permanent location of the entry the comment was submitted to.
	 * @param string[optional] $type		The type, can be blank, comment, trackback, pingback, or a made up value like "registration".
	 * @param string[optional] $referrer	The content of the HTTP_REFERER header should be sent here.
	 * @param array[optional] $others		Extra data (the variables from $_SERVER).
	 * @return bool 						If everything went fine true will be returned, otherwise an exception will be triggered.
	 */
	public function submitHam($userIp, $userAgent, $content, $author = null, $email = null, $url = null, $permalink = null, $type = null, $referrer = null, $others = null)
	{
		// possible answers
		$aPossibleResponses = array('Thanks for making the web a better place.');

		// redefine
		$userIp = (string) $userIp;
		$userAgent = (string) $userAgent;
		$content = (string) $content;
		$author = (string) $author;
		$email = (string) $email;
		$url = (string) $url;
		$permalink = (string) $permalink;
		$type = (string) $type;
		$referrer = (string) $referrer;
		$others = (array) $others;

		// build parameters
		$aParameters['user_ip'] = $userIp;
		$aParameters['user_agent'] = $userAgent;
		if($referrer != '') $aParameters['referrer'] = $referrer;
		if($permalink != '') $aParameters['permalink'] = $permalink;
		if($type != '') $aParameters['comment_type'] = $type;
		if($author != '') $aParameters['comment_author'] = $author;
		if($email != '') $aParameters['comment_author_email'] = $email;
		if($url != '') $aParameters['comment_author_url'] = $url;
		$aParameters['comment_content'] = $content;

		// add other parameters
		foreach($others as $key => $value) $aParameters[$key] = $value;

			// make the call
		$response = $this->doCall('submit-ham', $aParameters);

		// validate response
		if(in_array($response, $aPossibleResponses)) return true;

		// fallback
		throw new AkismetException($response);
	}

	/**
	 * Submit spam to Akismet
	 * This call is for submitting comments that weren't marked as spam but should have been.
	 *
	 * @param string $userIp				The address of the comment submitter.
	 * @param string $userAgent				The agent information.
	 * @param string[optional] $content		The content that was submitted.
	 * @param string[optional] $author		The name of the author.
	 * @param string[optional] $email		The email address.
	 * @param string[optional] $url			The URL.
	 * @param string[optional] $permalink	The permanent location of the entry the comment was submitted to.
	 * @param string[optional] $type		The type, can be blank, comment, trackback, pingback, or a made up value like "registration".
	 * @param string[optional] $referrer	The content of the HTTP_REFERER header should be sent here.
	 * @param array[optional] $others		Extra data (the variables from $_SERVER).
	 * @return bool 						If everything went fine true will be returned, otherwise an exception will be triggered.
	 */
	public function submitSpam($userIp, $userAgent, $content, $author = null, $email = null, $url = null, $permalink = null, $type = null, $referrer = null, $others = null)
	{
		// possible answers
		$aPossibleResponses = array('Thanks for making the web a better place.');

		// redefine
		$userIp = (string) $userIp;
		$userAgent = (string) $userAgent;
		$content = (string) $content;
		$author = (string) $author;
		$email = (string) $email;
		$url = (string) $url;
		$permalink = (string) $permalink;
		$type = (string) $type;
		$referrer = (string) $referrer;
		$others = (array) $others;

		// build parameters
		$aParameters['user_ip'] = $userIp;
		$aParameters['user_agent'] = $userAgent;
		if($referrer != '') $aParameters['referrer'] = $referrer;
		if($permalink != '') $aParameters['permalink'] = $permalink;
		if($type != '') $aParameters['comment_type'] = $type;
		if($author != '') $aParameters['comment_author'] = $author;
		if($email != '') $aParameters['comment_author_email'] = $email;
		if($url != '') $aParameters['comment_author_url'] = $url;
		$aParameters['comment_content'] = $content;

		// add other parameters
		foreach($others as $key => $value) $aParameters[$key] = $value;

			// make the call
		$response = $this->doCall('submit-spam', $aParameters);

		// validate response
		if(in_array($response, $aPossibleResponses)) return true;

		// fallback
		throw new AkismetException($response);
	}
}

/**
 * Akismet Exception class
 *
 * @author Tijs Verkoyen <php-akismet@verkoyen.eu>
 */
class AkismetException extends Exception
{
	/**
	 * Http header-codes
	 *
	 * @var array
	 */
	private $aStatusCodes = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		301 => 'Status code is received in response to a request other than GET or HEAD, the user agent MUST NOT automatically redirect the request unless it can be confirmed by the user, since this might change the conditions under which the request was issued.',
		302 => 'Found',
		302 => 'Status code is received in response to a request other than GET or HEAD, the user agent MUST NOT automatically redirect the request unless it can be confirmed by the user, since this might change the conditions under which the request was issued.',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		306 => '(Unused)',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported'
	);

	/**
	 * Default constructor
	 *
	 * @param $message string[optional]		message.
	 * @param $code int[optional]			error number.
	 */
	public function __construct($message = null, $code = null)
	{
		// set message
		if($message === null && isset($this->aStatusCodes[(int) $code])) $message = $this->aStatusCodes[(int) $code];

		// call parent
		parent::__construct((string) $message, $code);
	}
}