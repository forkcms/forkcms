<?php

/**
 * Facebook class
 *
 * This source file can be used to communicate with Facebook (http://facebook.com)
 *
 * The class is documented in the file itself. If you find any bugs help me out and report them. Reporting can be done by sending an email to php-facebook-bugs[at]verkoyen[dot]eu.
 * If you report a bug, make sure you give me enough information (include your code).
 *
 * Changelog since 1.0.0
 * - API-key isn't used anymore.
 * - Removed datefunction, because it isn't used anymore.
 * - Don't use a global curl instance anymore.
 * - Bugfix: when getting the access token a file was submitted.
 * - Implemented signed cookies, thx to Davy Van Vooren.
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
 * @author			Tijs Verkoyen <php-facebook@verkoyen.eu>
 * @version			1.0.1
 *
 * @copyright		Copyright (c) Tijs Verkoyen. All rights reserved.
 * @license			BSD License
 */
class Facebook
{
	// internal constant to enable/disable debugging
	const DEBUG = false;

	// url for the OpenGraph API
	const API_URL = 'https://graph.facebook.com';

	// url for the REST-API
	const REST_API_URL = 'https://api.facebook.com/method';

	// port for the facebook-API
	const API_PORT = 443;

	// current version
	const VERSION = '1.0.1';


	/**
	 * The Application secret
	 *
	 * @var	string
	 */
	private $applicationSecret;


	/**
	 * The Application ID
	 *
	 * @var	string
	 */
	private $applicationId;


	/**
	 * Array with the link between mime-type and extension
	 *
	 * @var	array
	 */
	private $mimeTypes = array(	'default' => 'application/octet-stream',
								'gif' => 'image/gif', 'jpe' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'jpg' => 'image/jpeg', 'png' => 'image/png', 'psd' => 'image/psd', 'tif' => 'image/tiff', 'tiff' => 'image/tiff', 'jp2 ' => 'image/jp2', 'wbmp' => 'image/vnd.wap.wbmp', 'xbm' => 'image/x-xbitmap',
								'3g2' => 'video/3gpp2', '3gpp2' => 'video/3gpp2', '3gpp' => 'video/3gpp', '3gp' => 'video/3gpp', 'asf' => 'video/x-ms-asf', 'avi' => 'video/x-msvideo', 'dat' => 'video/mpeg', 'flv' => 'video/x-flv', 'm4v' => 'video/x-m4v', 'mkv' => 'video/x-matroska', 'mov' => 'video/quicktime', 'mp4' => 'video/mp4', 'mpe' => 'video/mpeg', 'mpeg' => 'video/mpeg', 'mpeg4' => 'video/mpeg', 'mpg' => 'video/mpeg', 'ogm' => 'application/ogg', 'ogv' => 'video/ogg', 'qt' => 'video/quicktime', 'tod' => 'video/mpeg', 'vob' => 'video/dvd', 'wmv' => 'video/x-ms-wmv'
								);


	/**
	 * The token to use for authentication
	 *
	 * @var	string
	 */
	private $token;


