<?php

/**
 * CampaignMonitor class
 *
 * This source file can be used to communicate with CampaignMonitor (http://www.campaignmonitor.com/api/)
 *
 * Changelog since 0.0.1
 * - initial version is done
 *
 * License
 * Copyright (c) 2010, Dave Lens. All rights reserved.
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
 * @version		0.0.1
 *
 * @copyright	Copyright (c) 2010, Netlash. All rights reserved.
 * @license		BSD License
 */
class CampaignMonitor
{
	// internal constant to enable/disable debugging
	const DEBUG = false;

	// url for the campaignmonitor API
	const API_URL = 'http://api.createsend.com/api/api.asmx?wsdl';

	// port for the campaignmonitor API
	const API_PORT = 80;

	// current version
	const VERSION = '1.0.0';

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
	 * The username for an authenticating user
	 *
	 * @var	string
	 */
	private $username;


	/**
	 * Class constructor
	 *
	 * @return	void
	 * @param	string $siteURL					The base URL of the site you use to login to Campaign Monitor.
	 * @param	string $username				The username you use to login to Campaign Monitor.
	 * @param	string $password				The password you use to login to Campaign Monitor.
	 * @param	int[optional] $timeOut			The timeout.
	 * @param	string[optional] $clientId		The default client ID to use throughout the class.
	 * @param	string[optional] $listId		The default list ID to use throughout the class.
	 * @param	string[optional] $campaignId	The default campaign ID to use throughout the class.
	 */
	public function __construct($siteURL, $username, $password, $timeOut = 60, $clientId = null, $listId = null, $campaignId = null)
	{
		// check input
		if(empty($siteURL)) throw new CampaignMonitorException('No site URL given.');
		if(empty($username) || empty($password)) throw new CampaignMonitorException('No username/password set.', 105);

		// set parameters
		$parameters = array();
		$parameters['SiteUrl'] = (string) $siteURL;
		$parameters['Username'] = (string) $username;
		$parameters['Password'] = (string) $password;

		// set the site URL + client, list and campaign IDs
		$this->setSiteURL($siteURL);
		$this->setClientId((string) $clientId);
		$this->setListId((string) $listId);
		$this->setCampaignId((string) $campaignId);
		$this->setTimeOut((int) $timeOut);

		// set connection options
		$options['connection_timeout'] = $timeOut;
		$options['trace'] = true;
		$options['exceptions'] = true;

		try
		{
			// make soap connection
			$this->soap = new SoapClient(self::API_URL, $options);

			// fetch our API key for this user
			$this->apiKey = (string) $this->doCall('User.GetApiKey', $parameters);
		}
		catch(Exception $e)
		{
			throw new CampaignMonitorException($e->getMessage(), 408);
		}
	}


	/**
	 * Adds a subscriber to an existing subscriber list. If $resubscribe is set to true, it will resubscribe the e-mail address if not active.
	 *
	 * @return	bool
	 * @param	string $email					The email address of the new subscriber.
	 * @param	string $name					The name of the new subscriber. If the name is unknown, an empty string can be passed in.
	 * @param	array[optional] $customFields	The custom fields for this subscriber in key/value pairs.
	 * @param	bool[optional] $resubscribe		Subscribes an unsubscribed email address back to the list if this is true.
	 * @param	string[optional] $listId		The list you want to add the subscriber to.
	 */
	public function addSubscriber($email, $name, $customFields = array(), $resubscribe = true, $listId = null)
	{
		// set list ID
		$listId = empty($listId) ? $this->getListId() : $listId;

		// set parameters
		$parameters['Email'] = (string) $email;
		$parameters['Name'] = (string) $name;
		$parameters['ListID'] = (string) $listId;

		// set custom fields if any were found
		if(!empty($customFields))
		{
			// fetch all existing custom fields
			$currentFields = (array) $this->getCustomFields($listId);

			// loop the custom fields, build a new array
			foreach($currentFields as $key => $field) $currentFields[$key] = $field['name'];

			// loop the fields
			foreach($customFields as $key => $value)
			{
				// check if this field already exists; if not, add it.
				if(!in_array($key, $currentFields)) $this->createCustomField($key, 'text', null, $listId);

				// add it to the list of field values
				$parameters['CustomFields']['SubscriberCustomField'][] = array('Key' => $key, 'Value' => $value);
			}
		}

		// fetch the right method
		if($resubscribe) $method = (empty($customFields) ? 'Subscriber.AddAndResubscribe' : 'Subscriber.AddAndResubscribeWithCustomFields');
		else $method = (empty($customFields) ? 'Subscriber.Add' : 'Subscriber.AddWithCustomFields');

		// make the call
		$this->doCall($method, $parameters);

		// if we made it here, return true
		return true;
	}


	/**
	 * Creates a campaign. Returns the campaign ID when succesful or false if the call failed
	 *
	 * @return	mixed
	 * @param	string $name								The name of the new campaign. This must be unique across all draft campaigns for the client.
	 * @param	string $subject								The subject of the new campaign.
	 * @param	string $fromName							The name to appear in the From field in the recipients email client when they receive the new campaign.
	 * @param	string $fromEmail							The email address that the new campaign will come from.
	 * @param	string $replyToEmail						The email address that any replies to the new campaign will be sent to.
	 * @param	string $HTMLContentURL						The URL of the HTML content for the new campaign.
	 * @param	string $textContentURL						The URL of the text content for the new campaign.
	 * @param	array $subscriberLists						An array of lists to send the campaign to.
	 * @param	array[optional] $subscriberListSegments		An array of Segment Names and their appropriate List ID’s to send the campaign to.
	 * @param	string[optional] $clientId					The ID of the client who will be owner of the campaign.
	 */
	public function createCampaign($name, $subject, $fromName, $fromEmail, $replyToEmail, $HTMLContentURL, $textContentURL, array $subscriberLists, array $subscriberListSegments = array(), $clientId = null)
	{
		// set client ID
		$clientId = empty($clientId) ? $this->getClientId() : $clientId;

		// set parameters
		$parameters['ClientID'] = (string) $clientId;
		$parameters['CampaignName'] = (string) $name;
		$parameters['CampaignSubject'] = (string) $subject;
		$parameters['FromName'] = (string) $fromName;
		$parameters['FromEmail'] = (string) $fromEmail;
		$parameters['ReplyTo'] = (string) $replyToEmail;
		$parameters['HtmlUrl'] = (string) $HTMLContentURL;
		$parameters['TextUrl'] = (string) $textContentURL;
		$parameters['ApiKey'] = $this->getAPIKey();

		// subscriberlists found
		if(!empty($subscriberLists))
		{
			// loop the subscriberlists
			foreach($subscriberLists as $list) $parameters['SubscriberListIDs'][] = $list;
		}

		// subscriberlist segments found
		if(!empty($subscriberListSegments))
		{
			// loop the subscriberlist segments
			foreach($subscriberListSegments as $segment) $parameters['ListSegments'][] = $segment;
		}

		// return the result
		return (string) $this->doCall('Campaign.Create', $parameters);
	}


