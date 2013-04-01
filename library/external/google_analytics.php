<?php

/**
 * GoogleAnalytics class
 *
 * This source file can be used to communicate with Google via AuthSub (http://google.com)
 *
 * The class is documented in the file itself. If you find any bugs help me out and report them. Reporting can be done by sending an email to annelies@netlash.com
 * If you report a bug, make sure you give me enough information (include your code).
 *
 * Based on the classes of Tijs Verkoyen (http://classes.verkoyen.eu).
 *
 * @author Annelies Van Extergem <annelies@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@wijs.be>
 */
class GoogleAnalytics
{
	/*
	 * Internal constant to enable/disable debugging.
	 *
	 * @var bool
	 */
	const DEBUG = false;

	/**
	 * End point for Analytics data access.
	 *
	 * @var string
	 */
	const API_URL = 'https://www.googleapis.com/analytics/v2.4';

	/**
	 * API key needed to make calls since v2.4.
	 *
	 * @var string
	 */
	private $apiKey;

	/**
	 * cURL instance
	 *
	 * @var	resource
	 */
	private $curl;

	/**
	 * The session token
	 *
	 * @var	string
	 */
	private $sessionToken = null;

	/**
	 * The table id
	 *
	 * @var	string
	 */
	private $tableId = null;

	/**
	 * Creates an instance of GoogleAnalytics, setting the session token and table id.
	 *
	 * @return	void
	 * @param	string[optional] $sessionToken		The session token to make calls with.
	 * @param	string[optional] $tableId			The table id to get data from.
	 */
	public function __construct($sessionToken = null, $tableId = null)
	{
		$this->setSessionToken($sessionToken);
		$this->setTableId($tableId);
	}

	/**
	 * Destroy cURL instance.
	 *
	 * @return	void
	 */
	public function __destruct()
	{
		if($this->curl != null) curl_close($this->curl);
	}

	/**
	 * Make a call to the given URL with the given token.
	 *
	 * @return	string
	 * @param	string $URL			The url to call.
	 * @param	string $token		The token to call with.
	 */
	private function doCall($URL, $token)
	{
		// redefine parameters
		$URL = (string) $URL;
		$token = (string) $token;

		// append API Key, needed since v2.4
		$URL .= (stripos($URL, '?') !== false) ? '&' : '?';
		$URL .= 'key=' . $this->apiKey;

		// set options
		$options[CURLOPT_URL] = $URL;
		$options[CURLOPT_RETURNTRANSFER] = true;
		$options[CURLOPT_SSL_VERIFYPEER] = false;
		$this->curlheader[0] = sprintf('Authorization: AuthSub token="%s"/n', $token);
		$options[CURLOPT_HTTPHEADER] = $this->curlheader;

		// init if needed
		if($this->curl == null) $this->curl = curl_init();

		// set options
		curl_setopt_array($this->curl, $options);

		// execute
		$response = curl_exec($this->curl);
		$headers = curl_getinfo($this->curl);

		// fetch errors
		$errorNumber = curl_errno($this->curl);
		$errorMessage = curl_error($this->curl);

		// no analytics account - should be dealt with otherwise but this has the same http code as the 'unauthorized' state
		if($response == 'No Analytics account was found for the currently logged-in user')
		{
			// return this response
			return $response;
		}

		// invalid headers
		if($headers['http_code'] == 401)
		{
			// return special code
			return 'UNAUTHORIZED';
		}

		// invalid headers
		if(!in_array($headers['http_code'], array(0, 200)))
		{
			// should we provide debug information
			if(self::DEBUG)
			{
				// open pre
				echo '<pre>';

				// dump the header-information
				var_dump($headers);

				// dump the raw response
				var_dump($response);

				// close pre
				echo '</pre>';

				// stop the script
				exit;
			}

			// throw error
			throw new GoogleAnalyticsException($response, (int) $headers['http_code']);
		}

		// error?
		if($errorNumber != '') throw new GoogleAnalyticsException($errorMessage, $errorNumber);

		// return
		return $response;
	}

