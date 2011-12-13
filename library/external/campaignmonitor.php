<?php

/**
 * CampaignMonitor class
 *
 * This source file can be used to communicate with CampaignMonitor API v3 (http://www.campaignmonitor.com/api/)
 *
 * License
 * Copyright (c) 2011, Dave Lens. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation
 * 	  and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products derived from this software without specific prior written permission.
 *
 * This software is provided by the author "as is" and any express or implied warranties, including, but not limited to, the implied warranties of
 * merchantability and fitness for a particular purpose are disclaimed. In no event shall the author be liable for any direct, indirect, incidental,
 * special, exemplary, or consequential damages (including, but not limited to, procurement of substitute goods or services; loss of use, data, or
 * profits; or business interruption) however caused and on any theory of liability, whether in contract, strict liability, or tort (including
 * negligence or otherwise) arising in any way out of the use of this software, even if advised of the possibility of such damage.
 *
 * @author		Dave Lens <dave@netlash.com>
 * @version		2.0.0
 *
 * @copyright	Copyright (c) 2011, Netlash. All rights reserved.
 * @license		BSD License
 */
class CampaignMonitor
{
	// internal constant to enable/disable debugging
	const DEBUG = false;

	// url for the campaignmonitor API
	const API_URL = 'http://api.createsend.com/api/v3';
	const SECURE_API_URL = 'https://api.createsend.com/api/v3';

	// port for the campaignmonitor API
	const API_PORT = 80;

	// current version
	const API_VERSION = '3.0';

	/**
	 * The API key for the logged-in user
	 *
	 * @var	string
	 */
	private $apiKey;


	/**
	 * Default campaign ID
	 *
	 * @var	string
	 */
	private $campaignId;


	/**
	 * Default client ID
	 *
	 * @var	string
	 */
	private $clientId;


	/**
	 * cURL object
	 *
	 * @var	object
	 */
	private $curl;


	/**
	 * Default list ID
	 *
	 * @var string
	 */
	private $listId;


	/**
	 * The password for an authenticating user
	 *
	 * @var	string
	 */
	private $password;


	/**
	 * The response format of the cURL call. Either 'json' or 'xml'
	 *
	 * @var	string
	 */
	private $responseFormat = 'json';


	/**
	 * The base URL of the site you use to login to Campaign Monitor. e.g. http://example.createsend.com/
	 *
	 * @var	string
	 */
	private $siteURL;


	/**
	 * SOAP connection
	 *
	 * @var SoapClient
	 */
	private $soap;


	/**
	 * The timeout
	 *
	 * @var	int
	 */
	private $timeOut = 60;


	/**
	 * The user agent for the curl call
	 *
	 * @var	string
	 */
	private $userAgent;


	/**
	 * The username for an authenticating user
	 *
	 * @var	string
	 */
	private $username;


	/**
	 * Class constructor
	 *
	 * @return	void
	 * @param	string $URL						The base URL of your CreateSend site. e.g. http://example.createsend.com/.
	 * @param	string $username				The username you use to login to Campaign Monitor.
	 * @param	string $password				The password you use to login to Campaign Monitor.
	 * @param	string[optional] $clientId		The default client ID to use throughout the class.
	 * @param	string[optional] $listId		The default list ID to use throughout the class.
	 */
	public function __construct($URL, $username, $password, $timeOut = 60, $clientId = null, $listId = null)
	{
		// check input
		if(empty($username) || empty($password)) throw new CampaignMonitorException('No username or password set.', 105);

		// set username/password and call timeout
		$this->setUsername($username);
		$this->setPassword($password);
		$this->setTimeOut($timeOut);

		// get the API key
		$apiKey = $this->getAPIKey($URL, $this->getUsername(), $this->getPassword());

		// set api key and password
		$this->setAPIKey($apiKey);

		// set any IDs we need throughout the class
		$this->setClientId($clientId);
		$this->setListId($listId);
	}


	/**
	 * Adds a subscriber to an existing subscriber list. If $resubscribe is set to true, it will resubscribe the e-mail address if not active.
	 *
	 * @return	bool
	 * @param	string $email					The email address of the new subscriber.
	 * @param	string $name					The name of the new subscriber. If the name is unknown, an empty string can be passed in.
	 * @param	array[optional] $customFields	The custom fields for this subscriber in key/value pairs.
	 * $param	bool[optional] $resubscribe		Subscribes an unsubscribed email address back to the list if this is true.
	 * $param	string[optional] $listId		The list you want to add the subscriber to.
	 */
	public function addSubscriber($email, $name, $customFields = array(), $resubscribe = true, $listId = null)
	{
		// set list ID
		$listId = empty($listId) ? $this->getListId() : $listId;

		// set parameters
		$parameters['EmailAddress'] = (string) $email;
		$parameters['Name'] = (string) $name;
		$parameters['Resubscribe'] = (bool) $resubscribe;

		// set custom fields if any were found
		if(!empty($customFields))
		{
			// fetch all existing custom fields
			//$currentFields = (array) $this->getCustomFields($listId);

			// loop the custom fields, build a new array
			//foreach($currentFields as $key => $field) $currentFields[$key] = $field['name'];

			// loop the fields
			foreach($customFields as $key => $value)
			{
				// check if this field already exists; if not, add it.
				//if(!in_array($key, $currentFields)) $this->createCustomField($key, 'text', null, $listId);

				// add it to the list of field values
				$parameters['CustomFields'][] = array('Key' => $key, 'Value' => $value);
			}
		}

		// make the call
		$this->doCall('subscribers/'. $listId, $parameters, 'POST');

		// if we made it here, return true
		return true;
	}


	/**
	 * Creates a campaign. Returns the campaign ID when succesful or false if the call failed
	 *
	 * @return	string
	 * @param	string $name					The name of the new campaign. This must be unique across all draft campaigns for the client.
	 * @param	string $subject					The subject of the new campaign.
	 * @param	string $fromName				The name to appear in the From field in the recipients email client when they receive the new campaign.
	 * @param	string $fromEmail				The email address that the new campaign will come from.
	 * @param	string $replyToEmail			The email address that any replies to the new campaign will be sent to.
	 * @param	string $HTMLContentURL			The URL of the HTML content for the new campaign.
	 * @param	string $textContentURL			The URL of the text content for the new campaign.
	 * @param	array $subscriberLists			An array of lists to send the campaign to.
	 * @param	array $subscriberListSegments	An array of Segment Names and their appropriate List ID’s to send the campaign to.
	 * @param	string[optional] $clientId		The ID of the client who will be owner of the campaign.
	 */
	public function createCampaign($name, $subject, $fromName, $fromEmail, $replyToEmail, $HTMLContentURL, $textContentURL, array $subscriberLists, $subscriberListSegments = array(), $clientId = null)
	{
		// set client ID
		$clientId = empty($clientId) ? $this->getClientId() : $clientId;

		// set parameters
		$parameters['Name'] = (string) $name;
		$parameters['Subject'] = (string) $subject;
		$parameters['FromName'] = (string) $fromName;
		$parameters['FromEmail'] = (string) $fromEmail;
		$parameters['ReplyTo'] = (string) $replyToEmail;
		$parameters['HtmlUrl'] = (string) $HTMLContentURL;
		$parameters['TextUrl'] = (string) $textContentURL;
		$parameters['ListIDs'] = !empty($subscriberLists) ? $subscriberLists : array();
		$parameters['SegmentIDs'] = !empty($subscriberListSegments) ? $subscriberListSegments : array();

		// return the result
		return (string) $this->doCall('campaigns/'. $clientId, $parameters, 'POST');
	}