	/**
	 * Creates a client. Returns the client ID when succesful or false if the call failed
	 *
	 * @return	mixed
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
		return (string) $this->doCall('Client.Create', $parameters);
	}


	/**
	 * Creates a custom field for a list.
	 *
	 * @return	bool
	 * @param	string $name				The name of the field.
	 * @param	string[optional] $type		The type of the field to create, possible values are: string, int, text, number, multiSelectOne, multiSelectMany.
	 * @param	array[optional] $options	The available options for a multi-valued custom field. Options should be separated by a double pipe "||". This field must be null for Text and Number custom fields.
	 * @param	string[optional] $listId	The list ID to create the custom field for.
	 */
	public function createCustomField($name, $type = null, $options = array(), $listId = null)
	{
		// set list ID
		$listId = empty($listId) ? $this->getListId() : $listId;

		// check if the given type is allowed
		if(!empty($type) && !in_array($type, array('string', 'int', 'text', 'number', 'multiSelectOne', 'multiSelectMany')))
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
		$parameters['ListID'] = (string) $listId;
		$parameters['FieldName'] = (string) $name;
		$parameters['DataType'] = (string) ucfirst($type);

		// options found
		if(!empty($options))
		{
			// implode the options
			$options = implode('||', $options);

			// set options
			$parameters['Options'] = (string) $options;
		}

		// try and create the record
		return (bool) $this->doCall('List.CreateCustomField', $parameters);
	}


	/**
	 * Creates a list. Returns the list ID when succesful or false if the call failed
	 *
	 * @return	mixed
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
		$parameters['ClientID'] = (string) $clientId;
		$parameters['Title'] = (string) $title;
		$parameters['UnsubscribePage'] = (string) $unsubscribePage;
		$parameters['ConfirmOptIn'] = $confirmOptIn ? true : false;
		$parameters['ConfirmationSuccessPage'] = (string) $confirmationSuccessPage;

		// try and create the record
		try
		{
			$result = (string) $this->doCall('List.Create', $parameters);
		}

		// stop here if an exception is found
		catch(Exception $e)
		{
			$result = false;
		}

		// if we made it here, the record exists
		return $result;
	}


	/**
	 * Creates a template. Returns the template ID when succesful or false if the call failed
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
		$parameters['ClientID'] = (string) $clientId;
		$parameters['TemplateName'] = (string) $name;
		$parameters['HTMLPageURL'] = (string) $HTMLPageURL;
		$parameters['ZipFileURL'] = (string) $zipFileURL;
		$parameters['ScreenshotURL'] = (string) $screenshotURL;

		// try and create the template record
		try
		{
			$templateId = (string) $this->doCall('Template.Create', $parameters);
		}

		// stop here if an exception is found
		catch(Exception $e)
		{
			$templateId = false;
		}

		// if we made it here, the template exists
		return $templateId;
	}


	/**
	 * Deletes a campaign
	 *
	 * @return	bool
	 * @param	string[optional] $campaignId	The ID of the campaign.
	 */
	public function deleteCampaign($campaignId = null)
	{
		// set ID
		$campaignId = empty($campaignId) ? $this->getCampaignId() : $campaignId;

		// set parameters
		$parameters['CampaignID'] = (string) $campaignId;

		// make the call
		return (bool) $this->doCall('Campaign.Delete', $parameters);
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

		// check if record exists
		if(!$this->existsClient($clientId)) return false;

		// set parameters
		$parameters['ClientID'] = (string) $clientId;

		// make the call
		return (bool) $this->doCall('Client.Delete', $parameters);
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
		if(!preg_match('/\[.*\]/', $name)) $name = '[' . $name . ']';

		// set parameters
		$parameters['ListID'] = (string) $listId;
		$parameters['Key'] = (string) $name;

		// delete the record
		return (bool) $this->doCall('List.DeleteCustomField', $parameters);
	}


	/**
	 * Deletes a list
	 *
	 * @return	bool
	 * @param	string[optional] $listId	The ID of the list.
	 */
	public function deleteList($listId = null)
	{
		// set ID
		$listId = empty($listId) ? $this->getListId() : $listId;

		// check if record exists
		if(!$this->existsList($listId)) return false;

		// set parameters
		$parameters['ListID'] = (string) $listId;

		// make the call
		return (bool) $this->doCall('List.Delete', $parameters);
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

		// set parameters
		$parameters['TemplateID'] = (string) $templateId;

		// make the call
		return $this->doCall('Template.Delete', $parameters);
	}