	/**
	 * Get all website profiles and their account(s).
	 *
	 * @return	mixed
	 * @param	string $sessionToken	The session token to get accounts from.
	 */
	public function getAnalyticsAccountList($sessionToken)
	{
		// try to make the call
		try
		{
			/*
			 * The "~all/webproperties/~all/profiles" is to specify that the call should also
			 * return webproperties. We need the webproperty to be able to set the GA tracking code.
			 */
			$response = $this->doCall(self::API_URL . '/management/accounts/~all/webproperties/~all/profiles', $sessionToken);
			$accounts = $this->doCall(self::API_URL . '/management/accounts/', $sessionToken);
		}

		// catch possible exception
		catch(Exception $e)
		{
			throw $e;
		}

		// no accounts - return an empty array
		if($response == 'No Analytics account was found for the currently logged-in user') return array();

		// unauthorized
		if($response == 'UNAUTHORIZED') return $response;

		// load with SimpleXML
		$simpleXMLAccounts = @simplexml_load_string(str_replace(array('dxp:', 'openSearch:', 'ga:'), '', $accounts));
		$accountNames = array();

		foreach($simpleXMLAccounts->entry as $entry)
		{
			$id = null;
			$name = null;

			foreach($entry->property as $property)
			{
				if((string) $property['name'] == 'accountId') $id = (string)  $property['value'];
				if((string) $property['name'] == 'accountName') $name = (string)  $property['value'];
			}

			if($id !== null && $name !== null)
			{
				$accountNames[$id] = $name;
			}
		}

		// load with SimpleXML
		$simpleXML = @simplexml_load_string(str_replace(array('dxp:', 'openSearch:', 'ga:'), '', $response));

		// something went wrong
		if(!isset($simpleXML->entry)) return 'ERROR';

		// init vars
		$i = 0;
		$profiles = array();

		// loop entries
		foreach($simpleXML->entry as $entry)
		{
			// init entry array
			$profile = array();

			// build array
			$profile['id'] = (string) $entry->id;
			$profile['title'] = (string) $entry->title;
			$profile['tableId'] = 'ga:'. (string) $entry->tableId;
			$profile['accountName'] = '';

			// loop properties and save them
			foreach($entry->property as $property)
			{
				if((string) $property['name'] == 'accountId')
				{
					if(isset($accountNames[(string) $property['value']]))
					{
						$profile['accountName'] = $accountNames[(string) $property['value']];
					}
				}

				$profile[(string) $property['name']] = (string) $property['value'];
			}

			// save profile in profiles array
			$profiles[$i] = $profile;

			// increment counter
			$i++;
		}

		// return the profiles
		return (array) $profiles;
	}