	/**
	 * Creates a client. Returns the client ID when succesful or false if the call failed
	 *
	 * @return	string
	 * @param	string $companyName		The client company name.
	 * @param	string $contactName		The personal name of the principle contact for this client.
	 * @param	string $email			An email address to which this client will be sent application-related emails.
	 * @param	string $country			This client’s country.
	 * @param	string $timezone		Client timezone for tracking and reporting data.
	 */
	public function createClient($companyName, $contactName, $email, $country, $timezone)
	{
		// fetch the country list
		$countries = $this->getCountries();
		$timezones = $this->getTimezones();

		// check if $country is in the allowed country list
		if(!in_array($country, $countries)) throw new CampaignMonitorException('No valid country provided');

		// check if $timezone is in the allowed timezones list
		if(!in_array($timezone, $timezones)) throw new CampaignMonitorException('No valid timezone provided');

		// set parameters
		$parameters['CompanyName'] = (string) $companyName;
		$parameters['ContactName'] = (string) $contactName;
		$parameters['EmailAddress'] = (string) $email;
		$parameters['Country'] = (string) $country;
		$parameters['Timezone'] = (string) $timezone;

		// try and create the record
		return (string) $this->doCall('clients', $parameters, 'POST');
	}


	/**
	 * Creates a custom field for a list.
	 *
	 * @return	bool
	 * @param	string $name				The name of the field.
	 * @param	string[optional] $type		The type of the field to create, possible values are: string, int, text, number, multiSelectOne, multiSelectMany.
	 * @param	array[optional] $options	The available options for a multi-valued custom field.
	 * @param	string[optional] $listId	The list ID to create the custom field for
	 */
	public function createCustomField($name, $type = 'text', $options = array(), $listId = null)
	{
		// set list ID
		$listId = empty($listId) ? $this->getListId() : $listId;

		// check if the given type is allowed
		if(!empty($type) && !in_array($type, array('string', 'int', 'text', 'number', 'country', 'multiSelectOne', 'multiSelectMany')))
		{
			// type is not allowed
			throw new CampaignMonitorException('This type is not allowed. Must be text, number, multiSelectOne or multiSelectMany.');
		}

		// if type is empty, just set it to text
		if(empty($type) || $type == 'string') $type = 'text';

		// catch 'int' type
		if($type == 'int') $type = 'number';

		// if type is a multiple select, $options is required
		if(in_array($type, array('multiSelectOne', 'multiSelectMany')) && empty($options))
		{
			// no options set
			throw new CampaignMonitorException('You need to provide an array with options for a multiSelect type.');
		}

		// set parameters
		$parameters['FieldName'] = (string) $name;
		$parameters['DataType'] = (string) ucfirst($type);

		// options found
		if(!empty($options))
		{
			// set options
			$parameters['Options'] = (array) $options;
		}

		// try and create the record
		return (bool) $this->doCall('lists/'. $listId .'/customfields', $parameters, 'POST');
	}


	/**
	 * Creates a list. Returns the list ID when succesful or false if the call failed
	 *
	 * @return	string
	 * @param	string $title								The title of the list.
	 * @param	string[optional] $unsubscribePage			The URL to which subscribers will be directed when unsubscribing from the list. If left blank or omitted a generic unsubscribe page is used.
	 * @param	string[optional] $confirmOptIn				Either true or false depending on whether the list requires email confirmation or not. Please see the help documentation for more details of what this means.
	 * @param	string[optional] $confirmationSuccessPage	Successful email confirmations will be redirected to this URL. Ignored if ConfirmOptIn is false. If left blank or omitted a generic confirmation page is used.
	 * @param	string[optional] $clientId					The client ID.
	 */
	public function createList($title, $unsubscribePage = null, $confirmOptIn = false, $confirmationSuccessPage = null, $clientId = null)
	{
		// set client ID
		$clientId = empty($clientId) ? $this->getClientId() : $clientId;

		// set parameters
		$parameters['Title'] = (string) $title;
		$parameters['UnsubscribePage'] = (string) $unsubscribePage;
		$parameters['ConfirmedOptIn'] = $confirmOptIn ? true : false;
		$parameters['ConfirmationSuccessPage'] = (string) $confirmationSuccessPage;

		// return the result
		return (string) $this->doCall('lists/'. $clientId, $parameters, 'POST');
	}


	/**
	 * Creates a segment. Returns true on success
	 *
	 * @return	bool
	 * @param	string $title
	 * @param	array[optional] $rules
	 * @param	string[optional] $listId
	 */
	public function createSegment($title , $rules = array(), $listId = null)
	{
		// set parameters
		$parameters['Title'] = (string) $title;
		$parameters['Rules'] = array();

		// rules were found
		if(!empty($rules))
		{
			// loop the rules
			foreach($rules as $key => $rule)
			{
				// add the subject and clauses to the parameters stack
				$parameters['Rules'][$key]['Subject'] = $rule['subject'];
				$parameters['Rules'][$key]['Clauses'] = array();

				// clauses found
				if(!empty($rule['clauses']))
				{
					// loop the clauses
					foreach($rule['clauses'] as $clause)
					{
						// add the clause to the results stack for the active key
						$parameters['Rules'][$key]['Clauses'][] = $clause;
					}
				}
			}
		}

		// return the result
		return (bool) $this->doCall('segments/'. $listId, $parameters, 'POST');
	}


	/**
	 * Creates a template. Returns the template ID when succesful
	 *
	 * @return	mixed
	 * @param	string $name						The name of the template. Maximum of 30 characters (will be truncated to 30 characters if longer).
	 * @param	string $HTMLPageURL					The URL of the HTML page you have created for the template.
	 * @param	string[optional] $zipFileURL		Optional URL of a zip file containing any other files required by the template.
	 * @param	string[optional] $screenshotURL		Optional URL of a screenshot of the template. Must be in jpeg format and at least 218 pixels wide.
	 * @param	string[optional] $clientId			The ID of the client for whom the template should be created.
	 */
	public function createTemplate($name, $HTMLPageURL, $zipFileURL = null, $screenshotURL = null, $clientId = null)
	{
		// set client ID
		$clientId = empty($clientId) ? $this->getClientId() : $clientId;

		// set parameters
		$parameters['Name'] = (string) $name;
		$parameters['HtmlPageURL'] = (string) $HTMLPageURL;
		$parameters['ZipFileURL'] = (string) $zipFileURL;
		$parameters['ScreenshotURL'] = (string) $screenshotURL;

		// return the result
		return $this->doCall('templates/'. $clientId, $parameters, 'POST');
	}


	/**
	 * Deletes a campaign
	 *
	 * @return	bool
	 * @param	string $campaignId	The ID of the campaign.
	 */
	public function deleteCampaign($campaignId)
	{
		// make the call
		return (bool) $this->doCall('campaigns/'. $campaignId, null, 'DELETE');
	}


	/**
	 * Deletes a client
	 *
	 * @return	bool
	 * @param	string[optional] $clientId	The ID of the client.
	 */
	public function deleteClient($clientId = null)
	{
		// set ID
		$clientId = empty($clientId) ? $this->getClientId() : $clientId;

		// make the call
		return (bool) $this->doCall('clients/'. $clientId, null, 'DELETE');
	}


	/**
	 * Deletes a custom field
	 *
	 * @return	bool
	 * @param	string $name				The name of the field.
	 * @param	string[optional] $listId	The ID of the list.
	 */
	public function deleteCustomField($name, $listId = null)
	{
		// set list ID
		$listId = empty($listId) ? $this->getListId() : $listId;

		// keys need to be wrapped in []
		if(!preg_match('/\[.*\]/', $name)) $name = '['. $name .']';

		// delete the record
		return (bool) $this->doCall('lists/'. $listId .'/customfields/'. $name, null, 'DELETE');
	}


	/**
	 * Deletes a list
	 *
	 * @return	bool
	 * @param	string $listId	The ID of the list to delete
	 */
	public function deleteList($listId)
	{
		// check if record exists
		if(!$this->existsList($listId)) return false;

		// make the call
		return (bool) $this->doCall('lists/'. $listId, null, 'DELETE');
	}


	/**
	 * Deletes a template
	 *
	 * @return	bool
	 * @param	string $templateId		The ID of the template.
	 */
	public function deleteTemplate($templateId)
	{
		// check if record exists
		if(!$this->existsTemplate($templateId)) return false;

		// make the call
		return (bool) $this->doCall('templates/'. $templateId, null, 'DELETE');
	}