	/**
	 * The timeout
	 *
	 * @var	int
	 */
	private $timeOut = 30;


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
	 * @param	string $applicationSecret	The API-secret that has to be used for authentication (see http://facebook.com/developers).
	 * @param	string $applicationId		The Application ID that has to be used for authentication (see http://facebook.com/developers).
	 */
	public function __construct($applicationSecret, $applicationId)
	{
		// set some properties
		$this->setApplicationSecret($applicationSecret);
		$this->setApplicationId($applicationId);
	}


	/**
	 * Make the call (to the OpenGraph API)
	 *
	 * @return	array
	 * @param	string $url							The URL to call.
	 * @param	array $parameters					The parameters that should be passes.
	 * @param	string[optional] $method			Which method should be used?
	 * @param	string[optional] $file				Path to the file that should be posted.
	 * @param	string[optional] $authorization		Should the call authorize itself.
	 */
	private function doCall($url, array $parameters = null, $method = 'GET', $file = null, $authorization = false)
	{
		// redefine
		$url = (string) $url;
		$method = (string) $method;

		// init var
		$queryString = '';

		// through GET
		if($method == 'GET')
		{
			// append to url
			if(!empty($parameters)) $url .= '?' . http_build_query($parameters, null, '&');
		}

		// through POST
		elseif($method == 'POST')
		{
			$options[CURLOPT_POST] = true;
			$options[CURLOPT_POSTFIELDS] = urldecode(http_build_query($parameters, null, '&'));
		}

		// prepend
		$url = self::API_URL . '/' . $url;

		// add token
		if(!$authorization)
		{
			if(strpos($url, '?') != false) $url .= '&access_token=' . $this->getToken();
			else $url .= '?access_token=' . $this->getToken();
		}

		// set options
		$options[CURLOPT_URL] = $url;
		$options[CURLOPT_PORT] = self::API_PORT;
		$options[CURLOPT_USERAGENT] = $this->getUserAgent();
		$options[CURLOPT_FOLLOWLOCATION] = true;
		$options[CURLOPT_SSL_VERIFYPEER] = false;
		$options[CURLOPT_SSL_VERIFYHOST] = false;
		$options[CURLOPT_RETURNTRANSFER] = true;
		$options[CURLOPT_TIMEOUT] = (int) $this->getTimeOut();

		if($file !== null)
		{
			// build a boundary
			$boundary = md5(time());

			// init var
			$content[] = '--' . $boundary;

			// loop parameters and add them
			foreach($parameters as $key => $value) $content[] = 'Content-Disposition: form-data; name="' . $key . '"' . "\r\n\r\n" . $value . "\r\n" . '--' . $boundary;

			// process file
			$fileInfo = pathinfo($file);
			$mimeType = (isset($this->mimeTypes[$fileInfo['extension']])) ? $this->mimeTypes[$fileInfo['extension']] : $this->mimeTypes['default'];
			$fileContent = @file_get_contents($file);

			if($fileContent !== false)
			{
				// set file
				$content[] = 'Content-Disposition: form-data; filename="' . $fileInfo['basename'] . '"' ."\r\n" . 'Content-Type: ' . $mimeType . "\r\n\r\n" . $fileContent ."\r\n--". $boundary;

				// end
				$content[] = array_pop($content) . '--';
				$content = implode("\r\n", $content);

				// build headers
				$header[] = 'Content-Type: multipart/form-data; boundary=' . $boundary;
				$header[] = 'MIME-version: 1.0';
				$header[] = 'Content-Length: ' . strlen($content);

				// set options
				$options[CURLOPT_HTTPHEADER] = $header;
				$options[CURLOPT_POST] = true;
				$options[CURLOPT_POSTFIELDS] = $content;
			}
		}

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

		// error?
		if($errorNumber != '') throw new FacebookException($errorMessage, $errorNumber);

		// authorization is a little different
		if($authorization) return $response;

		// we expect JSON so decode it
		$json = @json_decode($response, true);

		// validate json
		if($json === false) throw new FacebookException('Invalid JSON-response');

		// is error?
		if(isset($json['error']))
		{
			if(self::DEBUG)
			{
				echo '<pre>'."\n";
				var_dump($headers);
				var_dump($json);
				echo '</pre>'."\n";
			}

			// init var
			$type = (isset($json['error']['type'])) ? $json['error']['type'] : '';
			$message = (isset($json['error']['message'])) ? $json['error']['message'] : '';

			// build real message
			if($type != '') $message = trim($type . ': ' . $message);

			// throw error
			throw new FacebookException($message);
		}

		// return
		return $json;
	}


	/**
	 * Make the call (to the REST API)
	 *
	 * @param	string $url						The URL to call.
	 * @param	array[optional] $parameters		The parameters that should be passes.
	 * @param	string[optional] $file			The path to the file to upload
	 */
	private function doRESTAPICall($url, array $parameters = null, $file = null)
	{
		// redefine
		$url = (string) $url;

		// init var
		$queryString = '';

		// append to url
		if($file === null) $url .= '?' . http_build_query($parameters, null, '&');

		// prepend
		$url = self::REST_API_URL . '/' . $url;

		// append access token
		$parameters['access_token'] = $this->getToken();

		// set options
		$options[CURLOPT_URL] = $url;
		$options[CURLOPT_PORT] = self::API_PORT;
		$options[CURLOPT_USERAGENT] = $this->getUserAgent();
		$options[CURLOPT_FOLLOWLOCATION] = true;
		$options[CURLOPT_SSL_VERIFYPEER] = false;
		$options[CURLOPT_SSL_VERIFYHOST] = false;
		$options[CURLOPT_RETURNTRANSFER] = true;
		$options[CURLOPT_TIMEOUT] = (int) $this->getTimeOut();
		$options[CURLOPT_POST] = false;
		$options[CURLOPT_POSTFIELDS] = null;

		if($file !== null)
		{
			// build a boundary
			$boundary = md5(time());

			// init var
			$content[] = '--' . $boundary;

			// loop parameters and add them
			foreach($parameters as $key => $value) $content[] = 'Content-Disposition: form-data; name="' . $key . '"' . "\r\n\r\n" . $value . "\r\n" . '--' . $boundary;

			// process file
			$fileInfo = pathinfo($file);
			$mimeType = (isset($this->mimeTypes[$fileInfo['extension']])) ? $this->mimeTypes[$fileInfo['extension']] : $this->mimeTypes['default'];
			$fileContent = @file_get_contents($file);

			if($fileContent !== false)
			{
				// set file
				$content[] = 'Content-Disposition: form-data; filename="' . $fileInfo['basename'] . '"' ."\r\n" . 'Content-Type: ' . $mimeType . "\r\n\r\n" . $fileContent ."\r\n--". $boundary;

				// end
				$content[] = array_pop($content) . '--';
				$content = implode("\r\n", $content);

				// build headers
				$header[] = 'Content-Type: multipart/form-data; boundary=' . $boundary;
				$header[] = 'MIME-version: 1.0';
				$header[] = 'Content-Length: ' . strlen($content);

				// set options
				$options[CURLOPT_HTTPHEADER] = $header;
				$options[CURLOPT_POST] = true;
				$options[CURLOPT_POSTFIELDS] = $content;
			}
		}

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

		// error?
		if($errorNumber != '') throw new FacebookException($errorMessage, $errorNumber);

		// we expect XML so decode it
		$xml = @simplexml_load_string($response);

		// is error?
		if(isset($xml->error_code))
		{
			throw new FacebookException((string) $xml->error_msg, (int) $xml->error_code);
		}

		// return
		return $xml;
	}


	/**
	 * Get the application id
	 *
	 * @return	string
	 */
	private function getApplicationId()
	{
		return $this->applicationId;
	}


	/**
	 * Get the application secret
	 *
	 * @return	string
	 */
	private function getApplicationSecret()
	{
		return $this->applicationSecret;
	}


	/**
	 * Get the token
	 *
	 * @return	string
	 */
	private function getToken()
	{
		// no token available
		if($this->token == null) return $this->getApplicationId() . '|' . $this->getApplicationSecret();

		// real token
		return $this->token;
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
	 * It will look like: "PHP Bitly/<version> <your-user-agent>"
	 *
	 * @return	string
	 */
	public function getUserAgent()
	{
		return (string) 'PHP Facebook/' . self::VERSION . ' ' . $this->userAgent;
	}


	/**
	 * Process the response array
	 * @remark	not fully implemented yet.
	 *
	 * @return	array
	 * @param	array $response		The response that was retrieved.
	 */
	private function processResponse(array $response)
	{
		// type available?
		if(isset($response['type']))
		{
			// base on the type we should handle the data
			switch($response['type'])
			{
				case 'event':
				break;
			}
		}

		// return
		return $response;
	}


	/**
	 * Set the token to use.
	 *
	 * @return	void
	 * @param	string $token
	 */
	public function setToken($token)
	{
		$this->token = (string) $token;
	}


	/**
	 * Set the application id
	 *
	 * @return	void
	 * @param	string $id
	 */
	private function setApplicationId($id)
	{
		$this->applicationId = (string) $id;
	}


	/**
	 * Set the application secret
	 *
	 * @return	void
	 * @param	string $secret
	 */
	private function setApplicationSecret($secret)
	{
		$this->applicationSecret = (string) $secret;
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
	 * It will be appended to ours, the result will look like: "PHP Bitly/<version> <your-user-agent>"
	 *
	 * @return	void
	 * @param	string $userAgent	Your user-agent, it should look like <app-name>/<app-version>
	 */
	public function setUserAgent($userAgent)
	{
		$this->userAgent = (string) $userAgent;
	}


	/**
	 * Publish something on Facebook
	 *
	 * @return	mixed
	 * @param	string $url			The URL to call.
	 * @param	array $parameters	The parameters to push.
	 * @param	string $file		A file that should be posted
	 */
	public function publish($url, array $parameters = null, $file = null)
	{
		// redefine
		$url = (string) $url;

		// make the call
		return $this->doCall($url, $parameters, 'POST', $file);
	}


	/**
	 * Retrieve from Facebook
	 *
	 * @return	mixed
	 * @param	string $url			The URL to call.
	 * @param	array $parameters	The parameters to push.
	 */
	public function get($url, array $parameters = null)
	{
		// redefine
		$url = (string) $url;

		// add metadata if not already there
		if(!isset($parameters['metadata'])) $parameters['metadata'] = 1;

		// make the call
		$response = $this->doCall($url, $parameters);

		// return
		return (array) $this->processResponse($response);
	}


// authentication
	/**
	 * Get a access token
	 *
	 * @return	string
	 * @param	string $code
	 * @param	string $redirectUrl
	 */
	public function getAccessToken($code, $redirectUrl)
	{
		// build parameters
		$parameters['code'] = (string) $code;
		$parameters['redirect_uri'] = (string) $redirectUrl;
		$parameters['client_id'] = $this->getApplicationId();
		$parameters['client_secret'] = $this->getApplicationSecret();

		// make the call
		$response = $this->doCall('oauth/access_token', $parameters, 'GET', null, true);

		// explode
		$chunks = explode('access_token=', $response);

		// validate
		if(!isset($chunks[1]))
		{
			// we expect JSON so decode it
			$json = @json_decode($response, true);

			// validate json
			if($json === false) throw new FacebookException('Invalid JSON-response');

			// is error?
			if(isset($json['error']))
			{
				// init var
				$type = (isset($json['error']['type'])) ? $json['error']['type'] : '';
				$message = (isset($json['error']['message'])) ? $json['error']['message'] : '';

				// build real message
				if($type != '') $message = trim($type . ': ' . $message);

				// throw error
				throw new FacebookException($message);
			}

			// fallback
			throw new FacebookException('Invalid JSON-response.');
		}

		// store the token for our use
		$this->setToken($chunks[1]);

		// return
		return $chunks[1];
	}


	/**
	 * Get a facebook cookie and process it
	 *
	 * @return	array	If invalid or no cookie it will return false.
	 */
	public function getCookie()
	{
		// build the cookie name
		$cookieName = 'fbs_'. $this->getApplicationId();
		$cookieNameSignedRequest = 'fbsr_'. $this->getApplicationId();

		// validate
		if(!isset($_COOKIE[$cookieName]) && !isset($_COOKIE[$cookieNameSignedRequest])) return false;

		// init var
		$data = array();

		// has signed request cookie
		if(isset($_COOKIE[$cookieNameSignedRequest]))
		{
			list($encodedSignature, $payload) = explode('.', $_COOKIE[$cookieNameSignedRequest], 2);

			// decode the data
			$data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

			try
			{
				$accessToken = $this->getAccessToken($data['code'], '');
				$this->setToken($accessToken);
			}
			catch(FacebookException $e)
			{
				if(substr_count($e->getMessage(), 'Code was invalid or expired.')) return $data;
			}
		}

		// non signed cookie
		if(isset($_COOKIE[$cookieName]))
		{
			// parse the cookie into the data
			parse_str(trim($_COOKIE[$cookieName], '"'), $data);

			// validate
			if(!isset($data['sig'])) return false;

			// sort the data
			ksort($data);
			$payload = '';

			// loop data
			foreach($data as $key => $value)
			{
				if($key != 'sig') $payload .= $key .'='. $value;
			}

			// validate data
			if(md5($payload . $this->getApplicationSecret()) != $data['sig']) return false;
		}

		// return
		return $data;
	}
}


/**
 * Facebook Exception class
 *
 * @author	Tijs Verkoyen <php-facebook@verkoyen.eu>
 */
class FacebookException extends Exception
{
}

?>