	/**
	 * Makes a call to Google.
	 *
	 * @return	array
	 * @param	mixed $metrics					The metrics as string or as array.
	 * @param	int $startTimestamp				The start date from where data must be collected.
	 * @param	int $endTimestamp				The end date to where data must be collected.
	 * @param	mixed[optional] $dimensions		The optional dimensions as string or as array.
	 * @param	array[optional] $parameters		The extra parameters for google.
	 */
	public function getAnalyticsResults($metrics, $startTimestamp, $endTimestamp, $dimensions = array(), array $parameters = array())
	{
		// check required parameters
		if(!isset($this->sessionToken, $this->tableId, $metrics, $startTimestamp, $endTimestamp)) return array('aggregates' => array(), 'entries' => array());

		// redefine parameters
		$metrics = (array) $metrics;
		$startDate = date('Y-m-d', (int) $startTimestamp);
		$endDate = date('Y-m-d', (int) $endTimestamp);
		$dimensions = (array) $dimensions;
		$parameters = (array) $parameters;

		// build url
		$URL = self::API_URL .'/data?ids=ga:'. $this->tableId;
		$URL .= '&metrics='. implode(',', $metrics);
		$URL .= '&start-date='. $startDate;
		$URL .= '&end-date='. $endDate;
		$URL .= '&dimensions='. implode(',', $dimensions);

		// add parameters
		if(count($parameters) > 0)
		{
			// loop them and combine key and urlencoded value (but don't encode the colons)
			foreach($parameters as $key => $value) $parameters[$key] = $key .'='. str_replace(array('%3A', '%3D%3D'), array(':', '=='), urlencode($value));

			// append to array
			$URL .= '&'. implode('&', $parameters);
		}

		// do the call
		$result = $this->doCall($URL, $this->sessionToken);

		// unauthorized
		if($result == 'UNAUTHORIZED') return $result;

		// interpret the result xml
		$simpleXML = simplexml_load_string(str_replace(array('dxp:', 'openSearch:', 'ga:'), '', $result));

		// init vars
		$results = array('aggregates' => array(), 'entries' => array());

		// results total, start index and items per page
		$results['totalResults'] = (int) $simpleXML->totalResults;
		$results['startIndex'] = (int) $simpleXML->startIndex;
		$results['itemsPerPage'] = (int) $simpleXML->itemsPerPage;

		// start and end date
		$results['startDate'] = (string) $simpleXML->startDate;
		$results['endDate'] = (string) $simpleXML->endDate;

		// there are some aggregates
		if(count($simpleXML->aggregates->metric) > 0)
		{
			// loop them
			foreach($simpleXML->aggregates->metric as $aggregate)
			{
				// build the array
				$results['aggregates'][(string) $aggregate['name']] = (string) $aggregate['value'];
			}
		}

		// there are some entries
		if(count($simpleXML->entry) > 0)
		{
			// init vars
			$i = 0;

			// loop them
			foreach($simpleXML->entry as $entry)
			{
				// loop the dimensions
				foreach($entry->dimension as $dimension) $results['entries'][$i][(string) $dimension['name']] = (string) $dimension['value'];

				// loop the metrics
				foreach($entry->metric as $metric) $results['entries'][$i][(string) $metric['name']] = (string) $metric['value'];

				// increase counter
				$i++;
			}
		}

		// return the result
		return $results;
	}

	/**
	 * Get a session token based on a one-time token.
	 *
	 * @return	string
	 * @param	string $oneTimeToken	The one-time token to get a session token with.
	 */
	public function getSessionToken($oneTimeToken)
	{
		// make the call
		$response = $this->doCall('https://www.google.com/accounts/AuthSubSessionToken', $oneTimeToken);

		// a token is given in the response - save it
		if(preg_match('/Token=(.*)/', $response, $matches)) $sessionToken = $matches[1];

		// no token was given - throw an exception
		else throw new GoogleAnalyticsException($response);

		// return the session token
		return $sessionToken;
	}

	/**
	 * Gets the table id
	 *
	 * @return	string
	 */
	public function getTableId()
	{
		return $this->tableId;
	}

	/**
	 * @param string $key
	 */
	public function setApiKey($key)
	{
		$this->apiKey = (string) $key;
	}

	/**
	 * Set the session token to make calls with
	 *
	 * @return	void
	 * @param	string $sessionToken	The session token to make calls with.
	 */
	public function setSessionToken($sessionToken)
	{
		$this->sessionToken = (isset($sessionToken) ? (string) $sessionToken : null);
	}

	/**
	 * Set the table id to get data from
	 *
	 * @return	void
	 * @param	string $tableId		The table id from which data is received.
	 */
	public function setTableId($tableId)
	{
		$this->tableId = (isset($tableId) ? (string) $tableId : null);
	}
}

/**
 * GoogleAnalyticsException class
 *
 * @author		Annelies Van Extergem <annelies@netlash.com>
 */
class GoogleAnalyticsException extends Exception
{
	/**
	 * Http header-codes
	 *
	 * @var	array
	 */
	private $aStatusCodes = array(100 => 'Continue',
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
									505 => 'HTTP Version Not Supported');


	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	string[optional] $message	The errormessage.
	 * @param	int[optional] $code			The errornumber.
	 */
	public function __construct($message = null, $code = null)
	{
		// set message
		if($message === null && isset($this->aStatusCodes[(int) $code])) $message = $this->aStatusCodes[(int) $code];

		// call parent
		parent::__construct((string) $message, $code);
	}
}

?>