	/**
	 * Make the call
	 *
	 * @return	string
	 * @param	string $url						The url to call.
	 * @param	array[optiona] $parameters		Optional parameters.
	 * @param	bool[optional] $method			The method to use. Possible values are GET, POST.
	 * @param	string[optional] $filePath		The path to the file to upload.
	 * @param	bool[optional] $secure			Do a secure call over https with basic HTTP auth
	 * @param	bool[optional] $expectJSON		Do we expect JSON.
	 * @param	bool[optional] $returnHeaders	Should the headers be returned?
	 */
	private function doCall($url, array $parameters = null, $method = 'GET', $secure = false, $expectJSON = true, $returnHeaders = false)
	{
		// allowed methods
		$allowedMethods = array('GET', 'POST', 'DELETE', 'PUT');

		// add HTTP authentication. The call to retrieve the apikey works slightly different
		switch($url)
		{
			case 'apikey':
				$options[CURLOPT_USERPWD] = $this->username .':'. $this->password;
				$options[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
			break;

			default:
				$options[CURLOPT_USERPWD] = $this->apiKey .':'. md5(sha1(time()));
				$options[CURLOPT_HTTPAUTH] = CURLAUTH_ANY;
		}

		// redefine
		$url = (string) $url .'.'. $this->responseFormat;
		$parameters = (array) $parameters;
		$method = (string) $method;
		$expectJSON = (bool) $expectJSON;

		// validate method
		if(!in_array($method, $allowedMethods)) throw new CampaignMonitorException('Unknown method ('. $method .'). Allowed methods are: '. implode(', ', $allowedMethods));

		// set the expect header to avoid connection fails
		$headers = array();
		$headers[] = 'Expect:';

		// based on the method, we should handle the parameters in a different way
		switch($method)
		{
			case 'DELETE':
				$options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
			break;

			case 'GET':
				// add the parameters into the querystring
				if(!empty($parameters)) $url .= '?'. http_build_query($parameters);
			break;

			case 'POST':
				// set postfields, repair forward slash bug
				$options[CURLOPT_POSTFIELDS] = str_replace('\\/', '/', json_encode($parameters));

				// enable post
				$options[CURLOPT_POST] = true;

				// set content-type to JSON
				$headers[] = 'Content-Type: application/json';
			break;

			case 'PUT':
				// set postfields, repair forward slash bug
				$options[CURLOPT_POSTFIELDS] = str_replace('\\/', '/', json_encode($parameters));

				// enable put
				$options[CURLOPT_CUSTOMREQUEST] = 'PUT';

				// set content-type to JSON
				$headers[] = 'Content-Type: application/json';
			break;
		}

		// set options
		$options[CURLOPT_URL] = self::API_URL .'/'. $url;
		$options[CURLOPT_PORT] = self::API_PORT;
		$options[CURLOPT_USERAGENT] = $this->getUserAgent();
		if(ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) $options[CURLOPT_FOLLOWLOCATION] = true;
		$options[CURLOPT_RETURNTRANSFER] = true;
		$options[CURLOPT_TIMEOUT] = (int) $this->getTimeOut();
		$options[CURLOPT_SSL_VERIFYPEER] = false;
		$options[CURLOPT_SSL_VERIFYHOST] = false;
		$options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
		$options[CURLOPT_HTTPHEADER] = $headers;

		// init
		if($this->curl == null) $this->curl = curl_init();

		// set options
		curl_setopt_array($this->curl, $options);

		// execute
		$response = curl_exec($this->curl);
		$headers = curl_getinfo($this->curl);

		// fetch errors
		$errorNumber = curl_errno($this->curl);
		$errorMessage = curl_error($this->curl);

		// return the headers
		if($returnHeaders) return $headers;

		// we don't expext JSON, return the response
		if(!$expectJSON) return $response;

		// replace ids with their string values, added because some PHP-versions can't handle these large values
		$response = preg_replace('/id":(\d+)/', 'id":"\1"', $response);

		/*
			This is a tricky one. Apparently PHP 5.2 does not decode strings, but returns NULL instead. 5.2.1x has no issues with this.
			So we can assume that, if the response is not empty, and it is not a json array or object, it is a string, and we return it
			without the double quotes, and without json_decode().

			I am aware this is somewhat dirty, but if it lets you use 5.2 without issues than I can live with it.
		*/
		$json = ($response != '' && strpos($response, '{') === false && strpos($response, '[') === false) ? str_replace('"', '', $response) : json_decode($response, true);

		// 200 OK means everything went well, 201 OK means a resource just got created with a POST request.
		if($headers['http_code'] !== 200 && $headers['http_code'] !== 201 && !isset($json['ResultData']))
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
			}

			// throw exception
			throw new CampaignMonitorException($headers['http_code'] .': '. $json['Message']);
		}

		// if we did a delete request, return true at this point
		if($method === 'DELETE' && $json === null) return true;