	/**
	 * Make the call
	 *
	 * @return	string
	 * @param	string $method					The method to execute.
	 * @param	array[optional] $parameters		The parameters to pass.
	 * @param	bool[optional] $authenticate	Should we authenticate?
	 * @param	bool[optional] $usePost			Should we use post?
	 */
	private function doCall($method, array $parameters = array(), $authenticate = false, $usePost = true)
	{
		// redefine
		$method = (string) $method;
		$parameters = (array) $parameters;
		$authenticate = (bool) $authenticate;
		$usePost = (bool) $usePost;

		// set the API key if the url is not the method to get the API key
		if($method != 'User.GetApiKey') $parameters['ApiKey'] = $this->apiKey;

		// validate needed authentication
		if($authenticate && ($this->getUsername() == '' || $this->getPassword() == '')) throw new CampaignMonitorException('No username or password was set.');

		// explode the called method
		$method = explode('.', $method);
		$callMethod = isset($method[1]) ? $method[1] : $method[0];
		$responseKey = $method[0] . '.' . $method[1] . 'Result';

		// check for getDetail
		switch($method[1])
		{
			case 'Add':
				$callMethod = 'Add' . $method[0];
			break;

			case 'Create':
				$callMethod = 'Create' . $method[0];
			break;

			case 'CreateCustomField':
				$callMethod = 'Create' . $method[0] . 'CustomField';
			break;

			case 'Delete':
				$callMethod = 'Delete' . $method[0];
			break;

			case 'DeleteCustomField':
				$callMethod = 'Delete' . $method[0] . 'CustomField';
			break;

			case 'GetBounces':
				$callMethod = 'Get' . $method[0] . 'Bounces';
			break;

			case 'GetCampaigns':
				$callMethod = 'Get' . $method[0] . 'Campaigns';
			break;

			case 'GetCustomFields':
				$callMethod = 'Get' . $method[0] . 'CustomFields';
			break;

			case 'GetDetail':
				$callMethod = 'Get' . $method[0] . 'Detail';
			break;

			case 'GetLists':
				$callMethod = 'Get' . $method[0] . 'Lists';
			break;

			case 'GetOpens':
				$callMethod = 'Get' . $method[0] . 'Opens';
			break;

			case 'GetSubscribers':
				$responseKey = 'Subscribers.GetActiveResult';
			break;

			case 'GetSegments':
				$callMethod = 'Get' . $method[0] . 'Segments';
			break;

			case 'GetStats':
				$callMethod = 'Get' . $method[0] . 'Stats';
			break;

			case 'GetSummary':
				$callMethod = 'Get' . $method[0] . 'Summary';
			break;

			case 'GetSuppressionList':
				$callMethod = 'Get' . $method[0] . 'SuppressionList';
			break;

			case 'GetTemplates':
				$callMethod = 'Get' . $method[0] . 'Templates';
			break;

			case 'GetUnsubscribes':
				$callMethod = 'Get' . $method[0] . 'Unsubscribes';
			break;

			case 'Send':
				$callMethod = 'Send' . $method[0];
			break;

			case 'Update':
				$callMethod = 'Update' . $method[0];
			break;

			case 'UpdateBasics':
				$callMethod = 'Update' . $method[0] . 'Basics';
			break;

			case 'UpdateAccessAndBilling':
				$callMethod = 'Update' . $method[0] . 'AccessAndBilling';
			break;
		}

		// catch any timeouts that may occur
		try
		{
			// execute the soap call and fetch the response
			$response = $this->XMLObjectsToArray($this->soap->{$callMethod}($parameters));
		}
		catch(CampaignMonitorException $e)
		{
			// check what message we got
			switch(strtolower($e->getMessage()))
			{
				case 'error fetching http headers':
					throw new CampaignMonitorException('The request to ' . API_URL . ' timed out.');
				break;
			}
		}

		// fetch response message and code
		$responseMessage = (string) (isset($response[$responseKey]['enc_value']['Message']) ? $response[$responseKey]['enc_value']['Message'] : null);
		$responseCode = (int) (isset($response[$responseKey]['enc_value']['Code']) ? $response[$responseKey]['enc_value']['Code'] : 200);

		if(is_array($response[$responseKey]) && key_exists('Message', $response[$responseKey])) $responseMessage = (string) $response[$responseKey]['Message'];
		if(is_array($response[$responseKey]) && key_exists('Code', $response[$responseKey])) $responseCode = (int) $response[$responseKey]['Code'];

		// invalid headers
		if(!in_array($responseCode, array(0, 200)))
		{
			// should we provide debug information
			if(self::DEBUG)
			{
				// make it output proper
				echo '<pre>';

				// dump the raw response
				var_dump($response);

				// end proper format
				echo '</pre>';

				// stop the script
				exit;
			}

			// throw error
			throw new CampaignMonitorException($responseMessage, $responseCode);
		}

		// return the response if the type is string
		if(is_string($response[$responseKey])) return $response[$responseKey];

		// filter out what we need if the response type is array
		if(is_array($response[$responseKey]) && isset($response[$responseKey]['enc_value'])) return (array) $response[$responseKey]['enc_value'];

		// if we made it here, we return our result
		return (bool) (is_array($response[$responseKey]) && in_array($responseCode, array(0, 200))) ? true : false;
	}


