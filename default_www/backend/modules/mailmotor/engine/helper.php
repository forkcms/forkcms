<?php

/**
 * In this file we store all generic functions that we will be using to communicate with CampaignMonitor
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorCMHelper
{
	/**
	 * Checks if a valid CM account is set up
	 *
	 * @return	bool
	 */
	public static function checkAccount()
	{
		// try and call the CM object
		try
		{
			self::getCM();
		}

		catch(Exception $e)
		{
			return false;
		}

		// if we made it here, the account was valid
		return true;
	}


	/**
	 * Creates a new client
	 *
	 * @return	void
	 * @param	string $companyName			The client company name.
	 * @param	string $contactName			The personal name of the principle contact for this client.
	 * @param	string $email				An email address to which this client will be sent application-related emails.
	 * @param	string[optional] $country	This client’s country.
	 * @param	string[optional] $timezone	Client timezone for tracking and reporting data.
	 */
	public static function createClient($companyName, $contactName, $email, $country = 'Belgium', $timezone = '(GMT+01:00) Brussels, Copenhagen, Madrid, Paris')
	{
		// create client
		$clientId = self::getCM()->createClient($companyName, $contactName, $email, $country, $timezone);

		// add client ID as a module setting for mailmotor
		BackendModel::setModuleSetting('mailmotor', 'cm_client_id', $clientId);
	}


	/**
	 * Creates a new custom field for a given group
	 *
	 * @return	bool
	 * @param	string $name	The name of the custom field.
	 * @param	int $groupId	The group ID you want to add the custom field for.
	 */
	public static function createCustomField($name, $groupId)
	{
		// get CM ID for this list
		$listId = self::getCampaignMonitorID('list', $groupId);

		// list ID found
		if(!empty($listId))
		{
			// create the field
			self::getCM()->createCustomField($name, 'text', null, $listId);

			// return true
			return true;
		}

		// if we made it here, return false
		return false;
	}


	/**
	 * Deletes a custom field from a given group
	 *
	 * @return	void
	 * @param	string $name	The name of the custom field.
	 * @param	string $groupId	The CampaignMonitor ID of the list you want to remove the custom field from.
	 */
	public static function deleteCustomField($name, $groupId)
	{
		// get CM ID for this list
		$listId = self::getCampaignMonitorID('list', $groupId);

		// list ID found
		if(!empty($listId))
		{
			// create the field
			self::getCM()->deleteCustomField($name, $listId);

			// return true
			return true;
		}

		// if we made it here, return false
		return false;
	}


	/**
	 * Deletes one or more groups
	 *
	 * @return	void
	 * @param 	mixed $ids	The ids to delete.
	 */
	public static function deleteGroups($ids)
	{
		// get DB
		$db = BackendModel::getDB(true);

		// if $ids is not an array, make one
		$ids = (!is_array($ids)) ? array($ids) : $ids;

		/*
		 * I know this is messy, but since you can remove multiple groups at the same time in the datagrid ànd remove the record in CM
		 * we need to loop the ID's one by one
		 */

		// loop the list
		foreach($ids as $id)
		{
			// a list was deleted
			if(self::getCM()->deleteList(self::getCampaignMonitorID('list', $id)))
			{
				// delete group
				BackendMailmotorModel::deleteGroups($id);

				// delete CampaignMonitor reference
				$db->delete('mailmotor_campaignmonitor_ids', 'type = ? AND other_id = ?', array('list', $id));
			}
		}
	}


	/**
	 * Deletes one or more mailings
	 *
	 * @return	void
	 * @param 	mixed $ids	The ids to delete.
	 */
	public static function deleteMailings($ids)
	{
		// if $ids is not an array, make one
		$ids = (!is_array($ids)) ? array($ids) : $ids;

		/*
		 * I know this is messy, but since you can remove multiple mailings at the same time in the datagrid ànd remove the record in CM
		 * we need to loop the ID's one by one
		 */

		// loop the list
		foreach($ids as $id)
		{
			/*
				Depending on when you call this method it may or may not trigger an exception due to emails no longer existing with CM.
				That's why this bit is in a try/catch.
			*/
			try
			{
				self::getCM()->deleteCampaign(self::getCampaignMonitorID('campaign', $id));
			}

			// ignore exceptions
			catch(Exception $e)
			{
				// do nothing
			}

			// delete group
			BackendMailmotorModel::delete($id);
		}
	}


	/**
	 * Returns all bounces
	 *
	 * @return	array
	 * @param	int $id		The id of the campaign.
	 */
	public static function getBounces($id)
	{
		// get campaignmonitor ID
		$cmId = self::getCampaignMonitorID('campaign', $id);

		// get all bounces from CM
		$bounces = BackendMailmotorCMHelper::getCM()->getCampaignBounces($cmId);

		// get all addresses
		$addresses = BackendMailmotorModel::getAddressesAsPairs();

		// no bounces found
		if(empty($bounces)) return array();

		// loop the bounces, check what bounces are still in our database
		foreach($bounces as $key => $bounce)
		{
			// check if the bounced address is in the full list of addresses
			if(!in_array($bounce['email'], $addresses)) unset($bounces[$key]);
		}

		// return the bounces
		return (array) $bounces;
	}


	/**
	 * Inserts a record into the mailmotor_campaignmonitor_ids table
	 *
	 * @return	string
	 * @param	string $type		The type to insert.
	 * @param	string $otherId		The ID in our tables.
	 */
	public static function getCampaignMonitorID($type, $otherId)
	{
		// get db
		$db = BackendModel::getDB();

		// insert the campaignmonitor ID
		return $db->getVar('SELECT cm_id
							FROM mailmotor_campaignmonitor_ids
							WHERE type = ? AND other_id = ?',
							array($type, $otherId));
	}


	/**
	 * Returns the CM IDs for a given list of group IDs
	 *
	 * @return	array
	 * @param 	array $groupIds		The IDs of the groups.
	 */
	public static function getCampaignMonitorIDsForGroups(array $groupIds)
	{
		// stop here if no groups are found
		if(empty($groupIds)) return array();

		// fetch campaignmonitor IDs
		return (array) BackendModel::getDB()->getColumn('SELECT mci.cm_id
															FROM mailmotor_campaignmonitor_ids AS mci
															WHERE mci.type = ? AND mci.other_id IN (' . implode(',', $groupIds) . ')',
															array('list'));
	}


	/**
	 * Returns the CM IDs for a given list of template IDs
	 *
	 * @return	array
	 * @param 	array $templateIds	The ids of the templates.
	 */
	public static function getCampaignMonitorIDsForTemplates($templateIds)
	{
		// check if templates are set,
		$templates = (empty($templateIds)) ? array(BackendMailmotorModel::getDefaultTemplateID()) : $templateIds;

		// fetch campaignmonitor IDs
		return (array) BackendModel::getDB()->getColumn('SELECT mci.cm_id
															FROM mailmotor_campaignmonitor_ids AS mci
															WHERE mci.type = ? AND mci.other_id IN (' . implode(',', $templates) . ')',
															array('template'));
	}


	/**
	 * Returns the client ID from the settings
	 *
	 * @return	string
	 */
	public static function getClientID()
	{
		return (string) BackendModel::getModuleSetting('mailmotor', 'cm_client_id');
	}


	/**
	 * Returns the clients for use in a dropdown
	 *
	 * @return	array
	 */
	public static function getClientsAsPairs()
	{
		// get the base stack of clients
		$clients = self::getCM()->getClients();

		// stop here if no clients were found
		if(empty($clients)) return array();

		// reserve results stack
		$results = array();
		$results[0] = ucfirst(BL::lbl('CreateNewClient', 'mailmotor'));

		// loop the clients
		foreach($clients as $client)
		{
			$results[$client['id']] = $client['name'];
		}

		// return the results
		return $results;
	}


	/**
	 * Returns the CampaignMonitor object.
	 *
	 * @return	CampaignMonitor
	 */
	public static function getCM()
	{
		// campaignmonitor reference exists
		if(!Spoon::exists('campaignmonitor'))
		{
			// check if the CampaignMonitor class exists
			if(!SpoonFile::exists(PATH_LIBRARY . '/external/campaignmonitor.php'))
			{
				// the class doesn't exist, so throw an exception
				throw new SpoonFileException(BL::err('ClassDoesNotExist', 'mailmotor'));
			}

			// require CampaignMonitor class
			require_once 'external/campaignmonitor.php';

			// set login data
			$url = BackendModel::getModuleSetting('mailmotor', 'cm_url');
			$username = BackendModel::getModuleSetting('mailmotor', 'cm_username');
			$password = BackendModel::getModuleSetting('mailmotor', 'cm_password');

			// init CampaignMonitor object
			$cm = new CampaignMonitor($url, $username, $password, 5, self::getClientId());

			// set CampaignMonitor object reference
			Spoon::set('campaignmonitor', $cm);
		}

		// return the CampaignMonitor object
		return Spoon::get('campaignmonitor');
	}


	/**
	 * Returns the CampaignMonitor countries as pairs
	 *
	 * @return	array
	 */
	public static function getCountriesAsPairs()
	{
		// get the countries
		$records = self::getCM()->getCountries();

		// loop and make em pairs
		foreach($records as &$record) $records[$record] = $record;

		// return the countries
		return $records;
	}


	/**
	 * Returns the custom fields by a given group ID
	 *
	 * @return	array
	 * @param	int $groupId	The id of the group.
	 */
	public static function getCustomFields($groupId)
	{
		// get CM ID for this group
		$listId = self::getCampaignMonitorID('list', $groupId);

		// get the custom fields from CM
		$cmFields = self::getCM()->getCustomFields($listId);

		// reserve new fields array
		$newFields = array();

		// fields found
		if(!empty($cmFields))
		{
			// get the custom fields from our database
			$fields = BackendMailmotorModel::getCustomFields($groupId);

			// loop the fields
			foreach($cmFields as $field)
			{
				// check if the field exists already. If not; add it
				if(!in_array($field['name'], $fields)) $fields[] = $field['name'];
			}

			// update the fields
			BackendMailmotorModel::updateCustomFields($fields, $groupId);
		}

		// return the results
		return (array) $fields;
	}


	/**
	 * Returns what addresses opened a certain mailing
	 *
	 * @return	array
	 * @param	string $cmId				The id of the mailing in CampaignMonitor.
	 * @param	bool[optional] $getColumn	If this is true, it will return an array with just the email addresses who opened the mailing.
	 */
	public static function getMailingOpens($cmId, $getColumn = false)
	{
		// fetch the campaign opens from CM
		$records = self::getCM()->getCampaignOpens($cmId);

		// check we have records
		if(empty($records)) return false;

		// return the records
		if(!$getColumn) return (array) $records;

		// new result stack
		$results = array();

		// loop the records, save emails to new result stack
		foreach($records as $record) $results[] = $record['email'];

		// return the results
		return (array) $results;
	}


	/**
	 * Get the custom field value for 'name'
	 *
	 * @return	string
	 * @param	array $fields		The custom fields array.
	 */
	private static function getNameFieldValue($fields)
	{
		// check input
		$name = null;

		// set the name if it is present in the custom fields
		if(isset($fields['name'])) $name = $fields['name'];
		elseif(isset($fields['Name'])) $name = $fields['Name'];
		elseif(isset($fields[BL::lbl('Name')])) $name = $fields[BL::lbl('Name')];

		// return the value
		return $name;
	}


	/**
	 * Returns the statistics for a given mailing
	 *
	 * @return	array
	 * @param	int $id							The id of the mailing.
	 * @param	bool[optional] $fetchClicks		If the click-count should be included.
	 * @param	bool[optional] $fetchOpens		If the open-count should be included.
	 */
	public static function getStatistics($id, $fetchClicks = false, $fetchOpens = false)
	{
		// check if the mailing exists
		if(!BackendMailmotorModel::existsMailing($id)) throw new SpoonException('No mailing found for id ' . $id);

		// fetch cmID
		$cmId = self::getCampaignMonitorID('campaign', $id);

		// fetch the CM ID
		if($cmId)
		{
			// fetch the statistics
			$stats = self::getCM()->getCampaignSummary($cmId);

			// stop here if no recipients were found
			if($stats['recipients'] == 0) return false;

			// reset the bounces to match the real ones
			$bounces = BackendMailmotorCMHelper::getBounces($id);

			// re-calculate base stats to match CM's
			$stats['bounces'] = count($bounces);
			$stats['recipients'] = ($stats['recipients']);
			$stats['recipients_total'] = ($stats['recipients']);
			$stats['unopens'] = $stats['recipients'] - $stats['unique_opens'] - $stats['bounces'];
			$stats['clicks_total'] = 0;

			// add percentages to these stats
			$stats['bounces_percentage'] = ($stats['recipients'] == 0) ? 0 : floor(($stats['bounces'] / $stats['recipients_total']) * 100) . '%';
			$stats['recipients_percentage'] = ($stats['recipients'] == 0) ? 0 : ceil(($stats['recipients'] / $stats['recipients_total']) * 100) . '%';
			$stats['unique_opens_percentage'] = ($stats['recipients'] == 0) ? 0 : ceil(($stats['unique_opens'] / $stats['recipients']) * 100) . '%';
			$stats['unopens_percentage'] = ($stats['recipients'] == 0) ? 0 : floor(($stats['unopens'] / $stats['recipients']) * 100) . '%';
			$stats['clicks_percentage'] = ($stats['recipients'] == 0) ? 0 : ceil(($stats['clicks'] / $stats['recipients']) * 100) . '%';

			// fetch clicks or not?
			if($fetchClicks)
			{
				// get detailed click reports
				$subscriberClicks = self::getCM()->getCampaignClicks($cmId);

				// links have been clicked
				if(!empty($subscriberClicks))
				{
					// declare array
					$stats['clicked_links'] = array();
					$stats['clicked_links_by'] = $subscriberClicks;

					// filter out the clicked links
					foreach($subscriberClicks as $link => $clickers)
					{
						// count the clickers
						$clickerCount = count($clickers);
						$stats['clicked_links'][] = array('link' => $link, 'clicks' => $clickerCount);
						$stats['clicks_total'] += $clickerCount;
					}

					/*
					// re-loop so we can fix the keys
					foreach($stats['clicked_links'] as $link)
					{
						// store the link data
						$stats['clicked_links'][] = array('link' => urlencode($link['link']));

						// unset the record with the link as key
						unset($stats['clicked_links'][$link['link']]);
					}

					// re-loop so we can fix the keys
					foreach($stats['clicked_links_by'] as $link => $clicks)
					{
						// loop the clicks
						foreach($clicks as $click)
						{
							// store the link data
							$stats['clicked_links_by'][$link][] = array('email' => $click['email']);

							// unset the record with the link as key
							unset($stats['clicked_links_by'][$link][$click['email']]);
						}
					}
					*/
				}
			}

			// fetch opened stats or not?
			if($fetchOpens)
			{
				// fetch opens
				$stats['opens'] = self::getMailingOpens($cmId);
			}

			// return the results
			return $stats;
		}

		// at this point, return false
		return false;
	}


	/**
	 * Returns the statistics for a given e-mail address
	 *
	 * @return	array
	 * @param	string $email	The emailaddress to get the stats for.
	 */
	public static function getStatisticsByAddress($email)
	{
		// reserve statistics array
		$stats = array();
		$stats['recipients'] = 0;
		$stats['opens'] = 0;
		$stats['unique_opens'] = 0;
		$stats['clicks'] = 0;
		$stats['unsubscribes'] = 0;
		$stats['bounces'] = 0;
		$stats['recipients_total'] = 0;
		$stats['unopens'] = 0;

		// fetch all mailings in this campaign
		$mailings = BackendModel::getDB()->getRecords(BackendMailmotorModel::QRY_DATAGRID_BROWSE_SENT_FOR_CAMPAIGN, array('sent', $email));

		// no mailings found
		if(empty($mailings)) return array();

		// loop the mailings
		foreach($mailings as $mailing)
		{
			// store the statistics in a separate array
			$mailingStats = self::getStatistics($mailing['id']);

			// add all stats to the totals
			$stats['recipients'] += $mailingStats['recipients'];
			$stats['opens'] += $mailingStats['opens'];
			$stats['unique_opens'] += $mailingStats['unique_opens'];
			$stats['clicks'] += $mailingStats['clicks'];
			$stats['unsubscribes'] += $mailingStats['unsubscribes'];
			$stats['bounces'] += $mailingStats['bounces'];
			$stats['recipients_total'] += $mailingStats['recipients_total'];
			$stats['unopens'] += $mailingStats['unopens'];
		}

		// add percentages to these stats
		$stats['bounces_percentage'] = floor(($stats['bounces'] / $stats['recipients_total']) * 100) . '%';
		$stats['recipients_percentage'] = ceil(($stats['recipients'] / $stats['recipients_total']) * 100) . '%';
		$stats['unique_opens_percentage'] = ceil(($stats['unique_opens'] / $stats['recipients']) * 100) . '%';
		$stats['unopens_percentage'] = floor(($stats['unopens'] / $stats['recipients']) * 100) . '%';
		$stats['clicks_percentage'] = ceil(($stats['clicks'] / $stats['recipients']) * 100) . '%';

		// return the stats
		return (array) $stats;
	}


	/**
	 * Returns the statistics for all mailings in a given campaign
	 *
	 * @return	array
	 * @param	int $id		The id of the campaign.
	 */
	public static function getStatisticsByCampaignID($id)
	{
		// reserve statistics array
		$stats = array();
		$stats['recipients'] = 0;
		$stats['opens'] = 0;
		$stats['unique_opens'] = 0;
		$stats['clicks'] = 0;
		$stats['unsubscribes'] = 0;
		$stats['bounces'] = 0;
		$stats['recipients_total'] = 0;
		$stats['unopens'] = 0;

		// fetch all mailings in this campaign
		$mailings = BackendModel::getDB()->getRecords(BackendMailmotorModel::QRY_DATAGRID_BROWSE_SENT_FOR_CAMPAIGN, array('sent', $id));

		// no mailings found
		if(empty($mailings)) return array();

		// loop the mailings
		foreach($mailings as $mailing)
		{
			// store the statistics in a separate array
			$mailingStats = self::getStatistics($mailing['id'], false);

			// add all stats to the totals
			$stats['recipients'] += $mailingStats['recipients'];
			$stats['opens'] += $mailingStats['opens'];
			$stats['unique_opens'] += $mailingStats['unique_opens'];
			$stats['clicks'] += $mailingStats['clicks'];
			$stats['unsubscribes'] += $mailingStats['unsubscribes'];
			$stats['bounces'] += $mailingStats['bounces'];
			$stats['recipients_total'] += $mailingStats['recipients_total'];
			$stats['unopens'] += $mailingStats['unopens'];
		}

		// add percentages to these stats
		$stats['bounces_percentage'] = floor(($stats['bounces'] / $stats['recipients_total']) * 100) . '%';
		$stats['recipients_percentage'] = ceil(($stats['recipients'] / $stats['recipients_total']) * 100) . '%';
		$stats['unique_opens_percentage'] = ceil(($stats['unique_opens'] / $stats['recipients']) * 100) . '%';
		$stats['unopens_percentage'] = floor(($stats['unopens'] / $stats['recipients']) * 100) . '%';
		$stats['clicks_percentage'] = ceil(($stats['clicks'] / $stats['recipients']) * 100) . '%';

		// return the stats
		return (array) $stats;
	}


	/**
	 * Returns the CampaignMonitor countries as pairs
	 *
	 * @return	array
	 */
	public static function getTimezonesAsPairs()
	{
		// get the timezones
		$records = self::getCM()->getTimezones();

		// loop and make em pairs
		foreach($records as &$record) $records[$record] = $record;

		// return the timezones
		return $records;
	}


	/**
	 * Inserts a record into the mailmotor_campaignmonitor_ids table
	 *
	 * @return	string
	 * @param	string $type		The type of the record.
	 * @param	string $id			The id in CampaignMonitor.
	 * @param	string $otherId		The id in our tables.
	 */
	public static function insertCampaignMonitorID($type, $id, $otherId)
	{
		// check input
		$type = SpoonFilter::getValue($type, array('campaign', 'list', 'template'), '');

		// no valid type given
		if($type == '') throw new CampaignMonitorException('No valid CM ID type given (only campaign, list, template).');

		// insert the campaignmonitor ID
		BackendModel::getDB(true)->insert('mailmotor_campaignmonitor_ids', array('type' => $type, 'cm_id' => $id, 'other_id' => $otherId));
	}


	/**
	 * Creates a list in campaignmonitor and inserts the group record in the database. Returns the group ID
	 *
	 * @return	int
	 * @param	array $item		The group record to insert.
	 */
	public static function insertGroup(array $item)
	{
		// build unsubscribe link for this list
		$unsubscribeLink = SITE_URL . BackendModel::getURLForBlock('mailmotor', 'unsubscribe', BL::getWorkingLanguage());

		// predict the next insert ID for the mailmotor_groups table
		$groupId = BackendMailmotorModel::getMaximumIdForGroups() + 1;

		// create list
		$cmId = self::getCM()->createList($item['name'], $unsubscribeLink . '/?group=' . $groupId . '&email=[email]');

		// a list was created
		if($cmId)
		{
			// check if we have a default group set
			if($item['is_default'] === 'Y' && $item['language'] != '0')
			{
				// set all defaults to N.
				BackendModel::getDB(true)->update('mailmotor_groups', array('is_default' => 'N', 'language' => null), 'language = ?', $item['language']);
			}

			// insert in database
			$id = BackendMailmotorModel::insertGroup($item);

			// insert in campaignmonitor
			self::insertCampaignMonitorID('list', $cmId, $id);

			// return the group ID
			return (int) $id;
		}
	}


	/**
	 * Creates a campaign in campaignmonitor. Returns the campaign ID
	 *
	 * @return	mixed
	 * @param	array $item		The mailing record to insert.
	 */
	public static function insertMailing(array $item)
	{
		// create campaign in CM
		$cmId = self::getCM()->createCampaign($item['name'], $item['subject'], $item['from_name'], $item['from_email'], $item['reply_to_email'], $item['content_html_url'], $item['content_plain_url'], $item['group_cm_ids']);

		// a campaign was created
		if($cmId)
		{
			// insert in campaignmonitor
			self::insertCampaignMonitorID('campaign', $cmId, $item['id']);

			// return the campaign CM ID
			return $cmId;
		}

		// no campaign created at this point
		return false;
	}


	/**
	 * Creates a campaign in campaignmonitor and sends it
	 *
	 * @return	void
	 * @param	array $item		The mailing record to insert.
	 */
	public static function sendMailing($item)
	{
		// get db
		$db = BackendModel::getDB(true);

		// fetch the CM IDs for each group if this field is not set yet
		if(!isset($item['group_cm_ids'])) $item['group_cm_ids'] = self::getCampaignMonitorIDsForGroups($item['groups']);

		// fetch the content URLs
		if(!isset($item['content_html_url'])) $item['content_html_url'] = BackendMailmotorModel::getMailingPreviewURL($item['id'], 'html', true);
		if(!isset($item['content_plain_url'])) $item['content_plain_url'] = BackendMailmotorModel::getMailingPreviewURL($item['id'], 'plain', true);

		// create the campaign in CM
		$result = self::insertMailing($item);

		// if result equals false, we have a problem
		if($result === false) throw new SpoonException('The mailing couldn\'t be created, please try again.');

		// at this point $result should equal the CM ID, so let's attempt to send it
		self::getCM()->sendCampaign($result, $item['from_email'], $item['delivery_date']);
	}


	/**
	 * Subscribes an e-mail address and send him/her to CampaignMonitor
	 *
	 * @return	bool
	 * @param	string $email					The emailaddress.
	 * @param	string[optional] $groupId		The group wherin the emailaddress should be added.
	 * @param	array[optional] $customFields	Any optional custom fields.
	 */
	public static function subscribe($email, $groupId = null, $customFields = null)
	{
		// get objects
		$db = BackendModel::getDB(true);
		$cm = self::getCM();

		// set groupID
		$groupId = !empty($groupId) ? $groupId : BackendMailmotorModel::getDefaultGroupID();

		// get group CM ID
		$groupCMId = self::getCampaignMonitorID('list', $groupId);

		// see if the name is present in the custom fields
		$name = self::getNameFieldValue($customFields);

		// group ID found
		if(BackendMailmotorModel::existsGroup($groupId) && $cm->subscribe($email, $name, $customFields, true, $groupCMId))
		{
			// set variables
			$subscriber['email'] = $email;
			$subscriber['source'] = 'CMS';
			$subscriber['created_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');

			// insert/update the user
			$db->execute('INSERT INTO mailmotor_addresses(email, source, created_on)
							VALUES (?, ?, ?)
							ON DUPLICATE KEY UPDATE source = ?, created_on = ?',
							array($subscriber['email'], $subscriber['source'], $subscriber['created_on'],
									$subscriber['source'], $subscriber['created_on']));

			// set variables
			$subscriberGroup['email'] = $email;
			$subscriberGroup['group_id'] = $groupId;
			$subscriberGroup['status'] = 'subscribed';
			$subscriberGroup['subscribed_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');

			// insert/update the user
			$db->execute('INSERT INTO mailmotor_addresses_groups(email, group_id, status, subscribed_on)
							VALUES (?, ?, ?, ?)
							ON DUPLICATE KEY UPDATE group_id = ?, status = ?, subscribed_on = ?',
							array($subscriberGroup['email'], $subscriberGroup['group_id'], $subscriberGroup['status'], $subscriberGroup['subscribed_on'],
									$subscriberGroup['group_id'], $subscriberGroup['status'], $subscriberGroup['subscribed_on']));

			// update custom fields for this subscriber/group
			if(!empty($customFields)) BackendMailmotorModel::updateCustomFields($customFields, $groupId, $email);

			// user subscribed
			return true;
		}

		// user not subscribed
		return false;
	}


	/**
	 * Unsubscribes an e-mail address from CampaignMonitor and our database
	 *
	 * @return	bool
	 * @param	string $email				The emailaddress to unsubscribe.
	 * @param	string[optional] $groupId	The group wherefrom the emailaddress should be unsubscribed.
	 */
	public static function unsubscribe($email, $groupId = null)
	{
		// get objects
		$cm = self::getCM();

		// set group ID
		$groupId = !empty($groupId) ? $groupId : BackendMailmotorModel::getDefaultGroupID();

		// get group CM ID
		$groupCMId = self::getCampaignMonitorID('list', $groupId);

		// group exists
		if(BackendMailmotorModel::existsGroup($groupId))
		{
			// unsubscribe the email from this group
			self::getCM()->unsubscribe($email, $groupCMId);

			// set variables
			$subscriber = array();
			$subscriber['status'] = 'unsubscribed';
			$subscriber['unsubscribed_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');

			// unsubscribe the user
			BackendModel::getDB(true)->update('mailmotor_addresses_groups', $subscriber, 'email = ? AND group_id = ?', array($email, $groupId));

			// user unsubscribed
			return true;
		}

		// user not unsubscribed
		return false;
	}


	/**
	 * Updates a client
	 *
	 * @return	void
	 * @param	string $companyName			The client company name.
	 * @param	string $contactName			The personal name of the principle contact for this client.
	 * @param	string $email				An email address to which this client will be sent application-related emails.
	 * @param	string[optional] $country	This client’s country.
	 * @param	string[optional] $timezone	Client timezone for tracking and reporting data.
	 */
	public static function updateClient($companyName, $contactName, $email, $country = 'Belgium', $timezone = '(GMT+01:00) Brussels, Copenhagen, Madrid, Paris')
	{
		// update client
		self::getCM()->updateClientBasics($companyName, $contactName, $email, $country, $timezone);
	}


	/**
	 * Updates a list with campaignmonitor and in the database. Returns the affected rows
	 *
	 * @return	int
	 * @param	array $item		The new data.
	 */
	public static function updateGroup($item)
	{
		// build unsubscribe link for this list
		$unsubscribeLink = SITE_URL . BackendModel::getURLForBlock('mailmotor', 'unsubscribe', BL::getWorkingLanguage());

		// update the group with CM
		self::getCM()->updateList($item['name'], $unsubscribeLink . '/?group=' . $item['id'] . '&email=[email]', null, null, self::getCampaignMonitorID('list', $item['id']));

		// check if we have a default group set
		if($item['is_default'] === 'Y' && $item['language'] != '0')
		{
			// set all defaults to N
			BackendModel::getDB(true)->update('mailmotor_groups', array('is_default' => 'N', 'language' => null), 'language = ?', array($item['language']));
		}

		// update the group in our database
		return (int) BackendMailmotorModel::updateGroup($item);
	}


	/**
	 * Updates a mailing
	 *
	 * @return	void
	 * @param	array $item		The mailing record to update.
	 */
	public static function updateMailing(array $item)
	{
		// local item
		$local = $item;

		// delete the mailing
		self::deleteMailings($item['id']);

		// fetch the CM IDs for each group if this field is not set yet
		if(!isset($item['group_cm_ids'])) $item['group_cm_ids'] = self::getCampaignMonitorIDsForGroups($item['groups']);

		// fetch the content URLs
		if(!isset($item['content_html_url'])) $item['content_html_url'] = BackendMailmotorModel::getMailingPreviewURL($item['id'], 'html', true);
		if(!isset($item['content_plain_url'])) $item['content_plain_url'] = BackendMailmotorModel::getMailingPreviewURL($item['id'], 'plain', true);

		// overwrite the name, because the previous one is taken -.-
		$item['name'] .= ' (#' . rand(0, 999) . ')';

		// re-insert the mailing in CM
		self::insertMailing($item);

		// unset vars we don't need, save vars we need later
		$groups = $local['groups'];
		unset($local['cm_id'], $local['groups'], $local['recipients'], $local['delivery_date']);

		// serialize full content mailing
		$local['data'] = serialize($local['data']);

		// re-insert the mailing in our database
		$id = BackendMailmotorModel::insertMailing($local);

		// reinsert the groups for this mailing
		BackendMailmotorModel::updateGroupsForMailing($id, $groups);
	}
}

?>