		// return
		return $json;
	}


	/**
	 * Checks if a client exists
	 *
	 * @return	bool
	 * @param	string $clientId	The ID of the client.
	 */
	public function existsClient($clientId)
	{
		// try and fetch the record
		try
		{
			$this->getClient($clientId);
		}

		// stop here if an exception is found
		catch(Exception $e)
		{
			return false;
		}

		// if we made it here, the record exists
		return true;
	}


	/**
	 * Checks if a list exists
	 *
	 * @return	bool
	 * @param	string $listId	The ID of the list.
	 */
	public function existsList($listId)
	{
		// try and fetch the record
		try
		{
			$this->getList($listId);
		}

		// stop here if an exception is found
		catch(Exception $e)
		{
			return false;
		}

		// if we made it here, the record exists
		return true;
	}


	/**
	 * Checks if a template exists
	 *
	 * @return	bool
	 * @param	string $templateId	The ID of the template.
	 */
	public function existsTemplate($templateId)
	{
		// try and fetch the record
		try
		{
			$this->getTemplate($templateId);
		}

		// stop here if an exception is found
		catch(Exception $e)
		{
			return false;
		}

		// if we made it here, the record exists
		return true;
	}


	/**
	 * Returns the API key for the logged-in user
	 *
	 * @return	string
	 * @param	string $URL			The base URL of your CreateSend site. e.g. http://example.createsend.com/
	 * @param	string $username	The CM account username.
	 * @param	string $password	The CM account password.
	 */
	public function getAPIKey($URL, $username, $password)
	{
		// set parameters
		$parameters['siteurl'] = (string) $URL;

		// make the call
		$result = $this->doCall('apikey', $parameters, 'GET', true);

		// return the result
		return $result['ApiKey'];
	}


	/**
	 * Returns a list of all subscribers for a list that have hard bounced since the specified date.
	 *
	 * @return	array
	 * @param	string[optional] $listId			The list ID to fetch the bounced subscribers from
	 * @param	int[optional] $timestamp			Subscribers which bounced on or after the Date value specified will be returned. Must be in the format YYYY-MM-DD. Required
	 * @param	int[optional] $page					The results page to retrieve. Default: 1
	 * @param	int[optional] $pageSize				The number of records to return (in docs referred to as 'pagesize'. Default 1000
	 * @param	string[optional] $orderField		The field which should be used to order the results. Default: date
	 * @param	string[optional] $orderDirection	The direction in which results should be ordered. Default: asc
	 */
	public function getBouncedSubscribers($listId = null, $timestamp = null, $page = 1, $pageSize = 1000, $orderField = 'date', $orderDirection = 'asc')
	{
		// set ID
		$listId = empty($listId) ? $this->getListId() : $listId;

		// check timestamp
		if(empty($timestamp)) $timestamp = strtotime('last week');

		// set parameters
		$parameters['date'] = (string) date('Y-m-d', $timestamp);
		$parameters['page'] = (int) $page;
		$parameters['pagesize'] = (int) $pageSize;
		$parameters['orderfield'] = (string) $orderField;
		$parameters['orderdirection'] = (string) $orderDirection;

		// make the call
		$records = (array) $this->doCall('lists/'. $listId .'/bounced', $parameters);

		// stop here if no records were set
		if(empty($records['Results'])) return array();

		// reserve variables
		$results = array();

		// loop the records
		foreach($records['Results'] as $key => $record)
		{
			// set values
			$results[$key]['email'] = $record['EmailAddress'];
			$results[$key]['name'] = $record['Name'];
			$results[$key]['date'] = $record['Date'];
			$results[$key]['status'] = $record['State'];

			// check if there are custom fields present
			if(empty($record['CustomFields']['SubscriberCustomField'])) continue;

			// loop records
			foreach($record['CustomFields']['SubscriberCustomField'] as $field)
			{
				// set values
				$results[$key]['custom_fields'][$field['Key']] = $field['Value'];
			}
		}

		// return the records
		return $results;
	}


	/**
	 * Returns a list of all subscribers who bounced for a given campaign, and the type of bounce ("Hard"=Hard Bounce, "Soft"=Soft Bounce).
	 *
	 * @return	array
	 * @param	string $campaignId					The ID of the campaign you want data for.
	 * @param	int[optional] $page					The results page to retrieve. Default: 1
	 * @param	int[optional] $pageSize				The number of records to return (in docs referred to as 'pagesize'. Default 1000
	 * @param	string[optional] $orderField		The field which should be used to order the results. Default: date
	 * @param	string[optional] $orderDirection	The direction in which results should be ordered. Default: asc
	 */
	public function getCampaignBounces($campaignId, $page = 1, $pageSize = 1000, $orderField = 'date', $orderDirection = 'asc')
	{
		// set parameters
		$parameters['page'] = (int) $page;
		$parameters['pagesize'] = (int) $pageSize;
		$parameters['orderfield'] = (string) $orderField;
		$parameters['orderdirection'] = (string) $orderDirection;

		// make the call
		$records = (array) $this->doCall('campaigns/'. $campaignId .'/bounces', $parameters);

		// stop here if no records were set
		if(empty($records['Results'])) return array();

		// reserve variables
		$results = array();

		// loop the records
		foreach($records['Results'] as $key => $record)
		{
			$results[$key]['email'] = $record['EmailAddress'];
			$results[$key]['list_id'] = $record['ListID'];
			$results[$key]['bounce_type'] = $record['BounceType'];
			$results[$key]['date'] = $record['Date'];
			$results[$key]['Reason'] = $record['Reason'];
		}

		// return the records
		return $results;
	}


	/**
	 * Contains a paged result representing all subscribers who clicked a link in the email for a given campaign, including the date/time and IP address they clicked the campaign from. The result set is organized by URL.
	 *
	 * @return	array
	 * @param	string $campaignId	The ID of the campaign you want data for.
	 * @param	int[optional] $timestamp			Clicks made after the Date value specified will be returned. Must be in the format YYYY-MM-DD. Required.
	 * @param	int[optional] $page					The results page to retrieve. Default: 1
	 * @param	int[optional] $pageSize				The number of records to return (in docs referred to as 'pagesize'. Default 1000
	 * @param	string[optional] $orderField		The field which should be used to order the results. Default: date
	 * @param	string[optional] $orderDirection	The direction in which results should be ordered. Default: asc
	 */
	public function getCampaignClicks($campaignId, $timestamp = null, $page = 1, $pageSize = 1000, $orderField = 'date', $orderDirection = 'asc')
	{
		// set parameters
		$parameters['date'] = (string) date('Y-m-d', $timestamp);
		$parameters['page'] = (int) $page;
		$parameters['pagesize'] = (int) $pageSize;
		$parameters['orderfield'] = (string) $orderField;
		$parameters['orderdirection'] = (string) $orderDirection;

		// make the call
		$records = (array) $this->doCall('campaigns/'. $campaignId .'/clicks', $parameters);

		// stop here if no records were set
		if(empty($records['Results'])) return array();

		// reserve variables
		$results = array();
		$links = array();

		// loop the records
		foreach($records['Results'] as $key => $record)
		{
			// build record for the clicker
			$result = array();
			$result['email'] = $record['EmailAddress'];
			$result['url'] = preg_replace('/(\?|&)utm_(.*)/is', '', $record['URL']);
			$result['list_id'] = $record['ListID'];
			$result['date'] = $record['Date'];
			$result['ip'] = $record['IPAddress'];

			// organise the result set based on the clicked link
			$results[$result['url']][] = $result;
		}

		// return the records
		return $results;
	}


	/**
	 * Returns a list of all subscribers who opened a given campaign, and the number of times they opened the campaign
	 *
	 * @return	array
	 * @param	string $campaignId	The ID of the campaign you want data for.
	 * @param	int[optional] $timestamp			Opens made after the Date value specified will be returned. Must be in the format YYYY-MM-DD. Required.
	 * @param	int[optional] $page					The results page to retrieve. Default: 1
	 * @param	int[optional] $pageSize				The number of records to return (in docs referred to as 'pagesize'. Default 1000
	 * @param	string[optional] $orderField		The field which should be used to order the results. Default: date
	 * @param	string[optional] $orderDirection	The direction in which results should be ordered. Default: asc
	 */
	public function getCampaignOpens($campaignId, $timestamp = null, $page = 1, $pageSize = 1000, $orderField = 'date', $orderDirection = 'asc')
	{
		// set parameters
		$parameters['date'] = (string) date('Y-m-d', $timestamp);
		$parameters['page'] = (int) $page;
		$parameters['pagesize'] = (int) $pageSize;
		$parameters['orderfield'] = (string) $orderField;
		$parameters['orderdirection'] = (string) $orderDirection;

		// make the call
		$records = (array) $this->doCall('campaigns/'. $campaignId .'/opens', $parameters);

		// stop here if no records were set
		if(empty($records['Results'])) return array();

		// reserve variables
		$results = array();

		// loop the records
		foreach($records['Results'] as $key => $record)
		{
			$results[$key]['email'] = $record['EmailAddress'];
			$results[$key]['list_id'] = $record['ListID'];
			$results[$key]['date'] = $record['Date'];
			$results[$key]['ip'] = $record['IPAddress'];
		}

		// return the records
		return $results;
	}


	/**
	 * Provides a basic summary of the results for any sent campaign such as the number of recipients, opens, clicks, unsubscribes, etc to date. Also includes the URL of the web version of that campaign.
	 *
	 * @return	array
	 * @param	string $campaignId	The ID of the campaign you want data for.
	 */
	public function getCampaignSummary($campaignId)
	{
		// make the call
		$record = (array) $this->doCall('campaigns/'. $campaignId .'/summary');

		// stop here if no record was set
		if(empty($record)) return array();

		// reserve variable
		$result = array();

		// basic details
		$result['recipients'] = (int) $record['Recipients'];
		$result['opens'] = (int) $record['TotalOpened'];
		$result['unique_opens'] = (int) $record['UniqueOpened'];
		$result['clicks'] = (int) $record['Clicks'];
		$result['unsubscribes'] = (int) $record['Unsubscribed'];
		$result['bounces'] = (int) $record['Bounced'];
		$result['online_version'] = (string) $record['WebVersionURL'];

		// return the record
		return $result;
	}


	/**
	 * Returns a list of all subscribers who unsubscribed for a given campaign.
	 *
	 * @return	array
	 * @param	string $campaignId					The ID of the campaign you want data for.
	 * @param	int[optional] $timestamp			Unsubscribes made after the Date value specified will be returned. Must be in the format YYYY-MM-DD. Required.
	 * @param	int[optional] $page					The results page to retrieve. Default: 1
	 * @param	int[optional] $pageSize				The number of records to return (in docs referred to as 'pagesize'. Default 1000
	 * @param	string[optional] $orderField		The field which should be used to order the results. Default: date
	 * @param	string[optional] $orderDirection	The direction in which results should be ordered. Default: asc
	 */
	public function getCampaignUnsubscribes($campaignId, $timestamp = null, $page = 1, $pageSize = 1000, $orderField = 'date', $orderDirection = 'asc')
	{
		// set parameters
		$parameters['date'] = (string) date('Y-m-d', $timestamp);
		$parameters['page'] = (int) $page;
		$parameters['pagesize'] = (int) $pageSize;
		$parameters['orderfield'] = (string) $orderField;
		$parameters['orderdirection'] = (string) $orderDirection;

		// make the call
		$records = (array) $this->doCall('campaigns/'. $campaignId .'/unsubscribes', $parameters);

		// stop here if no records were set
		if(empty($records['Results'])) return array();

		// reserve variables
		$results = array();

		// loop the records
		foreach($records['Results'] as $key => $record)
		{
			$results[$key]['email'] = $record['EmailAddress'];
			$results[$key]['list_id'] = $record['ListID'];
			$results[$key]['date'] = $record['Date'];
			$results[$key]['ip'] = $record['IPAddress'];
		}

		// return the records
		return $results;
	}


	/**
	 * Returns the complete account and billing details for a particular client.
	 *
	 * @return	array
	 * @param	string[optional] $clientId	The ID of the client.
	 */
	public function getClient($clientId = null)
	{
		// set ID
		$clientId = empty($clientId) ? $this->getClientId() : $clientId;

		// make the call
		$record = (array) $this->doCall('clients/'. $clientId);

		// stop here if no record was set
		if(empty($record)) return array();

		// reserve variable
		$result = array();
		$details = $record['BasicDetails'];
		$access = $record['AccessDetails'];
		$billing = $record['BillingDetails'];

		// basic details
		$result['api_key'] = $record['ApiKey'];
		$result['client_id'] = $details['ClientID'];
		$result['company'] = $details['CompanyName'];
		$result['contact_name'] = $details['ContactName'];
		$result['email'] = $details['EmailAddress'];
		$result['country'] = $details['Country'];
		$result['timezone'] = $details['TimeZone'];

		// access info
		$result['username'] = empty($access['Username']) ? null : $access['Username'];
		$result['access_level'] = empty($access['AccessLevel']) ? null : $access['AccessLevel'];

		// billing info
		$result['can_purchase_credits'] = $billing['CanPurchaseCredits'];
		$result['base_delivery_rate'] = $billing['BaseDeliveryRate'];
		$result['base_rate_per_recipient'] = $billing['BaseRatePerRecipient'];
		$result['base_design_spam_test_rate'] = $billing['BaseDesignSpamTestRate'];
		$result['markup_on_delivery'] = $billing['MarkupOnDelivery'];
		$result['markup_per_recipient'] = $billing['MarkupPerRecipient'];
		$result['markup_on_design_spam_test'] = $billing['MarkupOnDesignSpamTest'];
		$result['currency'] = $billing['Currency'];
		$result['client_pays'] = $billing['ClientPays'];

		// return the record
		return $result;
	}


	/**
	 * Returns all clients for the logged-in user.
	 *
	 * @return	array
	 */
	public function getClients()
	{
		// make the call
		$records = (array) $this->doCall('clients');

		// stop here if no records were set
		if(empty($records)) return array();

		// reserve variable
		$results = array();
		$i = 0;

		// loop the records
		foreach($records as $record)
		{
			$results[$i]['id'] = $record['ClientID'];
			$results[$i]['name'] = $record['Name'];
			$i++;
		}

		// return the records
		return $results;
	}


	/**
	 * Returns the default set client ID
	 *
	 * @return	string
	 */
	public function getClientId()
	{
		return (string) $this->clientId;
	}


	/**
	 * Returns all countries for the logged-in user.
	 *
	 * @return	array
	 */
	public function getCountries()
	{
		return (array) $this->doCall('countries');
	}


	/**
	 * Returns all the custom fields available for a list.
	 *
	 * @return	array
	 * @param	string[optional] $listId	The list ID to fetch the custom fields from
	 */
	public function getCustomFields($listId = null)
	{
		// set ID
		$listId = empty($listId) ? $this->getListId() : $listId;

		// make the call
		$records = (array) $this->doCall('lists/'. $listId .'/customfields');

		// stop here if no records were set
		if(empty($records)) return array();

		// reserve variable
		$results = array();

		// loop the records
		foreach($records as $key => $record)
		{
			$results[$key]['name'] = $record['FieldName'];
			$results[$key]['type'] = $record['DataType'];

			// field options found
			if(!empty($record['FieldOptions']))
			{
				// loop the options
				foreach($record['FieldOptions'] as $option) $results[$key]['options'][] = $option;
			}
		}

		// return the records
		return $results;
	}


	/**
	 * Returns a list of all draft campaigns for a client
	 *
	 * @return	array
	 * @param	string[optional] $clientId	The ID of the client.
	 */
	public function getDraftCampaignsByClientID($clientId = null)
	{
		// set ID
		$clientId = empty($clientId) ? $this->getClientId() : $clientId;

		// make the call
		$records = (array) $this->doCall('clients/'. $clientId .'/drafts');

		// stop here if no record was set
		if(empty($records)) return array();

		// reserve variable
		$results = array();

		// loop the records
		foreach($records as $key => $record)
		{
			// set result values
			$results[$key]['campaign_id'] = $record['CampaignID'];
			$results[$key]['subject'] = $record['Subject'];
			$results[$key]['name'] = $record['Name'];
			$results[$key]['date_created'] = $record['DateCreated'];
			$results[$key]['preview_url'] = $record['PreviewURL'];
		}

		// return the results
		return $results;
	}


	/**
	 * Returns a list's configuration detail
	 *
	 * @return	array
	 * @param	string[optional] $listId	The ID of the list.
	 */
	public function getList($listId = null)
	{
		// set ID
		$listId = empty($listId) ? $this->getListId() : $listId;

		// make the call
		$record = (array) $this->doCall('lists/'. $listId);

		// stop here if no record was set
		if(empty($record)) return array();

		// reserve variable
		$result = array();

		// basic details
		$result['id'] = $record['ListID'];
		$result['title'] = $record['Title'];
		$result['unsubscribe_url'] = empty($record['UnsubscribePage']) ? $this->siteURL . '/t/GenericUnsubscribe' : $record['UnsubscribePage'];
		$result['confirm_optin'] = (bool) $record['ConfirmedOptIn'];
		$result['confirmation_success_url'] = empty($record['ConfirmationSuccessPage']) ? $this->siteURL . '/t/Confirmed' : $record['ConfirmationSuccessPage'];

		// return the record
		return $result;
	}


	/**
	 * Returns the default set list ID
	 *
	 * @return	string
	 */
	public function getListId()
	{
		return (string) $this->listId;
	}


	/**
	 * Returns the list stats
	 *
	 * @return	array
	 * @param	string[optional] $listId	The ID of the list.
	 */
	public function getListStatistics($listId = null)
	{
		// set ID
		$listId = empty($listId) ? $this->getListId() : $listId;

		// make the call
		$record = (array) $this->doCall('lists/'. $listId .'/stats');

		// stop here if no record was set
		if(empty($record)) return array();

		// reserve variable
		$result = array();

		// basic details
		$result['total_subscribers'] = $record['TotalActiveSubscribers'];
		$result['total_unsubscribers'] = $record['TotalUnsubscribes'];
		$result['total_deleted'] = $record['TotalDeleted'];
		$result['total_bounces'] = $record['TotalBounces'];
		$result['subscribers_today'] = $record['NewActiveSubscribersToday'];
		$result['subscribers_yesterday'] = $record['NewActiveSubscribersYesterday'];
		$result['subscribers_week'] = $record['NewActiveSubscribersThisWeek'];
		$result['subscribers_month'] = $record['NewActiveSubscribersThisMonth'];
		$result['subscribers_year'] = $record['NewActiveSubscribersThisYear'];
		$result['unsubscribers_today'] = $record['UnsubscribesToday'];
		$result['unsubscribers_yesterday'] = $record['UnsubscribesYesterday'];
		$result['unsubscribers_week'] = $record['UnsubscribesThisWeek'];
		$result['unsubscribers_month'] = $record['UnsubscribesThisMonth'];
		$result['unsubscribers_year'] = $record['UnsubscribesThisYear'];
		$result['deleted_today'] = $record['DeletedToday'];
		$result['deleted_yesterday'] = $record['DeletedYesterday'];
		$result['deleted_week'] = $record['DeletedThisWeek'];
		$result['deleted_month'] = $record['DeletedThisMonth'];
		$result['deleted_year'] = $record['DeletedThisYear'];
		$result['bounces_today'] = $record['BouncesToday'];
		$result['bounces_yesterday'] = $record['BouncesYesterday'];
		$result['bounces_week'] = $record['BouncesThisWeek'];
		$result['bounces_month'] = $record['BouncesThisMonth'];
		$result['bounces_year'] = $record['BouncesThisYear'];

		// return the record
		return $result;
	}


	/**
	 * Returns a list of all subscriber lists for a campaign.
	 *
	 * @return	array
	 * @param	string $campaignId	The ID of the campaign.
	 */
	public function getListsByCampaignId($campaignId)
	{
		// set parameters
		$parameters['CampaignID'] = (string) $campaignId;

		// make the call
		$records = (array) $this->doCall('campaigns/'. $campaignId .'/listsandsegments', $parameters);

		// stop here if no records were set
		if(empty($records)) return array();

		// reserve variable
		$results = array();
		$i = 0;

		// loop the list and segment records
		foreach($records['Lists'] as $record)
		{
			$results[$i]['id'] = $record['ListID'];
			$results[$i]['name'] = $record['Name'];
			$i++;
		}

		// return the records
		return $results;
	}


	/**
	 * Returns a list of all subscriber lists for a client.
	 *
	 * @return	array
	 * @param	string[optional] $clientId	The ID of the client.
	 */
	public function getListsByClientId($clientId = null)
	{
		// set ID
		$clientId = empty($clientId) ? $this->getClientId() : $clientId;

		// make the call
		$records = (array) $this->doCall('clients/'. $clientId .'/lists');

		// stop here if no records were set
		if(empty($records)) return array();

		// reserve variables
		$results = array();

		// loop the records
		foreach($records as $key => $record)
		{
			$results[$key]['id'] = $record['ListID'];
			$results[$key]['name'] = $record['Name'];
		}

		// return the records
		return $results;
	}


	/**
	 * Get the password
	 *
	 * @return	string
	 */
	private function getPassword()
	{
		return (string) $this->password;
	}


	/**
	 * Get response format
	 *
	 * @return	void
	 */
	public function getResponseFormat()
	{
		return $this->responseFormat;
	}


	/**
	 * Returns a list of all segments for a campaign.
	 *
	 * @return	array
	 * @param	string $campaignId	The ID of the campaign.
	 */
	public function getSegmentsByCampaignId($campaignId)
	{
		// set parameters
		$parameters['CampaignID'] = (string) $campaignId;

		// make the call
		$records = (array) $this->doCall('campaigns/'. $campaignId .'/listsandsegments', $parameters);

		// stop here if no records were set
		if(empty($records)) return array();

		// reserve variable
		$results = array();
		$i = 0;

		// loop the list and segment records
		foreach($records['Segments'] as $record)
		{
			$results[$i]['id'] = $record['SegmentID'];
			$results[$i]['list_id'] = $record['ListID'];
			$results[$i]['name'] = $record['Name'];
			$i++;
		}

		// return the records
		return $results;
	}


	/**
	 * Returns a list of all campaigns that have been sent for a client
	 *
	 * @return	array
	 * @param	string[optional] $clientId	The ID of the client.
	 */
	public function getSentCampaignsByClientID($clientId = null)
	{
		// set ID
		$clientId = empty($clientId) ? $this->getClientId() : $clientId;

		// make the call
		$records = (array) $this->doCall('clients/'. $clientId .'/campaigns');

		// stop here if no record was set
		if(empty($records)) return array();

		// reserve variable
		$results = array();

		// loop the records
		foreach($records as $key => $record)
		{
			// set result values
			$results[$key]['campaign_id'] = $record['CampaignID'];
			$results[$key]['subject'] = $record['Subject'];
			$results[$key]['name'] = $record['Name'];
			$results[$key]['date_sent'] = $record['SentDate'];
			$results[$key]['total_recipients'] = $record['TotalRecipients'];
		}

		// return the results
		return $results;
	}


	/**
	 * Set the site URL
	 *
	 * @return	void
	 */
	private function getSiteURL()
	{
		return (string) $this->siteURL;
	}


	/**
	 * Returns the details of a particular subscriber, including email address, name, active/inactive status and all custom field data. If a subscriber with that email address does not exist in that list, an empty record is returned.
	 *
	 * @return	array
	 * @param	string $email				The emailaddress.
	 * @param	string[optional] $listId	The list ID to fetch the subscriber from
	 */
	public function getSubscriber($email, $listId = null)
	{
		// stop here if no email was set
		if(!$email) throw new CampaignMonitorException('No e-mail given.');

		// set ID
		$listId = empty($listId) ? $this->getListId() : $listId;

		// set parameters
		$parameters['email'] = (string) $email;

		// make the call
		$record = (array) $this->doCall('subscribers/'. $listId, $parameters);

		// stop here if no record was set
		if(empty($record)) return array();

		// set values
		$results['email'] = $record['EmailAddress'];
		$results['name'] = $record['Name'];
		$results['date'] = $record['Date'];
		$results['status'] = $record['State'];

		// check if there are clickedlinks present
		if(empty($record['CustomFields'])) continue;

		// loop records
		foreach($record['CustomFields'] as $field)
		{
			// set values
			$results['custom_fields'][$field['Key']] = $field['Value'];
		}

		// return the records
		return $results;
	}


	/**
	 * Returns a list of all active subscribers for a list that have been added since the specified date.
	 * http://www.campaignmonitor.com/api/lists/#getting_active_subscribers
	 *
	 * @return	array
	 * @param	string[optional] $listId			The ID of the list.
	 * @param	int[optional] $timestamp			Subscribers which became active on or after the Date value specified will be returned. Must be in the format YYYY-MM-DD. Required
	 * @param	int[optional] $page					The results page to retrieve. Default: 1
	 * @param	int[optional] $pageSize				The number of records to return (in docs referred to as 'pagesize'. Default 1000
	 * @param	string[optional] $orderField		The field which should be used to order the results. Default: date
	 * @param	string[optional] $orderDirection	The direction in which results should be ordered. Default: asc
	 */
	public function getSubscribers($listId = null, $timestamp = null, $page = 1, $pageSize = 1000, $orderField = 'date', $orderDirection = 'asc')
	{
		// set ID
		$listId = empty($listId) ? $this->getListId() : $listId;

		// check input
		if(empty($timestamp)) $timestamp = strtotime('10 years ago');

		// set parameters
		$parameters['date'] = (string) date('Y-m-d', $timestamp);
		$parameters['page'] = (int) $page;
		$parameters['pagesize'] = (int) $pageSize;
		$parameters['orderfield'] = (string) $orderField;
		$parameters['orderdirection'] = (string) $orderDirection;

		// make the call
		$records = (array) $this->doCall('lists/'. $listId .'/active', $parameters);

		// stop here if no records were set
		if(empty($records['Results'])) return array();

		// reserve variables
		$results = array();

		// loop the records
		foreach($records['Results'] as $key => $record)
		{
			// set values
			$results[$key]['email'] = $record['EmailAddress'];
			$results[$key]['name'] = $record['Name'];
			$results[$key]['date'] = $record['Date'];
			$results[$key]['status'] = $record['State'];

			// check if there are custom fields present
			if(empty($record['CustomFields'])) continue;

			// loop records
			foreach($record['CustomFields'] as $field)
			{
				// set values
				$results[$key]['custom_fields'][$field['Key']] = $field['Value'];
			}
		}

		// return the records
		return $results;
	}


	/**
	 * Returns all subscribers in the client-wide suppression list.
	 *
	 * @return	array
	 * @param	string[optional] $clientId			The ID of the client for which their suppression list should be retrieved.
	 * @param	int[optional] $page					The results page to retrieve. Default: 1
	 * @param	int[optional] $pageSize				The number of records to return (in docs referred to as 'pagesize'. Default 1000
	 * @param	string[optional] $orderField		The field which should be used to order the results. Default: email
	 * @param	string[optional] $orderDirection	The direction in which results should be ordered. Default: asc

	 */
	public function getSuppressionListByClientId($clientId = null, $page = 1, $pageSize = 1000, $orderField = 'email', $orderDirection = 'asc')
	{
		// set ID
		$clientId = empty($clientId) ? $this->getClientId() : $clientId;

		// set parameters
		$parameters['page'] = (int) $page;
		$parameters['pagesize'] = (int) $pageSize;
		$parameters['orderfield'] = (string) $orderField;
		$parameters['orderdirection'] = (string) $orderDirection;

		// make the call
		$records = (array) $this->doCall('clients/'. $clientId .'/suppressionlist', $parameters);

		// stop here if no records were set
		if(empty($records['Results'])) return array();

		// reserve variables
		$results = array();

		// loop the records
		foreach($records['Results'] as $key => $record)
		{
			$results[$key]['email'] = $record['EmailAddress'];
			$results[$key]['date'] = $record['Date'];
			$results[$key]['state'] = $record['State'];
		}

		// return the records
		return $results;
	}


	/**
	 * Returns the server system time for the logged-in user's time zone.
	 *
	 * @return	string
	 */
	public function getSystemDate()
	{
		$result = $this->doCall('systemdate');

		return $result['SystemDate'];
	}


	/**
	 * Returns a template's configuration detail
	 *
	 * @return	array
	 * @param	string $templateId	The ID of the template.
	 */
	public function getTemplate($templateId)
	{
		// make the call
		$record = (array) $this->doCall('templates/'. $templateId);

		// stop here if no record was set
		if(empty($record)) return array();

		// reserve variable
		$result = array();

		// basic details
		$result['id'] = $record['TemplateID'];
		$result['name'] = $record['Name'];
		$result['preview_url'] = (bool) $record['PreviewURL'];
		$result['screenshot_url'] = $record['ScreenshotURL'];

		// return the record
		return $result;
	}


	/**
	 * Returns a list of all templates for a client.
	 * http://www.campaignmonitor.com/api/clients/#getting_client_templates
	 *
	 * @return	array
	 * @param	string[optional] $clientId	The ID of the client.
	 */
	public function getTemplates($clientId = null)
	{
		// set ID
		$clientId = empty($clientId) ? $this->getClientId() : $clientId;

		// make the call
		$records = (array) $this->doCall('clients/'. $clientId .'/templates');

		// stop here if no records were set
		if(empty($records)) return array();

		// reserve variables
		$results = array();

		// loop the records
		foreach($records as $key => $record)
		{
			$results[$key]['id'] = $record['TemplateID'];
			$results[$key]['name'] = $record['Name'];
			$results[$key]['preview_url'] = $record['PreviewURL'];
			$results[$key]['screenshot_url'] = $record['ScreenshotURL'];
		}

		// return the records
		return $results;
	}


	/**
	 * Get the timeout
	 *
	 * @return	int
	 */
	public function getTimeOut()
	{
		return (int) $this->timeOut;
	}


	/**
	 * Returns all timezones for the logged-in user.
	 *
	 * @return	array
	 */
	public function getTimezones()
	{
		return (array) $this->doCall('timezones');
	}


	/**
	 * Contains a paged result representing all the unsubscribed subscribers for a given list. This includes their email address, name, date unsubscribed and any custom field data. You have complete control over how results should be returned including page sizes, sort order and sort direction.
	 *
	 * @return	array
	 * @param	string[optional] $listId			The list ID to fetch the unsubscribers from
	 * @param	int[optional] $timestamp			Subscribers which unsubscribed on or after the Date value specified will be returned. Must be in the format YYYY-MM-DD. Required
	 * @param	int[optional] $page					The results page to retrieve. Default: 1
	 * @param	int[optional] $pageSize				The number of records to return (in docs referred to as 'pagesize'. Default 1000
	 * @param	string[optional] $orderField		The field which should be used to order the results. Default: date
	 * @param	string[optional] $orderDirection	The direction in which results should be ordered. Default: asc
	 */
	public function getUnsubscribes($listId = null, $timestamp = null, $page = 1, $pageSize = 1000, $orderField = 'email', $orderDirection = 'asc')
	{
		// set ID
		$listId = empty($listId) ? $this->getListId() : $listId;

		// check timestamp
		if(empty($timestamp)) $timestamp = strtotime('last week');

		// set parameters
		$parameters['date'] = (string) date('Y-m-d', $timestamp);
		$parameters['page'] = (int) $page;
		$parameters['pagesize'] = (int) $pageSize;
		$parameters['orderfield'] = (string) $orderField;
		$parameters['orderdirection'] = (string) $orderDirection;

		// make the call
		$records = (array) $this->doCall('lists/'. $listId .'/unsubscribed', $parameters);

		// stop here if no records were set
		if(empty($records['Results'])) return array();

		// reserve variables
		$results = array();

		// loop the records
		foreach($records['Results'] as $key => $record)
		{
			// set values
			$results[$key]['email'] = $record['EmailAddress'];
			$results[$key]['name'] = $record['Name'];
			$results[$key]['date'] = $record['Date'];
			$results[$key]['status'] = $record['State'];
			$results[$key]['custom_fields'] = array();

			// check if there are clickedlinks present
			if(empty($record['CustomFields'])) continue;

			// loop records
			foreach($record['CustomFields'] as $field)
			{
				// set values
				$results[$key]['custom_fields'][$field['Key']] = $field['Value'];
			}
		}

		// return the records
		return $results;
	}


	/**
	 * Get the useragent that will be used. Our version will be prepended to yours.
	 * It will look like: "PHP CampaignMonitor/<version> <your-user-agent>"
	 *
	 * @return	string
	 */
	public function getUserAgent()
	{
		return (string) 'PHP CampaignMonitor/'. self::API_VERSION .' '. $this->userAgent;
	}


	/**
	 * Returns the username
	 *
	 * @return	string
	 */
	private function getUsername()
	{
		return (string) $this->username;
	}


	/**
	 * Imports "many" (CM only allows 100 max for this method) subscribers. Returns the rows that did not import.
	 *
	 * @return	array		The failed results.
	 * @param	array $subscribers
	 * @param	string[optional] $listId
	 */
	public function importSubscribers($subscribers, $listId = null)
	{
		// set parameters
		$parameters['Subscribers'] = $subscribers;
		$parameters['Resubscribe'] = 'true';

		// make the call
		$results = $this->doCall('subscribers/'. $listId .'/import', $parameters, 'POST');

		// check if we have failed results in the result set
		if(empty($results['ResultData']['FailureDetails'])) return array();

		// reserve failed stack
		$failed = array();

		// loop the failed results
		foreach($results['ResultData']['FailureDetails'] as $result)
		{
			$failed[] = $result['EmailAddress'];
		}

		// return the failed results
		return $failed;
	}


	/**
	 * Schedules an existing campaign for sending. The campaign must be imported with defined recipients. For campaigns with more than 5 recipients the user must have sufficient credits or their credit card details saved within the application for the campaign to be sent via the API. For campaigns with 5 recipients or less the user must have enough test campaigns remaining in their API account.
	 *
	 * @return	bool
	 * @param	string $campaignId	The ID of the campaign to send.
	 * @param	string $confirmationEmail		The email address where the confirmation email will be sent to.
	 * @param	string[optional] $deliveryDate	The date the campaign should be scheduled to be sent. (YYYY-MM-DD HH:MM:SS)
	 */
	public function sendCampaign($campaignId, $confirmationEmail, $deliveryDate = null)
	{
		// set parameters
		$parameters['ConfirmationEmail'] = (string) $confirmationEmail;
		$parameters['SendDate'] = $deliveryDate;

		// make the call
		return $this->doCall('campaigns/'. $campaignId .'/send', $parameters, 'POST');
	}


	/**
	 * This sends a preview campaign based for a given campaign ID.
	 *
	 * @return	bool
	 * @param	string $campaignId	The ID of the campaign to send.
	 * @param	mixed $recipients This can be an e-mail address string, or an array of addresses.
	 * @param	string[optional] $personalization This can be 'Fallback','Random', or a specific e-mail address.
	 */
	public function sendCampaignPreview($campaignId, $recipients, $personalization = 'Fallback')
	{
		$recipients = !is_array($recipients) ? array($recipients) : $recipients;

		// set parameters
		$parameters['PreviewRecipients'] = $recipients;
		$parameters['SendDate'] = $personalization;

		// make the call
		return $this->doCall('campaigns/'. $campaignId .'/sendpreview', $parameters, 'POST');
	}


	/**
	 * Set API key
	 *
	 * @return	void
	 * @param	string $key
	 */
	private function setAPIKey($key)
	{
		$this->apiKey = (string) $key;
	}


	/**
	 * Set the default client ID to use
	 *
	 * @return	void
	 * @param	string $id
	 */
	public function setClientId($id)
	{
		$this->clientId = (string) $id;
	}


	/**
	 * Set the default list ID to use
	 *
	 * @return	void
	 * @param	string $id
	 */
	public function setListId($id)
	{
		$this->listId = (string) $id;
	}


	/**
	 * Set password
	 *
	 * @return	void
	 * @param	string $password
	 */
	private function setPassword($password)
	{
		$this->password = (string) $password;
	}


	/**
	 * Set response format
	 *
	 * @return	void
	 * @param	string[optional] $format
	 */
	public function setResponseFormat($format = 'json')
	{
		$this->responseFormat = in_array($format, array('json', 'xml')) ? $format : 'json';
	}


	/**
	 * Set the site URL
	 *
	 * @return	void
	 * @param	string $siteURL		The base URL of the site you use to login to Campaign Monitor. e.g. http://example.createsend.com/
	 */
	public function setSiteURL($siteURL)
	{
		$this->siteURL = (string) $siteURL;
	}


	/**
	 * Set the timeout
	 *
	 * @return	void
	 * @param	int $seconds	The timeout in seconds.
	 */
	private function setTimeOut($seconds)
	{
		$this->timeOut = (int) $seconds;
	}


	/**
	 * Set username
	 *
	 * @return	void
	 * @param	string $username
	 */
	private function setUsername($username)
	{
		$this->username = (string) $username;
	}


	/**
	 * Adds a subscriber to an existing subscriber list. If $resubscribe is set to true, it will resubscribe the email in question if it's not active in the list
	 *
	 * @return	bool
	 * @param	string $email					The email address of the new subscriber.
	 * @param	string[optional] $name			The name of the new subscriber.
	 * @param	array[optional] $customFields	The custom fields for this subscriber in key/value pairs.
	 * $param	bool[optional] $resubscribe		Subscribes an unsubscribed email address back to the list if this is true.
	 * $param	string[optional] $listId		The list you want to add the subscriber to.
	 */
	public function subscribe($email, $name = null, $customFields = array(), $resubscribe = true, $listId = null)
	{
		return $this->addSubscriber($email, $name, $customFields, $resubscribe, $listId);
	}


	/**
	 * Changes the status of an Active Subscriber to an Unsubscribed Subscriber who will no longer receive campaigns sent to that Subscriber List.
	 *
	 * @return	bool
	 * @param	string $email				The emailaddress.
	 * @param	string[optional] $listId	The list ID to unsubscribe from
	 */
	public function unsubscribe($email, $listId = null)
	{
		// stop here if no email was set
		if(!$email) throw new CampaignMonitorException('No e-mail given.');

		// set ID
		$listId = empty($listId) ? $this->getListId() : $listId;

		// set parameters
		$parameters['EmailAddress'] = (string) $email;

		// make the call
		return $this->doCall('subscribers/'. $listId .'/unsubscribe', $parameters, 'POST');
	}


	/**
	 * Updates a client's basic settings.
	 *
	 * @return	bool
	 * @param	string $companyName			The client company name.
	 * @param	string $contactName			The personal name of the principle contact for this client.
	 * @param	string $email				An email address to which this client will be sent application-related emails.
	 * @param	string $country				This client's country.
	 * @param	string $timezone			Client timezone for tracking and reporting data.
	 * @param	string[optional] $clientId	The client ID to update.
	 */
	public function updateClientBasics($companyName, $contactName, $email, $country, $timezone, $clientId = null)
	{
		// set ID
		$clientId = empty($clientId) ? $this->getClientId() : $clientId;

		// fetch the country list
		$countries = $this->getCountries();
		$timezones = $this->getTimezones();

		// check if $country is in the allowed country list
		if(!in_array($country, $countries)) throw new CampaignMonitorException('No valid country provided');

		// check if $timezone is in the allowed timezones list
		if(!in_array($timezone, $timezones)) throw new CampaignMonitorException('No valid timezone provided');

		// set parameters
		$parameters['CompanyName'] = (string) $companyName;
		$parameters['ContactName'] = (string) $contactName;
		$parameters['EmailAddress'] = (string) $email;
		$parameters['Country'] = (string) $country;
		$parameters['Timezone'] = (string) $timezone;

		// try and update the record
		return $this->doCall('clients/'. $clientId .'/setbasics', $parameters, 'PUT');
	}


	/**
	 * Updates a subscriber list's details.
	 *
	 * @return	bool
	 * @param	string $title								The title of the list.
	 * @param	string[optional] $unsubscribePage			The URL to which subscribers will be directed when unsubscribing from the list. If left blank or omitted a generic unsubscribe page is used.
	 * @param	bool[optional] $confirmOptIn				Either true or false depending on whether the list requires email confirmation or not. Please see the help documentation for more details of what this means.
	 * @param	string[optional] $confirmationSuccessPage	Successful email confirmations will be redirected to this URL. Ignored if ConfirmOptIn is false. If left blank or omitted a generic confirmation page is used.
	 * @param	string[optional] $listId					The list ID to update
	 */
	public function updateList($title, $unsubscribePage = null, $confirmOptIn = false, $confirmationSuccessPage = null, $listId = null)
	{
		// set list ID
		$listId = empty($listId) ? $this->getListId() : $listId;

		// set parameters
		$parameters['Title'] = (string) $title;
		$parameters['UnsubscribePage'] = (string) $unsubscribePage;
		$parameters['ConfirmOptIn'] = $confirmOptIn ? true : false;
		$parameters['ConfirmationSuccessPage'] = (string) $confirmationSuccessPage;

		// try and update the record
		return $this->doCall('lists/'. $listId, $parameters, 'PUT');
	}


	/**
	 * Updates a template.
	 *
	 * @return	bool
	 * @param	string $templateId				The ID of the template.
	 * @param	string[optional] $name			The name of the template. Maximum of 30 characters (will be truncated to 30 characters if longer).
	 * @param	string[optional] $HTMLPageURL	The URL of the HTML page you have created for the template.
	 * @param	string[optional] $zipFileURL	Optional URL of a zip file containing any other files required by the template.
	 * @param	string[optional] $screenshotURL	Optional URL of a screenshot of the template. Must be in jpeg format and at least 218 pixels wide.
	 */
	public function updateTemplate($templateId, $name = null, $HTMLPageURL = null, $zipFileURL = null, $screenshotURL = null)
	{
		// set parameters
		$parameters['TemplateName'] = (string) $name;
		$parameters['HTMLPageURL'] = (string) $HTMLPageURL;
		$parameters['ZipFileURL'] = (string) $zipFileURL;
		$parameters['ScreenshotURL'] = (string) $screenshotURL;

		// try and update the record
		return (bool) $this->doCall('templates/'. $templateId, $parameters, 'PUT');
	}
}


/**
 * CampaignMonitor Exception class
 *
 * @author	Dave Lens <dave@netlash.com>
 */
class CampaignMonitorException extends Exception
{
}