	/**
	 * Same as doCall, but it performs it in a try/catch wrapper and returns false upon an exception/true upon success
	 *
	 * @return	bool
	 * @param	string $method		The method to execute.
	 * @param	array $parameters	The parameters to pass.
	 */
	private function doSilentCall($method, $parameters)
	{
		// try and update the template record
		try
		{
			$this->doCall($method, $parameters);
		}

		// stop here if an exception is found
		catch(Exception $e)
		{
			return false;
		}

		// if we made it here, the template was updated
		return true;
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
	 */
	public function getAPIKey()
	{
		return (string) $this->apiKey;
	}


	/**
	 * Returns a list of all subscribers for a list that have hard bounced since the specified date.
	 *
	 * @return	array
	 * @param	string[optional] $listId	The list ID to fetch the bounced subscribers from.
	 * @param	int[optional] $timestamp	The date to check from.
	 */
	public function getBouncedSubscribers($listId = null, $timestamp = null)
	{
		// set ID
		$listId = empty($listId) ? $this->getListId() : $listId;

		// check timestamp
		if(empty($timestamp)) $timestamp = strtotime('last week');

		// set parameters
		$parameters['ListID'] = (string) $listId;
		$parameters['Date'] = (string) date('Y-m-d H:i:s', $timestamp);

		// make the call
		$records = (array) $this->doCall('Subscribers.GetBounced', $parameters);

		// stop here if no records were set
		if(empty($records)) return array();

		// reserve variables
		$results = array();
		$i = 0;

		// if EmailAddress is set in the level below Subscriber, we have 1 result, otherwise we have an array of results
		$records = (isset($records['Subscriber']['EmailAddress'])) ? $records : $records['Subscriber'];

		// loop the records
		foreach($records as $record)
		{
			// set values
			$results[$i]['email'] = $record['EmailAddress'];
			$results[$i]['name'] = $record['Name'];
			$results[$i]['date'] = $record['Date'];
			$results[$i]['status'] = $record['State'];

			// check if there are custom fields present
			if(empty($record['CustomFields']['SubscriberCustomField'])) continue;

			// loop records
			foreach($record['CustomFields']['SubscriberCustomField'] as $field)
			{
				// set values
				$results[$i]['custom_fields'][$field['Key']] = $field['Value'];
			}
		}

		// return the records
		return $results;
	}


	/**
	 * Returns a list of all subscribers who bounced for a given campaign, and the type of bounce ("Hard"=Hard Bounce, "Soft"=Soft Bounce).
	 *
	 * @return	array
	 * @param	string[optional] $campaignId	The ID of the campaign you want data for.
	 */
	public function getCampaignBounces($campaignId = null)
	{
		// set ID
		$campaignId = empty($campaignId) ? $this->getCampaignId() : $campaignId;

		// set parameters
		$parameters['CampaignID'] = (string) $campaignId;

		// make the call
		$records = (array) $this->doCall('Campaign.GetBounces', $parameters);

		// stop here if no records were set
		if(empty($records)) return array();

		// if SubscriberClick is set in the first level, we have multiple results
		$records = (isset($records['SubscriberBounce']['EmailAddress'])) ? $records : $records['SubscriberBounce'];

		// reserve variables
		$results = array();
		$i = 0;

		// loop the records
		foreach($records as $record)
		{
			$results[$i]['email'] = $record['EmailAddress'];
			$results[$i]['list_id'] = $record['ListID'];
			$results[$i]['bounce_type'] = $record['BounceType'];
			$i++;
		}

		// return the records
		return $results;
	}


	/**
	 * Returns the default set campaign ID
	 *
	 * @return	string
	 */
	public function getCampaignId()
	{
		return (string) $this->campaignId;
	}


	/**
	 * Returns a list of all subscribers who opened a given campaign, and the number of times they opened the campaign
	 *
	 * @return	array
	 * @param	string[optional] $campaignId	The ID of the campaign you want data for.
	 */
	public function getCampaignOpens($campaignId = null)
	{
		// set ID
		$campaignId = empty($campaignId) ? $this->getCampaignId() : $campaignId;

		// set parameters
		$parameters['CampaignID'] = (string) $campaignId;

		// make the call
		$records = (array) $this->doCall('Campaign.GetOpens', $parameters);

		// stop here if no records were set
		if(empty($records)) return array();

		// if SubscriberClick is set in the first level, we have multiple results
		$records = (isset($records['SubscriberOpen']['EmailAddress'])) ? $records : $records['SubscriberOpen'];

		// reserve variables
		$results = array();
		$i = 0;

		// loop the records
		foreach($records as $record)
		{
			$results[$i]['email'] = $record['EmailAddress'];
			$results[$i]['list_id'] = $record['ListID'];
			$results[$i]['number_of_opens'] = $record['NumberOfOpens'];
			$i++;
		}

		// return the records
		return $results;
	}


	/**
	 * Returns a list of all subscribers who clicked a link for a given campaign, The ID of the list they belong to, the links they clicked, and the number of times they clicked the link.
	 *
	 * @return	array
	 * @param	string[optional] $campaignId	The ID of the campaign you want data for.
	 */
	public function getCampaignSubscriberClicks($campaignId = null)
	{
		// set ID
		$campaignId = empty($campaignId) ? $this->getCampaignId() : $campaignId;

		// set parameters
		$parameters['CampaignID'] = (string) $campaignId;

		// make the call
		$records = (array) $this->doCall('Campaign.GetSubscriberClicks', $parameters);

		// stop here if no records were set
		if(empty($records)) return array();

		// if SubscriberClick is set in the first level, we have multiple results
		$records = (isset($records['SubscriberClick']['EmailAddress'])) ? $records : $records['SubscriberClick'];

		// reserve variables
		$results = array();
		$i = 0;

		// loop the records
		foreach($records as $record)
		{
			// shorten the email var for re-use ease
			$email = $record['EmailAddress'];

			// set result values
			$results[$email]['list_id'] = $record['ListID'];
			$results[$email]['clicked_links'] = array();

			// check if there are clickedlinks present
			if(empty($record['ClickedLinks'])) continue;

			// if Link is set in the level below SubscriberClickedLink, we have 1 result. Otherwise, we have multiple
			$links = (isset($record['ClickedLinks']['SubscriberClickedLink']['Link'])) ? $record['ClickedLinks'] : $record['ClickedLinks']['SubscriberClickedLink'];

			// loop the clicked links
			foreach($links as $link)
			{
				// set clicked link values
				$results[$email]['clicked_links'][$i]['link'] = $link['Link'];
				$results[$email]['clicked_links'][$i]['clicks'] = $link['Clicks'];
				$i++;
			}

			// reset the link counter
			$i = 0;
		}

		// return the records
		return $results;
	}


	/**
	 * Returns a statistical summary, including number of recipients and open count, for a given campaign.
	 *
	 * @return	array
	 * @param	string[optional] $campaignId	The ID of the campaign you want data for.
	 */
	public function getCampaignSummary($campaignId = null)
	{
		// set ID
		$campaignId = empty($campaignId) ? $this->getCampaignId() : $campaignId;

		// set parameters
		$parameters['CampaignID'] = (string) $campaignId;

		// make the call
		$record = (array) $this->doCall('Campaign.GetSummary', $parameters);

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

		// return the record
		return $result;
	}


	/**
	 * Returns a list of all subscribers who unsubscribed for a given campaign.
	 *
	 * @return	array
	 * @param	string[optional] $campaignId	The ID of the campaign you want data for.
	 */
	public function getCampaignUnsubscribers($campaignId = null)
	{
		// set ID
		$campaignId = empty($campaignId) ? $this->getCampaignId() : $campaignId;

		// set parameters
		$parameters['CampaignID'] = (string) $campaignId;

		// make the call
		$records = (array) $this->doCall('Campaign.GetUnsubscribes', $parameters);

		// stop here if no records were set
		if(empty($records)) return array();

		// reserve variables
		$results = array();
		$i = 0;

		// if EmailAddress is set in the level below SubscriberUnsubscribe, we have 1 result, otherwise we have an array of results
		$records = (isset($records['SubscriberUnsubscribe']['EmailAddress'])) ? $records : $records['SubscriberUnsubscribe'];

		// loop the records
		foreach($records as $record)
		{
			$results[$i]['email'] = $record['EmailAddress'];
			$results[$i]['list_id'] = $record['ListID'];
			$i++;
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

		// set parameters
		$parameters['ClientID'] = (string) $clientId;

		// make the call
		$record = (array) $this->doCall('Client.GetDetail', $parameters);

		// stop here if no record was set
		if(empty($record)) return array();

		// reserve variable
		$result = array();
		$details = $record['BasicDetails'];
		$billing = $record['AccessAndBilling'];

		// basic details
		$result['client_id'] = $details['ClientID'];
		$result['company'] = $details['CompanyName'];
		$result['contact_name'] = $details['ContactName'];
		$result['email'] = $details['EmailAddress'];
		$result['country'] = $details['Country'];
		$result['timezone'] = $details['Timezone'];

		// access and billing
		$result['username'] = empty($billing['Username']) ? null : $billing['Username'];
		$result['password'] = (empty($billing['Password']) || $billing['Password'] == 'Deprecated') ? null : $billing['Password'];
		$result['access_level'] = empty($billing['AccessLevel']) ? null : $billing['AccessLevel'];

		// set value of billing type
		switch($billing['BillingType'])
		{
			case 'UserPaysOnClientsBehalf':
				$result['billing_type'] = 'user';
			break;

			case 'ClientPaysAtStandardRate':
				$result['billing_type'] = 'client_standard';
			break;

			case 'ClientPaysWithMarkup':
				$result['billing_type'] = 'client_markup';
			break;

			default:
				$result['billing_type'] = null;
			break;
		}

		// depending on the billing type, parse these vars
		if($result['billing_type'] != 'user')
		{
			// set billing type vars
			$result['currency'] = empty($billing['Currency']) ? null : $billing['Currency'];
			$result['delivery_fee'] = empty($billing['DeliveryFee']) ? null : $billing['DeliveryFee'];
			$result['cost_per_recipient'] = empty($billing['CostPerRecipient']) ? null : $billing['CostPerRecipient'];
			$result['design_and_spam_test_fee'] = empty($billing['DesignAndSpamTestFee']) ? null : $billing['DesignAndSpamTestFee'];
		}

		// return the record
		return $result;
	}


	/**
	 * Returns a list of all campaigns that have been sent for a client
	 *
	 * @return	array
	 * @param	string[optional] $clientId	The ID of the client.
	 */
	public function getClientCampaigns($clientId = null)
	{
		// set ID
		$clientId = empty($clientId) ? $this->getClientId() : $clientId;

		// set parameters
		$parameters['ClientID'] = (string) $clientId;

		// make the call
		$records = (array) $this->doCall('Client.GetCampaigns', $parameters);

		// stop here if no record was set
		if(empty($records)) return array();

		// reserve variable
		$results = array();
		$i = 0;

		// if CampaignID is set in the level below Campaign, we have 1 result, otherwise we have an array of results
		$records = (isset($records['Campaign']['CampaignID'])) ? $records : $records['Campaign'];

		// loop the records
		foreach($records as $record)
		{
			// set result values
			$results[$i]['campaign_id'] = $record['CampaignID'];
			$results[$i]['subject'] = $record['Subject'];
			$results[$i]['name'] = $record['Name'];
			$results[$i]['date_sent'] = $record['SentDate'];
			$results[$i]['total_recipients'] = $record['TotalRecipients'];

			// increment the counter
			$i++;
		}

		// return the results
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
	 * Returns all clients for the logged-in user.
	 *
	 * @return	array
	 */
	public function getClients()
	{
		// make the call
		$records = (array) $this->doCall('User.GetClients');

		// stop here if no records were set
		if(empty($records)) return array();

		// reserve variable
		$results = array();
		$i = 0;

		// if CampaignID is set in the level below Campaign, we have 1 result, otherwise we have an array of results
		$records = (isset($records['Client']['ClientID'])) ? $records : $records['Client'];

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
	 * Returns all countries for the logged-in user.
	 *
	 * @return	array
	 */
	public function getCountries()
	{
		// make the call
		$records = (array) $this->doCall('User.GetCountries');

		// stop here if no records were set
		if(empty($records['string'])) return array();

		// return the records
		return $records['string'];
	}


	/**
	 * Returns all the custom fields available for a list.
	 *
	 * @return	array
	 * @param	string[optional] $listId	The list ID to fetch the custom fields from.
	 */
	public function getCustomFields($listId = null)
	{
		// set ID
		$listId = empty($listId) ? $this->getListId() : $listId;

		// set parameters
		$parameters['ListID'] = (string) $listId;

		// make the call
		$records = (array) $this->doCall('List.GetCustomFields', $parameters);

		// stop here if no records were set
		if(empty($records)) return array();

		// reserve variable
		$results = array();
		$i = 0;

		// if CampaignID is set in the level below Campaign, we have 1 result, otherwise we have an array of results
		$records = (isset($records['ListCustomField']['FieldName'])) ? $records : $records['ListCustomField'];

		// loop the records
		foreach($records as $record)
		{
			$results[$i]['name'] = $record['FieldName'];
			$results[$i]['type'] = $record['DataType'];

			// field options found
			if(!empty($record['FieldOptions']['string']))
			{
				// loop the options
				foreach($record['FieldOptions']['string'] as $option) $results[$i]['options'][] = $option;
			}

			// increment counter
			$i++;
		}

		// return the records
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

		// set parameters
		$parameters['ListID'] = (string) $listId;

		// make the call
		$record = (array) $this->doCall('List.GetDetail', $parameters);

		// stop here if no record was set
		if(empty($record)) return array();

		// reserve variable
		$result = array();

		// basic details
		$result['id'] = $record['ListID'];
		$result['title'] = $record['Title'];
		$result['confirm_optin'] = (bool) $record['ConfirmOptIn'];
		$result['unsubscribe_url'] = empty($record['UnsubscribePage']) ? $this->siteURL . '/t/GenericUnsubscribe' : $record['UnsubscribePage'];
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
	 * Returns a list of all subscriber lists for a campaign.
	 *
	 * @return	array
	 * @param	string[optional] $campaignId	The ID of the campaign.
	 */
	public function getListsByCampaignId($campaignId = null)
	{
		// set ID
		$campaignId = empty($campaignId) ? $this->getCampaignId() : $campaignId;

		// set parameters
		$parameters['CampaignID'] = (string) $campaignId;

		// make the call
		$records = (array) $this->doCall('Campaign.GetLists', $parameters);

		// stop here if no records were set
		if(empty($records)) return array();

		// reserve variable
		$results = array();
		$i = 0;

		// loop the records
		foreach($records as $record)
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

		// set parameters
		$parameters['ClientID'] = (string) $clientId;

		// make the call
		$records = (array) $this->doCall('Client.GetLists', $parameters);

		// stop here if no records were set
		if(empty($records)) return array();

		// reserve variables
		$results = array();
		$i = 0;

		// loop the records
		foreach($records['List'] as $record)
		{
			$results[$i]['id'] = $record['ListID'];
			$results[$i]['name'] = $record['Name'];
			$i++;
		}

		// return the records
		return $results;
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

		// set parameters
		$parameters['ListID'] = (string) $listId;

		// make the call
		$record = (array) $this->doCall('List.GetStats', $parameters);

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
	 * Get the password
	 *
	 * @return	string
	 */
	private function getPassword()
	{
		return (string) $this->password;
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
	 * @param	string[optional] $listId	The list ID to fetch the subscriber from.
	 */
	public function getSubscriber($email, $listId = null)
	{
		// stop here if no email was set
		if(!$email) throw new CampaignMonitorException('No e-mail given.');

		// set ID
		$listId = empty($listId) ? $this->getListId() : $listId;

		// set parameters
		$parameters['ListID'] = (string) $listId;
		$parameters['EmailAddress'] = (string) $email;

		// make the call
		$record = (array) $this->doCall('Subscribers.GetSingleSubscriber', $parameters);

		// stop here if no record was set
		if(empty($record)) return array();

		// set values
		$results['email'] = $record['EmailAddress'];
		$results['name'] = $record['Name'];
		$results['date'] = $record['Date'];
		$results['status'] = $record['State'];

		// check if there are clickedlinks present
		if(empty($record['CustomFields']['SubscriberCustomField'])) continue;

		// loop records
		foreach($record['CustomFields']['SubscriberCustomField'] as $field)
		{
			// set values
			$results['custom_fields'][$field['Key']] = $field['Value'];
		}

		// return the records
		return $results;
	}


	/**
	 * Returns a list of all active subscribers for a list that have been added since the specified date.
	 * In the documentation this function is called GetActive, yet the soap methods will tell you it requires GetSubscribers
	 *
	 * @return	array
	 * @param	string[optional] $listId	The ID of the list.
	 * @param	int[optional] $timestamp	The list ID to fetch the subscribers from.
	 */
	public function getSubscribers($listId = null, $timestamp = null)
	{
		// set ID
		$listId = empty($listId) ? $this->getListId() : $listId;

		// check timestamp
		if(empty($timestamp)) $timestamp = strtotime('last week');

		// set parameters
		$parameters['ListID'] = (string) $listId;
		$parameters['Date'] = (string) date('Y-m-d H:i:s', $timestamp);

		// make the call
		$records = (array) $this->doCall('Subscribers.GetSubscribers', $parameters);

		// stop here if no records were set
		if(empty($records)) return array();

		// reserve variables
		$results = array();
		$i = 0;

		// loop the records
		foreach($records as $record)
		{
			// set values
			$results[$i]['email'] = $record['EmailAddress'];
			$results[$i]['name'] = $record['Name'];
			$results[$i]['date'] = $record['Date'];
			$results[$i]['status'] = $record['State'];

			// check if there are custom fields present
			if(empty($record['CustomFields']['SubscriberCustomField'])) continue;

			// loop records
			foreach($record['CustomFields']['SubscriberCustomField'] as $field)
			{
				// set values
				$results[$i]['custom_fields'][$field['Key']] = $field['Value'];
			}
		}

		// return the records
		return $results;
	}


	/**
	 * Returns a list of all subscriber segments for a client.
	 *
	 * @return	array
	 * @param	string[optional] $clientId	The ID of the client.
	 */
	public function getSubscriberSegments($clientId = null)
	{
		// set ID
		$clientId = empty($clientId) ? $this->getClientId() : $clientId;

		// set parameters
		$parameters['ClientID'] = (string) $clientId;

		// make the call
		$records = (array) $this->doCall('Client.GetSegments', $parameters);

		// stop here if no records were set
		if(empty($records)) return array();

		// reserve variables
		$results = array();
		$i = 0;

		// loop the records
		foreach($records as $record)
		{
			$results[$i]['name'] = $record['Name'];
			$results[$i]['list_id'] = $record['ListID'];
			$i++;
		}

		// return the records
		return $results;
	}


	/**
	 * Returns all subscribers in the client-wide suppression list.
	 *
	 * @return	array
	 * @param	string[optional] $clientId	The client ID to fetch the suppression list from.
	 */
	public function getSuppressionListByClientId($clientId = null)
	{
		// set ID
		$clientId = empty($clientId) ? $this->getClientId() : $clientId;

		// set parameters
		$parameters['ClientID'] = (string) $clientId;

		// make the call
		$records = (array) $this->doCall('Client.GetSuppressionList', $parameters);

		// stop here if no records were set
		if(empty($records)) return array();

		// reserve variables
		$results = array();
		$i = 0;

		// if EmailAddress is set in the level below Subscriber, we have 1 result, otherwise we have an array of results
		$records = (isset($records['Subscriber']['EmailAddress'])) ? $records : $records['EmailAddress'];

		// loop the records
		foreach($records as $record)
		{
			$results[$i]['email'] = $record['EmailAddress'];
			$results[$i]['name'] = $record['Name'];
			$results[$i]['state'] = $record['State'];
			$i++;
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
		return $this->doCall('User.GetSystemDate');
	}


	/**
	 * Returns a template's configuration detail
	 *
	 * @return	array
	 * @param	string $templateId	The ID of the template.
	 */
	public function getTemplate($templateId)
	{
		// set parameters
		$parameters['TemplateID'] = (string) $templateId;

		// make the call
		$record = (array) $this->doCall('Template.GetDetail', $parameters);

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
	 *
	 * @return	array
	 * @param	string[optional] $clientId	The ID of the client.
	 */
	public function getTemplatesByClientId($clientId = null)
	{
		// set ID
		$clientId = empty($clientId) ? $this->getClientId() : $clientId;

		// set parameters
		$parameters['ClientID'] = (string) $clientId;

		// make the call
		$records = (array) $this->doCall('Client.GetTemplates', $parameters);

		// stop here if no records were set
		if(empty($records)) return array();

		// reserve variables
		$results = array();
		$i = 0;

		// if ListID is set in the level below List, we have 1 result, otherwise we have an array of results
		$records = (isset($records['Template']['TemplateID'])) ? $records : $records['Template'];

		// loop the records
		foreach($records as $record)
		{
			$results[$i]['id'] = $record['TemplateID'];
			$results[$i]['name'] = $record['Name'];
			$results[$i]['preview_url'] = $record['PreviewURL'];
			$results[$i]['screenshot_url'] = $record['ScreenshotURL'];
			$i++;
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
		// make the call
		$records = (array) $this->doCall('User.GetTimezones');

		// stop here if no records were set
		if(empty($records['string'])) return array();

		// return the records
		return $records['string'];
	}


	/**
	 * Returns a list of all subscribers for a list that have unsubscribed since the specified date. Said date defaults to last week.
	 * Results only contain custom fields if they have values OR if they have had a value once before. CM has some funny quirks like that.
	 *
	 * @return	array
	 * @param	string[optional] $listId	The list ID to fetch the unsubscribers from.
	 * @param	int[optional] $timestamp	If this is filled this method will only return unsubscribes that occured since this timestamp.
	 */
	public function getUnsubscribers($listId = null, $timestamp = null)
	{
		// set ID
		$listId = empty($listId) ? $this->getListId() : $listId;

		// check timestamp
		if(empty($timestamp)) $timestamp = strtotime('last week');

		// set parameters
		$parameters['ListID'] = (string) $listId;
		$parameters['Date'] = (string) date('Y-m-d H:i:s', $timestamp);

		// make the call
		$records = (array) $this->doCall('Subscribers.GetUnsubscribed', $parameters);

		// stop here if no records were set
		if(empty($records)) return array();

		// reserve variables
		$results = array();
		$i = 0;

		// loop the records
		foreach($records as $record)
		{
			// set values
			$results[$i]['email'] = $record['EmailAddress'];
			$results[$i]['name'] = $record['Name'];
			$results[$i]['date'] = $record['Date'];
			$results[$i]['status'] = $record['State'];

			// check if there are clickedlinks present
			if(empty($record['CustomFields']['SubscriberCustomField'])) continue;

			// loop records
			foreach($record['CustomFields']['SubscriberCustomField'] as $field)
			{
				// set values
				$results[$i]['custom_fields'][$field['Key']] = $field['Value'];
			}
		}

		// return the records
		return $results;
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
	 * Returns true or false if a given e-mail address is subscribed in the given list.
	 *
	 * @return	bool
	 * @param	string $email				The emailaddress.
	 * @param	string[optional] $listId	The list ID to look in.
	 */
	public function isSubscribed($email, $listId = null)
	{
		// stop here if no email was set
		if(!$email) throw new CampaignMonitorException('No e-mail given.');

		// set ID
		$listId = empty($listId) ? $this->getListId() : $listId;

		// set parameters
		$parameters['ListID'] = (string) $listId;
		$parameters['Email'] = (string) $email;

		// make the call
		$result = $this->doCall('Subscribers.GetIsSubscribed', $parameters);

		// check for True/False
		switch($result)
		{
			case 'False':
				return false;

			case 'True':
				return true;

			default:
				return false;
		}
	}


	/**
	 * Schedules an existing campaign for sending. The campaign must be imported with defined recipients. For campaigns with more than 5 recipients the user must have sufficient credits or their credit card details saved within the application for the campaign to be sent via the API. For campaigns with 5 recipients or less the user must have enough test campaigns remaining in their API account.
	 *
	 * @return	bool
	 * @param	string $confirmationEmail		The email address that the confirmation email that the campaign has been sent will go to.
	 * @param	string[optional] $deliveryDate	The date the campaign should be scheduled to be sent (YYYY-MM-DD HH:MM:SS).
	 * @param	string[optional] $campaignId	The ID of the campaign to send.
	 */
	public function sendCampaign($confirmationEmail, $deliveryDate = null, $campaignId = null)
	{
		// set ID
		$campaignId = empty($campaignId) ? $this->getCampaignId() : $campaignId;

		// check timestamp input
		$timestamp = (empty($timestamp)) ? time() : (int) $timestamp;

		// set parameters
		$parameters['CampaignID'] = (string) $campaignId;
		$parameters['ConfirmationEmail'] = (string) $confirmationEmail;
		$parameters['SendDate'] = $deliveryDate;

		// make the call
		return $this->doCall('Campaign.Send', $parameters);
	}


	/**
	 * Set the default campaign ID to use
	 *
	 * @return	void
	 * @param	string $id	The id of the campaign.
	 */
	public function setCampaignId($id)
	{
		$this->campaignId = (string) $id;
	}


	/**
	 * Set the default client ID to use
	 *
	 * @return	void
	 * @param	string $id	The id of the client.
	 */
	public function setClientId($id)
	{
		$this->clientId = (string) $id;
	}


	/**
	 * Set the default list ID to use
	 *
	 * @return	void
	 * @param	string $id	The id of the list.
	 */
	public function setListId($id)
	{
		$this->listId = (string) $id;
	}


	/**
	 * Set password
	 *
	 * @return	void
	 * @param	string $password	The password to use.
	 */
	private function setPassword($password)
	{
		$this->password = (string) $password;
	}


	/**
	 * Set the site URL
	 *
	 * @return	void
	 * @param	string $siteURL		The base URL of the site you use to login to Campaign Monitor. e.g. http://example.createsend.com/.
	 */
	private function setSiteURL($siteURL)
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
	 * @param	string $username	The username to use.
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
	 * @param	string $name					The name of the new subscriber. If the name is unknown, an empty string can be passed in.
	 * @param	array[optional] $customFields	The custom fields for this subscriber in key/value pairs.
	 * @param	bool[optional] $resubscribe		Subscribes an unsubscribed email address back to the list if this is true.
	 * @param	string[optional] $listId		The list you want to add the subscriber to.
	 */
	public function subscribe($email, $name, $customFields = array(), $resubscribe = true, $listId = null)
	{
		return $this->addSubscriber($email, $name, $customFields, $resubscribe, $listId);
	}


	/**
	 * Changes the status of an Active Subscriber to an Unsubscribed Subscriber who will no longer receive campaigns sent to that Subscriber List.
	 *
	 * @return	bool
	 * @param	string $email				The emailaddress.
	 * @param	string[optional] $listId	The list ID to unsubscribe from.
	 */
	public function unsubscribe($email, $listId = null)
	{
		// stop here if no email was set
		if(!$email) throw new CampaignMonitorException('No e-mail given.');

		// set ID
		$listId = empty($listId) ? $this->getListId() : $listId;

		// set parameters
		$parameters['ListID'] = (string) $listId;
		$parameters['Email'] = (string) $email;

		// make the call
		return $this->doCall('Subscriber.Unsubscribe', $parameters);
	}


	/**
	 * Updates a client's access and billing settings.
	 *
	 * @return	bool
	 * @param	int $accessLevel						An integer describing the client’s ability to access different areas of the application. Influences the significance and requirements of the following parameters.
	 * @param	string[optional] $username				Client login username. Not required and ignored if AccessLevel is set to 0.
	 * @param	string[optional] $password				Client login password. Not required and ignored if AccessLevel is set to 0.
	 * @param	string[optional] $billingType			Client billing type, only required if AccessLevel is set to allow the client to create and send campaigns.
	 * @param	string[optional] $currency				Billing currency for this client, only required if BillingType is set to either ClientPaysAtStandardRate or ClientPaysWithMarkup.
	 * @param	string[optional] $deliveryFee			Flat rate delivery fee to be charged to the client for each campaign sent, expressed in the chosen currency’s major unit, but without the currency symbol. Only required if BillingType is set to ClientPaysWithMarkup.
	 * @param	string[optional] $costPerRecipient		Additional cost added to the campaign for each email address the campaign is sent to, expressed in the chosen currency’s minor unit. Only required if BillingType is set to ClientPaysWithMarkup.
	 * @param	string[optional] $designAndSpamTestFee	Expressed in the chosen currency’s major unit. Only required if BillingType is set to ClientPaysWithMarkup and client has access to design and spam tests.
	 * @param	string[optional] $clientId				The client ID to update.
	 */
	public function updateClientAccessAndBilling($accessLevel, $username = null, $password = null, $billingType = null, $currency = null, $deliveryFee = null, $costPerRecipient = null, $designAndSpamTestFee = null, $clientId = null)
	{
		// set ID
		$clientId = empty($clientId) ? $this->getClientId() : $clientId;

		// set parameters
		$parameters['AccessLevel'] = (int) $accessLevel;
		$parameters['Username'] = (string) $username;
		$parameters['Password'] = (string) $password;
		$parameters['BillingType'] = (string) $billingType;
		$parameters['Currency'] = (string) $currency;
		$parameters['DeliveryFee'] = (string) $deliveryFee;
		$parameters['CostPerRecipient'] = (string) $costPerRecipient;
		$parameters['DesignAndSpamTestFee'] = (string) $designAndSpamTestFee;
		$parameters['ClientID'] = (string) $clientId;

		// try and update the record
		return $this->doCall('Client.UpdateAccessAndBilling', $parameters);
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

		// set parameters
		$parameters['CompanyName'] = (string) $companyName;
		$parameters['ContactName'] = (string) $contactName;
		$parameters['EmailAddress'] = (string) $email;
		$parameters['Country'] = (string) $country;
		$parameters['Timezone'] = (string) $timezone;
		$parameters['ClientID'] = (string) $clientId;

		// try and update the record
		return (bool) $this->doSilentCall('Client.UpdateBasics', $parameters);
	}


	/**
	 * Updates a subscriber list's details.
	 *
	 * @return	bool
	 * @param	string $title								The title of the list.
	 * @param	string[optional] $unsubscribePage			The URL to which subscribers will be directed when unsubscribing from the list. If left blank or omitted a generic unsubscribe page is used.
	 * @param	bool[optional] $confirmOptIn				Either true or false depending on whether the list requires email confirmation or not. Please see the help documentation for more details of what this means.
	 * @param	string[optional] $confirmationSuccessPage	Successful email confirmations will be redirected to this URL. Ignored if ConfirmOptIn is false. If left blank or omitted a generic confirmation page is used.
	 * @param	string[optional] $listId					The list ID to update.
	 */
	public function updateList($title, $unsubscribePage = null, $confirmOptIn = false, $confirmationSuccessPage = null, $listId = null)
	{
		// set list ID
		$listId = empty($listId) ? $this->getListId() : $listId;

		// set parameters
		$parameters['ListID'] = (string) $listId;
		$parameters['Title'] = (string) $title;
		$parameters['UnsubscribePage'] = (string) $unsubscribePage;
		$parameters['ConfirmOptIn'] = $confirmOptIn ? true : false;
		$parameters['ConfirmationSuccessPage'] = (string) $confirmationSuccessPage;

		// try and update the record
		return $this->doCall('List.Update', $parameters);
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
		$parameters['TemplateID'] = (string) $templateId;
		$parameters['TemplateName'] = (string) $name;
		$parameters['HTMLPageURL'] = (string) $HTMLPageURL;
		$parameters['ZipFileURL'] = (string) $zipFileURL;
		$parameters['ScreenshotURL'] = (string) $screenshotURL;

		// try and update the record
		return (bool) $this->doSilentCall('Template.Update', $parameters);
	}


	/**
	 * XML objects to array conversion
	 *
	 * @return	mixed
	 * @param	mixed $xml	The XML to process.
	 */
	private static function XMLObjectsToArray($xml)
	{
		// xml is an object
		if(is_object($xml)) $xml = get_object_vars($xml);

		// if $xml is an array, map the active function
		return (is_array($xml)) ? array_map(array(__CLASS__, __FUNCTION__), $xml) : $xml;
	}
}


/**
 * CampaignMonitor Exception class
 *
 * @author	Dave Lens <dave@netlash.com>
 */
class CampaignMonitorException extends Exception
{
	/**
	 * Http header-codes
	 *
	 * @var	array
	 */
	private $aStatusCodes = array(1 => 'Invalid Email',
									100 => 'Invalid API Key',
									101 => 'Switching Protocols',
									102 => 'Invalid ClientID',
									105 => 'Invalid Login',
									150 => 'Email Taken',
									152 => 'Invalid Timezone',
									154 => 'Empty Company Name',
									155 => 'Empty Contact Name',
									157 => 'Invalid Country',
									172 => 'Client Creaton Limit',
									200 => 'OK',
									201 => 'Created',
									202 => 'Accepted',
									203 => 'Non-Authoritative Information',
									204 => 'No Content',
									205 => 'Reset Content',
									206 => 'Partial Content',
									250 => 'Duplicate List Title',
									251 => 'List Title Empty',
									300 => 'Multiple Choices',
									301 => 'Moved Permanently',
									302 => 'Found',
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
	 * Class constructor
	 *
	 * @return	void
	 * @param	string[optional] $message	The message.
	 * @param	int[optional] $code			The numeric code.
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