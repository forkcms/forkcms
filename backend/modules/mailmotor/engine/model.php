<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the mailmotor module
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class BackendMailmotorModel
{
	const QRY_DATAGRID_BROWSE_CAMPAIGNS =
		'SELECT c.*, UNIX_TIMESTAMP(c.created_on) AS created_on
		 FROM mailmotor_campaigns AS c';

	const QRY_DATAGRID_BROWSE_GROUPS =
		'SELECT mg.id, mg.name, mg.language, mg.is_default, UNIX_TIMESTAMP(mg.created_on) AS created_on
		 FROM mailmotor_groups AS mg';

	const QRY_DATAGRID_BROWSE_SENT =
		'SELECT mm.id, mm.name, mc.id AS campaign_id, mc.name AS campaign_name, UNIX_TIMESTAMP(mm.send_on) AS sent, mm.language, mm.status
		 FROM mailmotor_mailings AS mm
		 LEFT OUTER JOIN mailmotor_campaigns AS mc ON mc.id = mm.campaign_id
		 WHERE mm.status = ?';

	const QRY_DATAGRID_BROWSE_SENT_FOR_CAMPAIGN =
		'SELECT mm.id, mm.name, mc.id AS campaign_id, mc.name AS campaign_name, UNIX_TIMESTAMP(mm.send_on) AS sent, mm.language, mm.status
		 FROM mailmotor_mailings AS mm
		 INNER JOIN mailmotor_campaigns AS mc ON mc.id = mm.campaign_id
		 WHERE mm.status = ? AND mm.campaign_id = ?';

	const QRY_DATAGRID_BROWSE_UNSENT =
		'SELECT mm.id, mm.name, mc.id AS campaign_id, mc.name AS campaign_name, UNIX_TIMESTAMP(mm.created_on) AS created_on, mm.language, mm.status
		 FROM mailmotor_mailings AS mm
		 LEFT OUTER JOIN mailmotor_campaigns AS mc ON mc.id = mm.campaign_id
		 WHERE mm.status = ?';

	const QRY_DATAGRID_BROWSE_UNSENT_FOR_CAMPAIGN =
		'SELECT mm.id, mm.name, mc.id AS campaign_id, mc.name AS campaign_name, UNIX_TIMESTAMP(mm.created_on) AS created_on, mm.language, mm.status
		 FROM mailmotor_mailings AS mm
		 INNER JOIN mailmotor_campaigns AS mc ON mc.id = mm.campaign_id
		 WHERE mm.status = ? AND mm.campaign_id = ?';

	/**
	 * Returns true if every working language has a default group set, false if at least one is missing.
	 *
	 * @return bool
	 */
	public static function checkDefaultGroups()
	{
		// check if the defaults were set already, and return true if they were
		if(BackendModel::getModuleSetting('mailmotor', 'cm_groups_defaults_set')) return true;

		// get all default groups
		$defaults = self::getDefaultGroups();

		// if the total amount of working languages do not add up to the total amount of default groups not all default groups were set.
		if(count(BL::getWorkingLanguages()) === count($defaults))
		{
			// cm_groups_defaults_set status is now true
			BackendModel::setModuleSetting('mailmotor', 'cm_groups_defaults_set', true);

			// return true
			return true;
		}

		// if we made it here, not all default groups were set; return false
		return false;
	}

	/**
	 * Checks the settings and optionally returns an array with warnings
	 *
	 * @return array
	 */
	public static function checkSettings()
	{
		$warnings = array();

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('settings', 'mailmotor'))
		{
			// analytics session token
			if(BackendModel::getModuleSetting('mailmotor', 'cm_account') == false)
			{
				$warnings[] = array('message' => sprintf(BL::err('AnalysisNoCMAccount', 'mailmotor'), BackendModel::createURLForAction('settings', 'mailmotor')));
			}
			elseif(BackendModel::getModuleSetting('mailmotor', 'cm_client_id') == '')
			{
				// add warning
				$warnings[] = array('message' => sprintf(BL::err('AnalysisNoCMClientID', 'mailmotor'), BackendModel::createURLForAction('settings', 'mailmtor')));
			}
		}

		return $warnings;
	}

	/**
	 * Deletes one or more mailings
	 *
	 * @param  mixed $ids The ids to delete.
	 */
	public static function delete($ids)
	{
		// make sure ids are set
		if(empty($ids)) return;

		// get DB
		$db = BackendModel::getDB(true);

		// if $ids is not an array, make it one
		$ids = (array) $ids;

		// delete records
		$db->delete('mailmotor_mailings', 'id IN (' . implode(',', $ids) . ')');
		$db->delete('mailmotor_mailings_groups', 'mailing_id IN (' . implode(',', $ids) . ')');

		// delete CampaignMonitor references
		$db->delete('mailmotor_campaignmonitor_ids', 'type = ? AND other_id IN (' . implode(',', $ids) . ')', array('campaign'));
	}

	/**
	 * Deletes one or more e-mail addresses
	 *
	 * @param  mixed $emails The emails to delete.
	 */
	public static function deleteAddresses($emails)
	{
		// make sure emails are set
		if(empty($emails)) return;

		// get DB
		$db = BackendModel::getDB(true);

		// if $ids is not an array, make one
		$emails = (array) $emails;

		// delete records
		$db->delete('mailmotor_addresses', 'email IN ("' . implode('","', $emails) . '")');
		$db->delete('mailmotor_addresses_groups', 'email IN ("' . implode('","', $emails) . '")');
	}

	/**
	 * Deletes one or more campaigns
	 *
	 * @param  mixed $ids The ids to delete.
	 */
	public static function deleteCampaigns($ids)
	{
		// make sure ids are set
		if(empty($ids)) return;

		// get DB
		$db = BackendModel::getDB(true);

		// if $ids is not an array, make it one
		$ids = (array) $ids;

		// delete records
		$db->delete('mailmotor_campaigns', 'id IN (' . implode(',', $ids) . ')');

		// update all mailings for the ids
		$db->update('mailmotor_mailings', array('campaign_id' => 0), 'campaign_id IN (' . implode(',', $ids) . ')');
	}

	/**
	 * Deletes one or more groups
	 *
	 * @param  mixed $ids The ids to delete.
	 */
	public static function deleteGroups($ids)
	{
		// make sure ids are set
		if(empty($ids)) return;

		// get DB
		$db = BackendModel::getDB(true);

		// if $ids is not an array, make one
		$ids = (array) $ids;

		// delete records
		$db->delete('mailmotor_groups', 'id IN (' . implode(',', $ids) . ')');
		$db->delete('mailmotor_addresses_groups', 'group_id IN (' . implode(',', $ids) . ')');
		$db->delete('mailmotor_mailings_groups', 'group_id IN (' . implode(',', $ids) . ')');
	}

	/**
	 * Checks if an e-mailaddress exists
	 *
	 * @param string $email The emailaddress to check for existance.
	 * @return bool
	 */
	public static function existsAddress($email)
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT COUNT(ma.email)
			 FROM mailmotor_addresses AS ma
			 WHERE ma.email = ?',
			array((string) $email)
		);
	}

	/**
	 * Checks if a campaign exists
	 *
	 * @param int $id The id of the campaign.
	 * @return bool
	 */
	public static function existsCampaign($id)
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT COUNT(mc.id)
			 FROM mailmotor_campaigns AS mc
			 WHERE mc.id = ?',
			array((int) $id)
		);
	}

	/**
	 * Checks if a campaign exists
	 *
	 * @param string $name The name of the campaign to check for existance.
	 * @return bool
	 */
	public static function existsCampaignByName($name)
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT COUNT(mc.id)
			 FROM mailmotor_campaigns AS mc
			 WHERE mc.name = ?',
			array((string) $name)
		);
	}

	/**
	 * Checks if a group exists
	 *
	 * @param int $id The id of the group to check.
	 * @return bool
	 */
	public static function existsGroup($id)
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT COUNT(mg.id)
			 FROM mailmotor_groups AS mg
			 WHERE mg.id = ?',
			array((int) $id)
		);
	}

	/**
	 * Checks if a group exists
	 *
	 * @param string $name The name of the group to check.
	 * @return bool
	 */
	public static function existsGroupByName($name)
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT COUNT(mg.id)
			 FROM mailmotor_groups AS mg
			 WHERE mg.name = ? AND mg.language = ?',
			array((string) $name, BL::getWorkingLanguage())
		);
	}

	/**
	 * Checks if a mailing exists
	 *
	 * @param int $id The id of the mailing to check.
	 * @return bool
	 */
	public static function existsMailing($id)
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT COUNT(mm.id)
			 FROM mailmotor_mailings AS mm
			 WHERE mm.id = ?',
			array((int) $id)
		);
	}

	/**
	 * Checks if a mailing exists by name
	 *
	 * @param string $name The name of the mailing to check.
	 * @return bool
	 */
	public static function existsMailingByName($name)
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT COUNT(mm.id)
			 FROM mailmotor_mailings AS mm
			 WHERE mm.name = ?',
			array((string) $name)
		);
	}

	/**
	 * Checks if there are mailings without campaigns assigned
	 *
	 * @return bool
	 */
	public static function existsMailingsWithoutCampaign()
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT COUNT(mm.id)
			 FROM mailmotor_mailings AS mm
			 WHERE mm.campaign_id IS NOT NULL'
		);
	}

	/**
	 * Checks if a given campaign has sent mailings under it
	 *
	 * @param int $id The id of the campaign to check.
	 * @return bool
	 */
	public static function existsSentMailingsByCampaignID($id)
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT COUNT(mm.id)
			 FROM mailmotor_mailings AS mm
			 WHERE mm.campaign_id = ? AND mm.status = ?',
			array((int) $id, 'sent')
		);
	}

	/**
	 * Exports a series of e-mail address records in CSV format. This function will send headers to download the CSV and exit your script after use.
	 *
	 * @param array $emails The data to export.
	 */
	public static function exportAddresses(array $emails)
	{
		// set the filename and path
		$filename = 'addresses-' . SpoonDate::getDate('YmdHi') . '.csv';
		$path = BACKEND_CACHE_PATH . '/mailmotor/' . $filename;

		// reformat the created_on date
		if(!empty($emails))
		{
			foreach($emails as &$email) $email['created_on'] = SpoonDate::getDate('j F Y', $email['created_on'], BL::getWorkingLanguage());
		}

		// generate the CSV and download the file
		BackendCSV::arrayToFile($path, $emails, array(BL::lbl('Email'), BL::lbl('Created')), null, ';', '"', true);
	}

	/**
	 * Exports a series of e-mail address records by group ID in CSV format. This function will send headers to download the CSV and exit your script after use.
	 *
	 * @param int $id The id of the group to export.
	 */
	public static function exportAddressesByGroupID($id)
	{
		// set the filename and path
		$filename = 'addresses-' . SpoonDate::getDate('YmdHi') . '.csv';
		$path = BACKEND_CACHE_PATH . '/mailmotor/' . $filename;

		// fetch the addresses by group
		$records = self::getAddressesByGroupID($id);

		// fetch the group fields
		$groupFields = array_flip(self::getCustomFields($id));

		// group custom fields found
		if(!empty($groupFields))
		{
			// loop the group fields and empty every value
			foreach($groupFields as &$field) $field = '';
		}

		// records found
		if(!empty($records))
		{
			// loop records
			foreach($records as $key => $record)
			{
				// reformat the date
				$records[$key]['created_on'] = SpoonDate::getDate('j F Y', $record['created_on'], BL::getWorkingLanguage());

				// fetch custom fields for this e-mail
				$customFields = self::getCustomFieldsByAddress($record['email']);
				$customFields = !empty($customFields[$id]) ? $customFields[$id] : $groupFields;

				// loop custom fields
				foreach($customFields as $column => $value)
				{
					// add the fields to this record
					$records[$key][$column] = $value;
				}
			}
		}

		// generate the CSV and download the file
		BackendCSV::arrayToFile($path, $records, array(BL::lbl('Email'), BL::lbl('Created')), null, ';', '"', true);
	}

	/**
	 * Exports the statistics of a given mailing in CSV format. This function will send headers to download the CSV and exit your script after use.
	 *
	 * @param int $id The ID of the mailing.
	 */
	public static function exportStatistics($id)
	{
		// fetch the addresses by group
		$records = array();
		$records[] = BackendMailmotorCMHelper::getStatistics($id, true);

		// fetch separate arrays
		$statsClickedLinks = isset($records[0]['clicked_links']) ? $records[0]['clicked_links'] : array();
		$statsClickedLinksBy = isset($records[0]['clicked_links_by']) ? $records[0]['clicked_links_by'] : array();

		// unset multi-dimensional arrays
		unset($records[0]['clicked_links'], $records[0]['clicked_links_by'], $records[0]['opens'],
			$records[0]['clicks'], $records[0]['clicks_percentage'], $records[0]['clicks_total'],
			$records[0]['recipients_total'], $records[0]['recipients_percentage'], $records[0]['online_version']);

		// set columns
		$columns = array();
		$columns[] = BL::msg('MailingCSVRecipients');
		$columns[] = BL::msg('MailingCSVUniqueOpens');
		$columns[] = BL::msg('MailingCSVUnsubscribes');
		$columns[] = BL::msg('MailingCSVBounces');
		$columns[] = BL::msg('MailingCSVUnopens');
		$columns[] = BL::msg('MailingCSVBouncesPercentage');
		$columns[] = BL::msg('MailingCSVUniqueOpensPercentage');
		$columns[] = BL::msg('MailingCSVUnopensPercentage');

		// set start of the CSV
		$csv = BackendCSV::arrayToString($records, $columns);

		// check set links
		if(!empty($statsClickedLinks))
		{
			// urldecode the clicked URLs
			$statsClickedLinks = SpoonFilter::arrayMapRecursive('urldecode', $statsClickedLinks);

			// fetch CSV strings
			$csv .= PHP_EOL . BackendCSV::arrayToString($statsClickedLinks);
		}

		// set the filename and path
		$filename = 'statistics-' . SpoonDate::getDate('YmdHi') . '.csv';

		// set headers for download
		$headers = array();
		$headers[] = 'Content-type: application/octet-stream';
		$headers[] = 'Content-Disposition: attachment; filename="' . $filename . '"';

		// overwrite the headers
		SpoonHTTP::setHeaders($headers);

		// output the CSV string
		echo $csv;

		// exit here
		exit;
	}

	/**
	 * Exports the statistics of all mailings for a given campaign ID in CSV format. This function
	 * will send headers to download the CSV and exit your script after use.
	 *
	 * @param int $id The ID of the campaign.
	 */
	public static function exportStatisticsByCampaignID($id)
	{
		// set the filename and path
		$filename = 'statistics-' . SpoonDate::getDate('YmdHi') . '.csv';

		// fetch the addresses by group
		$records = array();
		$records[] = BackendMailmotorCMHelper::getStatisticsByCampaignID($id);

		// unset some records
		unset($records[0]['opens'], $records[0]['clicks'], $records[0]['clicks_percentage'],
				$records[0]['recipients_total'], $records[0]['recipients_percentage']);

		// set columns
		$columns = array();
		$columns[] = BL::msg('MailingCSVRecipients');
		$columns[] = BL::msg('MailingCSVUniqueOpens');
		$columns[] = BL::msg('MailingCSVUnsubscribes');
		$columns[] = BL::msg('MailingCSVBounces');
		$columns[] = BL::msg('MailingCSVUnopens');
		$columns[] = BL::msg('MailingCSVBouncesPercentage');
		$columns[] = BL::msg('MailingCSVUniqueOpensPercentage');
		$columns[] = BL::msg('MailingCSVUnopensPercentage');

		// set start of the CSV
		$csv = BackendCSV::arrayToString($records, $columns);

		// fetch all mailings in this campaign
		$mailings = BackendModel::getDB()->getRecords(BackendMailmotorModel::QRY_DATAGRID_BROWSE_SENT_FOR_CAMPAIGN, array('sent', $id));

		// mailings set
		if(!empty($mailings))
		{
			// set mailings columns
			$mailingColumns = array();
			$mailingColumns['name'] = BL::lbl('Name');
			$mailingColumns['language'] = BL::lbl('Language');

			// add the records to the csv string
			$csv .= PHP_EOL . 'Mailings:' . PHP_EOL . BackendCSV::arrayToString($mailings, $mailingColumns, array('id', 'campaign_id', 'campaign_name', 'send_on', 'status'));
		}

		// set headers for download
		$headers = array();
		$headers[] = 'Content-type: application/octet-stream';
		$headers[] = 'Content-Disposition: attachment; filename="' . $filename . '"';

		// overwrite the headers
		SpoonHTTP::setHeaders($headers);

		// output the CSV string
		echo $csv;

		// exit here
		exit;
	}

	/**
	 * Get all campaigns that have mailings assigned
	 *
	 * @return array
	 */
	public static function getActiveCampaigns()
	{
		return (array) BackendModel::getDB()->getRecords(
			'SELECT mc.*
			 FROM mailmotor_campaigns AS mc
			 INNER JOIN mailmotor_mailings AS mm ON mm.campaign_id = mc.id
			 GROUP BY mc.id'
		);
	}

	/**
	 * Get an e-mail address record
	 *
	 * @param string $email The emailaddress to get.
	 * @return array
	 */
	public static function getAddress($email)
	{
		$record = BackendModel::getDB()->getRecord(
			'SELECT ma.*
			 FROM mailmotor_addresses AS ma
			 WHERE ma.email = ?',
			array((string) $email)
		);

		// no record means we stop here
		if(empty($record)) return array();

		// fetch groups for this address
		$record['groups'] = (array) self::getGroupIDsByEmail($email);
		$record['custom_fields'] = array();

		// user is subscribed to groups
		if(!empty($record['groups']))
		{
			// reserve custom fields array
			$record['custom_fields'] = self::getCustomFieldsByAddress($email);
		}

		return $record;
	}

	/**
	 * Get all e-mail addresses
	 *
	 * @param int[optional] $limit Maximum number of addresses to get.
	 * @return array
	 */
	public static function getAddresses($limit = null)
	{
		$query =
			'SELECT ma.email, UNIX_TIMESTAMP(ma.created_on) AS created_on
			 FROM mailmotor_addresses AS ma
			 ORDER BY ma.created_on DESC';

		// set parameters
		$parameters = array();

		// check if a limit was set
		if(!empty($limit))
		{
			// add limit to query and parameters
			$query .= ' LIMIT ?';
			$parameters[] = $limit;
		}

		return (array) BackendModel::getDB()->getRecords($query, $parameters);
	}

	/**
	 * Get all e-mail addresses
	 *
	 * @return array
	 */
	public static function getAddressesAsPairs()
	{
		return BackendModel::getDB()->getColumn(
			'SELECT ma.email
			 FROM mailmotor_addresses AS ma'
		);
	}

	/**
	 * Get the e-mail addresses by group ID(s)
	 *
	 * @param array $ids The ids of the groups.
	 * @param bool[optional] $getColumn If this is true, the function returns a column of addresses instead.
	 * @param int[optional] $limit Maximum number if addresses to return.
	 * @return array
	 */
	public static function getAddressesByGroupID($ids, $getColumn = false, $limit = null)
	{
		if(empty($ids)) return array();

		// check if an array was given
		$ids = (array) $ids;

		// get DB
		$db = BackendModel::getDB();

		// build query
		$query =
			'SELECT ma.email, UNIX_TIMESTAMP(ma.created_on) AS created_on
			 FROM mailmotor_addresses AS ma
			 INNER JOIN mailmotor_addresses_groups AS mag ON mag.email = ma.email
			 INNER JOIN mailmotor_groups AS mg ON mg.id = mag.group_id
			 WHERE mag.status = ? AND mag.group_id IN (' . implode(',', $ids) . ')
			 GROUP BY ma.email';

		$parameters = array('subscribed');

		// limit was found
		if(!empty($limit))
		{
			$query .= ' LIMIT ?';
			$parameters[] = $limit;
		}

		// get record and return it
		if(!$getColumn) return (array) $db->getRecords($query, $parameters);

		// don't fetch a column of addresses
		return (array) $db->getColumn($query, $parameters);
	}

	/**
	 * Get all data for a given id
	 *
	 * @param int $id The id of the campaign to fetch.
	 * @return array
	 */
	public static function getCampaign($id)
	{
		return (array) BackendModel::getDB()->getRecord(
			'SELECT *
			 FROM mailmotor_campaigns
			 WHERE id = ?',
			array((int) $id)
		);
	}

	/**
	 * Get a campaign id by name
	 *
	 * @param string $name The name of the campaign.
	 * @return int
	 */
	public static function getCampaignID($name)
	{
		return (int) BackendModel::getDB()->getVar(
			'SELECT mc.id
			 FROM mailmotor_campaigns AS mc
			 WHERE mc.name = ?',
			array((string) $name)
		);
	}

	/**
	 * Get all campaign IDs
	 *
	 * @return array
	 */
	public static function getCampaignIDs()
	{
		return (array) BackendModel::getDB()->getColumn(
			'SELECT mc.id FROM mailmotor_campaigns AS mc'
		);
	}

	/**
	 * Get all campaigns
	 *
	 * @return array
	 */
	public static function getCampaigns()
	{
		return (array) BackendModel::getDB()->getRecords(
			'SELECT * FROM mailmotor_campaigns'
		);
	}

	/**
	 * Get all campaigns in key/value format for id/name
	 *
	 * @return int
	 */
	public static function getCampaignsAsPairs()
	{
		$record = BackendModel::getDB()->getPairs(
			'SELECT mc.id AS value, mc.name AS label
			 FROM mailmotor_campaigns AS mc'
		);

		// prepend an additional option
		array_unshift($record, SpoonFilter::ucfirst(BL::lbl('NoCampaign')));

		return $record;
	}

	/**
	 * Get all custom fields for a given group ID
	 *
	 * @param int $groupId The ID of the group.
	 * @return array
	 */
	public static function getCustomFields($groupId)
	{
		// get the group record
		$group = self::getGroup($groupId);

		// return the custom fields for this group
		return (array) $group['custom_fields'];
	}

	/**
	 * Get all custom fields and their values for a given e-mail address
	 *
	 * @param string $email The emailaddress to get the custom fields for.
	 * @return array
	 */
	public static function getCustomFieldsByAddress($email)
	{
		// email is not valid
		if(!SpoonFilter::isEmail($email)) throw new SpoonException('No valid e-mail given.');

		// fetch all group IDs
		$groupIds = self::getGroupIDs();

		// no groups found = stop here
		if(empty($groupIds)) return array();

		// fetch address group records
		$records = BackendModel::getDB()->getRecords(
			'SELECT mag.group_id, mag.custom_fields
			 FROM mailmotor_addresses_groups AS mag
			 WHERE mag.email = ? AND mag.group_id IN (' . implode(',', $groupIds) . ')',
			array($email), 'group_id'
		);

		// no records found = stop here
		if(empty($records)) return array();

		// loop the caught records and unserialize the fields
		foreach($records as $key => $record)
		{
			// unserialize the custom fields
			$records[$key] = unserialize($record['custom_fields']);
		}

		// return the fields
		return (array) $records;
	}

	/**
	 * Returns the default group ID
	 *
	 * @param string[optional] $language The language wherfor the default should be returned.
	 * @return int
	 */
	public static function getDefaultGroupID($language = null)
	{
		// filter input
		$language = empty($language) ? BL::getWorkingLanguage() : (string) $language;

		// return the group ID
		return (int) BackendModel::getDB()->getVar(
			'SELECT mg.id
			 FROM mailmotor_groups AS mg
			 WHERE mg.is_default = ? AND mg.language = ?
			 LIMIT 1',
			array('Y', $language)
		);
	}

	/**
	 * Returns the default group IDs
	 *
	 * @return array
	 */
	public static function getDefaultGroupIDs()
	{
		return (array) BackendModel::getDB()->getColumn(
			'SELECT mg.id
			 FROM mailmotor_groups AS mg
			 WHERE mg.is_default = ?',
			array('Y')
		);
	}

	/**
	 * Returns the default groups
	 *
	 * @return array
	 */
	public static function getDefaultGroups()
	{
		return (array) BackendModel::getDB()->getRecords(
			'SELECT mg.id, mg.language, mg.name, mg.created_on
			 FROM mailmotor_groups AS mg
			 WHERE mg.is_default = ?',
			array('Y'), 'language'
		);
	}

	/**
	 * Get all data for a given group
	 *
	 * @param int $id The id of the group to fetch.
	 * @return array
	 */
	public static function getGroup($id)
	{
		$record = (array) BackendModel::getDB()->getRecord(
			'SELECT mg.*, mci.cm_id
			 FROM mailmotor_groups AS mg
			 INNER JOIN mailmotor_campaignmonitor_ids AS mci ON mci.other_id = mg.id
			 WHERE mci.type = ? AND mg.id = ?',
			array('list', (int) $id)
		);

		// no record found
		if(empty($record)) return array();

		// unserialize the custom fields
		$record['custom_fields'] = ($record['custom_fields'] == null) ? array() : unserialize($record['custom_fields']);

		// return the record
		return (array) $record;
	}

	/**
	 * Get a group id by name
	 *
	 * @param string $name The name of the group.
	 * @return int
	 */
	public static function getGroupID($name)
	{
		return (int) BackendModel::getDB()->getVar(
			'SELECT mg.id
			 FROM mailmotor_groups AS mg
			 WHERE mg.name = ?',
			array((string) $name)
		);
	}

	/**
	 * Get all group IDs
	 *
	 * @return array
	 */
	public static function getGroupIDs()
	{
		return (array) BackendModel::getDB()->getColumn(
			'SELECT mg.id FROM mailmotor_groups AS mg'
		);
	}

	/**
	 * Get all groups for a given e-mail address
	 *
	 * @param string $email The emailaddress to get the groupID for.
	 * @return array
	 */
	public static function getGroupIDsByEmail($email)
	{
		return (array) BackendModel::getDB()->getColumn(
			'SELECT mg.id
			 FROM mailmotor_groups AS mg
			 LEFT OUTER JOIN mailmotor_addresses_groups AS mag ON mag.group_id = mg.id
			 WHERE mag.email = ? AND status = ?
			 GROUP BY mg.id',
			array($email, 'subscribed')
		);
	}

	/**
	 * Get all groups for a given mailing
	 *
	 * @param int $id The ID of the mailing.
	 * @return array
	 */
	public static function getGroupIDsByMailingID($id)
	{
		return (array) BackendModel::getDB()->getColumn(
			'SELECT mmg.group_id
			 FROM mailmotor_mailings AS mm
			 LEFT OUTER JOIN mailmotor_mailings_groups AS mmg ON mmg.mailing_id = mm.id
			 WHERE mmg.mailing_id = ?
			 GROUP BY mmg.group_id',
			array($id)
		);
	}

	/**
	 * Get all groups
	 *
	 * @return array
	 */
	public static function getGroups()
	{
		$records = (array) BackendModel::getDB()->getRecords(
			'SELECT mg.id, mg.name, mci.cm_id, mg.custom_fields
			 FROM mailmotor_groups AS mg
			 INNER JOIN mailmotor_campaignmonitor_ids AS mci ON mci.other_id = mg.id
			 WHERE mci.type = ?',
			array('list'), 'id'
		);

		// no records found
		if(empty($records)) return array();

		// loop the records
		foreach($records as &$record)
		{
			// unserialize the custom fields
			$record['custom_fields'] = ($record['custom_fields'] == null) ? array() : unserialize($record['custom_fields']);
		}

		// return the records
		return (array) $records;
	}

	/**
	 * Get all groups in key/value pairs
	 *
	 * @return array
	 */
	public static function getGroupsAsPairs()
	{
		return (array) BackendModel::getDB()->getPairs(
			'SELECT mg.id, mg.name FROM mailmotor_groups AS mg'
		);
	}

	/**
	 * Get all groups in key/value pairs
	 *
	 * @param string $email The emailaddress to get the groups for.
	 * @return array
	 */
	public static function getGroupsByEmailAsPairs($email)
	{
		return (array) BackendModel::getDB()->getPairs(
			'SELECT mg.id, mg.name
			 FROM mailmotor_groups AS mg
			 INNER JOIN mailmotor_addresses_groups AS mag ON mag.group_id = mg.id
			 WHERE mag.email = ? AND mag.status <> ?',
			array((string) $email, 'unsubscribed')
		);
	}

	/**
	 * Get all groups by their IDs
	 *
	 * @param array $ids The ids of the required groups.
	 * @return array
	 */
	public static function getGroupsByIds($ids)
	{
		// no ids set = stop here
		if(empty($ids)) return false;

		// check if an array was given
		$ids = (array) $ids;

		return (array) BackendModel::getDB()->getRecords(
			'SELECT mg.id, mg.name, mci.cm_id
			 FROM mailmotor_groups AS mg
			 INNER JOIN mailmotor_campaignmonitor_ids AS mci ON mci.other_id = mg.id
			 WHERE mci.type = ? AND mg.id IN (' . implode(',', $ids) . ')',
			array('list'), 'id'
		);
	}

	/**
	 * Get all groups in a format acceptable for SpoonForm::addRadioButton() and SpoonForm::addMultiCheckbox()
	 *
	 * @return array
	 */
	public static function getGroupsForCheckboxes()
	{
		return (array) BackendModel::getDB()->getRecords(
			'SELECT mg.id AS value, mg.name AS label
			 FROM mailmotor_groups AS mg
			 GROUP BY mg.id'
		);
	}

	/**
	 * Get all groups with recipients in a format acceptable for SpoonForm::addRadioButton() and SpoonForm::addMultiCheckbox()
	 *
	 * @return array
	 */
	public static function getGroupsWithRecipientsForCheckboxes()
	{
		$records = (array) BackendModel::getDB()->getRecords(
			'SELECT
			 	mg.id AS value, mg.name AS label, COUNT(mag.email) AS recipients
			 FROM mailmotor_groups AS mg
			 LEFT OUTER JOIN mailmotor_addresses_groups AS mag ON mag.group_id = mg.id
			 WHERE status = ?
			 GROUP BY mg.id',
			array('subscribed')
		);

		// no records found
		if(empty($records)) return array();

		foreach($records as &$record)
		{
			// store variables array
			$record['variables'] = array(
				'recipients' => ($record['recipients'] != 0) ? $record['recipients'] : false,
				'single' => ($record['recipients'] == 1) ? true : false
			);

			// unset the recipients from this stack
			unset($record['recipients']);
		}

		return $records;
	}

	/**
	 * Get all active languages in a format acceptable for SpoonForm::addRadioButton() and SpoonForm::addMultiCheckbox()
	 *
	 * @return array
	 */
	public static function getLanguagesForCheckboxes()
	{
		// get the active languages
		$languages = BL::getActiveLanguages();

		// no languages found
		if(empty($languages)) return array();

		// init results
		$results = array();

		// loop the languages
		foreach($languages as $abbreviation)
		{
			// build new value
			$results[] = array('value' => $abbreviation, 'label' => BL::lbl(strtoupper($abbreviation)));
		}

		return $results;
	}

	/**
	 * Get all data for a given mailing
	 *
	 * @param int $id The id of the mailing.
	 * @return array
	 */
	public static function getMailing($id)
	{
		// get record and return it
		$record = (array) BackendModel::getDB()->getRecord(
			'SELECT mm.*, UNIX_TIMESTAMP(mm.send_on) AS send_on
			 FROM mailmotor_mailings AS mm
			 WHERE mm.id = ?',
			array((int) $id)
		);

		// stop here if record is empty
		if(empty($record)) return array();

		// get groups for this mailing ID
		$record['groups'] = self::getGroupIDsByMailingID($id);
		$record['recipients'] = self::getAddressesByGroupID($record['groups']);

		// fetch CM id for this mailing
		$record['cm_id'] = BackendMailmotorCMHelper::getCampaignMonitorID('campaign', $record['id']);

		// return the record
		return $record;
	}

	/**
	 * Get all mailing IDs
	 *
	 * @return array
	 */
	public static function getMailingIDs()
	{
		return (array) BackendModel::getDB()->getColumn(
			'SELECT mm.id
			 FROM mailmotor_mailings AS mm
			 WHERE mm.language = ?',
			array(BL::getWorkingLanguage())
		);
	}

	/**
	 * Get a preview URL to the specific mailing
	 *
	 * @param int $id The id of the mailing.
	 * @param string[optional] $contentType The content-type, possible values are: html, plain.
	 * @param bool[optional] $forCM Is the URL intended for Campaign Monitor.
	 * @return string
	 */
	public static function getMailingPreviewURL($id, $contentType = 'html', $forCM = false)
	{
		$contentType = SpoonFilter::getValue($contentType, array('html', 'plain'), 'html');
		$forCM = SpoonFilter::getValue($forCM, array(false, true), false, 'int');

		// return the URL
		return SITE_URL . BackendModel::getURLForBlock('mailmotor', 'detail') . '/' . $id . '?type=' . $contentType . '&cm=' . $forCM;
	}

	/**
	 * Get the maximum id for mailings
	 *
	 * @return int
	 */
	public static function getMaximumId()
	{
		return (int) BackendModel::getDB()->getVar(
			'SELECT MAX(id) FROM mailmotor_mailings LIMIT 1'
		);
	}

	/**
	 * Get the maximum id for groups
	 *
	 * @return int
	 */
	public static function getMaximumIdForGroups()
	{
		return (int) BackendModel::getDB()->getVar(
			'SELECT MAX(id) FROM mailmotor_groups LIMIT 1'
		);
	}

	/**
	 * Get all sent mailings
	 *
	 * @param int[optional] $limit The maximum number of items to retrieve.
	 * @return array
	 */
	public static function getSentMailings($limit = null)
	{
		$query = BackendMailmotorModel::QRY_DATAGRID_BROWSE_SENT . ' ORDER BY send_on DESC';
		$parameters = array('sent');

		if(!empty($limit))
		{
			$query .= ' LIMIT ?';
			$parameters[] = $limit;
		}

		return (array) BackendModel::getDB()->getRecords($query, $parameters);
	}

	/**
	 * Get all subscriptions for a given e-mail address
	 *
	 * @param string $email The emailaddress to get the subscriptions for.
	 * @return array
	 */
	public static function getSubscriptions($email)
	{
		return (array) BackendModel::getDB()->getRecords(
			'SELECT mag.*
			 FROM mailmotor_addresses_groups AS mag
			 WHERE mag.email = ?',
			array((string) $email), 'group_id'
		);
	}

	/**
	 * Get the template record
	 *
	 * @param string $language The language.
	 * @param string $name The name of the template.
	 * @return array
	 */
	public static function getTemplate($language, $name)
	{
		// set the path to the template folders for this language
		$path = BACKEND_MODULE_PATH . '/templates/' . $language;

		// load all templates in the 'templates' folder for this language
		$templates = SpoonDirectory::getList($path, false, array('.svn'));

		// stop here if no directories were found
		if(empty($templates) || !in_array($name, $templates)) return array();

		// load all templates in the 'templates' folder for this language
		if(!SpoonFile::exists($path . '/' . $name . '/template.tpl')) throw new SpoonException('The template folder "' . $name . '" exists, but no template.tpl file was found. Please create one.');
		if(!SpoonFile::exists($path . '/' . $name . '/css/screen.css')) throw new SpoonException('The template folder "' . $name . '" exists, but no screen.css file was found. Please create one in a subfolder "css".');

		// set template data
		$record = array();
		$record['name'] = $name;
		$record['language'] = $language;
		$record['label'] = BL::lbl('Template' . SpoonFilter::toCamelCase($record, array('-', '_')));
		$record['path_content'] = $path . '/' . $name . '/template.tpl';
		$record['path_css'] = $path . '/' . $name . '/css/screen.css';
		$record['url_css'] = SITE_URL . '/backend/modules/mailmotor/templates/' . $language . '/' . $name . '/css/screen.css';

		// check if the template file actually exists
		if(SpoonFile::exists($record['path_content'])) $record['content'] = SpoonFile::getContent($record['path_content']);
		if(SpoonFile::exists($record['path_css'])) $record['css'] = SpoonFile::getContent($record['path_css']);

		return $record;
	}

	/**
	 * Get all data for templates in a format acceptable for SpoonForm::addRadioButton() and SpoonForm::addMultiCheckbox()
	 *
	 * @param string $language The language.
	 * @return array
	 */
	public static function getTemplatesForCheckboxes($language)
	{
		// load all templates in the 'templates' folder for this language
		$records = SpoonDirectory::getList(BACKEND_MODULE_PATH . '/templates/' . $language . '/', false, array('.svn'));

		// stop here if no directories were found
		if(empty($records)) return array();

		// loop and complete the records
		foreach($records as $key => $record)
		{
			// add additional values
			$records[$record]['language'] = $language;
			$records[$record]['value'] = $record;
			$records[$record]['label'] = BL::lbl('Template' . SpoonFilter::toCamelCase($record, array('-', '_')));

			// unset the key
			unset($records[$key]);
		}

		return (array) $records;
	}

	/**
	 * Get the unsubscribed e-mail addresses by group ID(s)
	 *
	 * @param mixed $ids The ids of the groups.
	 * @return array
	 */
	public static function getUnsubscribedAddressesByGroupID($ids)
	{
		// check input
		if(empty($ids)) return array();

		// check if an array was given
		$ids = (array) $ids;

		// get record and return it
		return (array) BackendModel::getDB()->getRecords(
			'SELECT ma.email, UNIX_TIMESTAMP(ma.created_on) AS created_on
			 FROM mailmotor_addresses AS ma
			 INNER JOIN mailmotor_addresses_groups AS mag ON mag.email = ma.email
			 INNER JOIN mailmotor_groups AS mg ON mg.id = mag.group_id
			 WHERE mag.group_id IN (' . implode(',', $ids) . ') AND mag.status = ?
			 GROUP BY ma.email',
			array('unsubscribed')
		);
	}

	/**
	 * Inserts a new e-mail address into the database
	 *
	 * @param array $item The data to insert for the address.
	 */
	public static function insertAddress(array $item)
	{
		// get DB
		$db = BackendModel::getDB(true);

		// set record values
		$record = array();
		$record['email'] = $item['email'];
		$record['source'] = $item['source'];
		$record['created_on'] = $item['created_on'];

		// insert record
		$db->insert('mailmotor_addresses', $record);

		// no groups = stop here
		if(empty($item['groups'])) return;

		// check if groups was an array or not
		$item['groups'] = (array) $item['groups'];

		// insert record(s)
		foreach($item['groups'] as $id)
		{
			// set variables
			$variables = array();
			$variables['group_id'] = $id;
			$variables['status'] = 'subscribed';
			$variables['subscribed_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');
			$variables['email'] = $item['email'];

			// insert the record
			$db->insert('mailmotor_addresses_groups', $variables);
		}
	}

	/**
	 * Inserts a new campaign into the database
	 *
	 * @param array $item The data to insert for the campaign.
	 * @return int
	 */
	public static function insertCampaign(array $item)
	{
		return (int) BackendModel::getDB(true)->insert('mailmotor_campaigns', $item);
	}

	/**
	 * Inserts the custom fields for a given group. Accepts an optional third parameter $email that will insert the values for that e-mail.
	 *
	 * @param array $fields The fields to insert.
	 * @param int $groupId The ID of the group for which the fields will be inserted.
	 * @param string[optional] $email The email you want to insert the custom fields for.
	 * @param int[optional] $customFieldsGroup If this is set it will only update the custom fields for this group.
	 * @param bool[optional] $import This method is called through the import action.
	 * @return bool
	 */
	public static function insertCustomFields(array $fields, $groupId, $email = null, $customFieldsGroup = null, $import = false)
	{
		$db = BackendModel::getDB(true);

		// no fields given
		if(empty($fields)) return false;

		// no email address set means we just update the custom fields (ie adding new ones)
		if(!empty($email) && SpoonFilter::isEmail($email))
		{
			// set custom fields values
			$subscription['email'] = $email;
			$subscription['custom_fields'] = serialize($fields);
			$subscription['group_id'] = $groupId;

			// insert/update the user
			$db->execute(
				'INSERT INTO mailmotor_addresses_groups(email, custom_fields, group_id, status, subscribed_on)
				 VALUES (?, ?, ?, ?, ?)
				 ON DUPLICATE KEY UPDATE custom_fields = ?',
				array(
					$subscription['email'],
					$subscription['custom_fields'],
					$subscription['group_id'],
					'subscribed',
					BackendModel::getUTCDate('Y-m-d H:i:s'),
					$subscription['custom_fields']
				)
			);
		}

		// if this is called through the import action OR the given group equals the current ID, we continue
		if($customFieldsGroup == $groupId || $import == true)
		{
			// fetch array keys if $fields isn't a boolean
			if($fields !== false) $fields = array_keys($fields);

			// overwrite custom fields so we only have the keys
			$values['custom_fields'] = serialize($fields);

			// update the field values for this e-mail address
			return (bool) $db->update('mailmotor_groups', $values, 'id = ?', $groupId);
		}
	}

	/**
	 * Inserts a new group into the database
	 *
	 * @param array $item The data to insert for the group.
	 * @return int
	 */
	public static function insertGroup(array $item)
	{
		// get DB
		$db = BackendModel::getDB(true);

		// check if there is a default group set for this language
		// @todo refactor, this looks like shite
		if(!(bool) $db->getVar('SELECT COUNT(mg.id)
								FROM mailmotor_groups AS mg
								WHERE mg.is_default = ? AND mg.language = ?',
								array('Y', BL::getWorkingLanguage())))
		{
			// this list will be a default list
			$item['language'] = BL::getWorkingLanguage();
			$item['is_default'] = 'Y';
		}

		return (int) $db->insert('mailmotor_groups', $item);
	}

	/**
	 * Inserts a new mailing into the database
	 *
	 * @param array $item The data to insert for the mailing.
	 * @return int
	 */
	public static function insertMailing(array $item)
	{
		return (int) BackendModel::getDB(true)->insert('mailmotor_mailings', $item);
	}

	/**
	 * Inserts a new subscription into the database
	 *
	 * @param array $item The data to insert for the address.
	 * @param array[optional] $fields The custom field values for this user.
	 * @return bool
	 */
	public static function insertSubscription(array $item, array $fields = null)
	{
		// get DB
		$db = BackendModel::getDB(true);

		// if groups are empty, add the user to the default group for this working language
		if(empty($item['groups'])) return array();

		// insert record(s)
		foreach($item['groups'] as $id)
		{
			// set variables
			$variables = array();
			$variables['group_id'] = $id;
			$variables['status'] = 'subscribed';
			$variables['subscribed_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');
			$variables['email'] = $item['email'];
			$variables['custom_fields'] = serialize($fields);

			// insert the record
			$db->insert('mailmotor_addresses_groups', $variables);
		}

		return true;
	}

	/**
	 * Checks if a given e-mail address is subscribed in our database
	 *
	 * @param string $email The emailaddress to check.
	 * @param int[optional] $groupId The id of the group.
	 * @return bool
	 */
	public static function isSubscribed($email, $groupId = null)
	{
		$groupId = (int) (empty($groupId) ? self::getDefaultGroupID() : $groupId);

		return (bool) BackendModel::getDB()->getVar(
			'SELECT COUNT(ma.email)
			 FROM mailmotor_addresses AS ma
			 INNER JOIN mailmotor_addresses_groups AS mag ON mag.email = ma.email
			 WHERE ma.email = ? AND mag.group_id = ? AND mag.status = ?',
			array((string) $email, $groupId, 'subscribed')
		);
	}

	/**
	 * Inserts or updates a subscriber record.
	 *
	 * @param array $item The data to update for the e-mail address.
	 * @param int $groupId The group to subscribe the address to.
	 * @param array[optional] $fields The custom fields for the address in the given group.
	 * @return bool
	 */
	public static function saveAddress(array $item, $groupId, $fields = array())
	{
		$db = BackendModel::getDB(true);

		// set record values
		$record['email'] = $item['email'];
		$record['source'] = $item['source'];
		$record['created_on'] = $item['created_on'];

		// insert/update the user
		$db->execute('INSERT INTO mailmotor_addresses(email, source, created_on)
						VALUES (?, ?, ?)
						ON DUPLICATE KEY UPDATE email = ?',
						array($record['email'], $record['source'], $record['created_on'],
								$record['email']));

		// set values
		$subscription['email'] = $item['email'];
		$subscription['custom_fields'] = serialize($fields);
		$subscription['group_id'] = $groupId;

		// insert/update the user
		$db->execute(
			'INSERT INTO mailmotor_addresses_groups(email, custom_fields, group_id, status, subscribed_on)
			 VALUES (?, ?, ?, ?, ?)
			 ON DUPLICATE KEY UPDATE custom_fields = ?',
			array(
				$subscription['email'],
				$subscription['custom_fields'],
				$subscription['group_id'],
				'subscribed',
				BackendModel::getUTCDate(),
				$subscription['custom_fields']
			)
		);
	}

	/**
	 * Updates a campaign
	 *
	 * @param array $item The data to update for the campaign.
	 * @return int
	 */
	public static function updateCampaign(array $item)
	{
		return BackendModel::getDB(true)->update('mailmotor_campaigns', $item, 'id = ?', array($item['id']));
	}

	/**
	 * Updates the custom fields for a given group. Accepts an optional third parameter $email that will update the values for that e-mail.
	 *
	 * @param array $fields The fields.
	 * @param int $groupId The group to update.
	 * @param string[optional] $email The email you want to update the custom fields for.
	 * @return int
	 */
	public static function updateCustomFields($fields, $groupId, $email = null)
	{
		// get DB
		$db = BackendModel::getDB(true);

		// set values to update
		$values = array();

		// no email address set means we just update the custom fields (ie adding new ones)
		if(!empty($email) && SpoonFilter::isEmail($email))
		{
			// set custom fields values
			$values['custom_fields'] = serialize($fields);

			// update field values for this email
			$db->update('mailmotor_addresses_groups', $values, 'email = ? AND group_id = ?', array($email, (int) $groupId));
		}

		// fetch array keys if $fields isn't a boolean
		if($fields !== false && !isset($fields[0])) $fields = array_keys($fields);

		// overwrite custom fields so we only have the keys
		$values['custom_fields'] = serialize($fields);

		// update the field values for this e-mail address
		return (int) $db->update('mailmotor_groups', $values, 'id = ?', array((int) $groupId));
	}

	/**
	 * Updates a group
	 *
	 * @param array $item The data to update for the group.
	 * @return int
	 */
	public static function updateGroup(array $item)
	{
		return BackendModel::getDB(true)->update('mailmotor_groups', $item, 'id = ?', array($item['id']));
	}

	/**
	 * Updates the groups for a given email address
	 *
	 * @param string $email The emailaddress to update.
	 * @param mixed $groupIds The ids of the groups.
	 */
	public static function updateGroups($email, $groupIds)
	{
		$db = BackendModel::getDB(true);

		// stop here if groups are empty
		if(empty($groupIds)) return false;

		// check if $groupIds is an array or not, make it one if it isn't
		$groupIds = (array) $groupIds;

		// insert record(s)
		foreach($groupIds as $id)
		{
			// set variables
			$variables = array();
			$variables['email'] = $email;
			$variables['group_id'] = $id;
			$variables['status'] = 'subscribed';
			$variables['subscribed_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');

			// update
			$db->insert('mailmotor_addresses_groups', $variables);
		}
	}

	/**
	 * Updates the groups for a given mailing
	 *
	 * @param int $mailingId The id of the mailing.
	 * @param array $groupIds A list of group-ids.
	 */
	public static function updateGroupsForMailing($mailingId, $groupIds)
	{
		$db = BackendModel::getDB(true);

		// delete all groups for this mailing
		$db->delete('mailmotor_mailings_groups', 'mailing_id = ?', array((int) $mailingId));

		// stop here if groups are empty
		if(empty($groupIds)) return false;

		// insert record(s)
		foreach($groupIds as $id)
		{
			// set variables
			$variables = array();
			$variables['mailing_id'] = (int) $mailingId;
			$variables['group_id'] = (int) $id;

			// update
			$db->insert('mailmotor_mailings_groups', $variables);
		}
	}

	/**
	 * Updates a mailing
	 *
	 * @param array $item The data to update for the mailing.
	 * @return int
	 */
	public static function updateMailing(array $item)
	{
		return BackendModel::getDB(true)->update('mailmotor_mailings', $item, 'id = ?', array($item['id']));
	}

	/**
	 * Updates the queued mailings with 'sent' status if they were sent
	 *
	 * @return mixed
	 */
	public static function updateQueuedMailings()
	{
		$db = BackendModel::getDB(true);

		// fetch all mailings that aren't sent
		$records = $db->getRecords(self::QRY_DATAGRID_BROWSE_SENT, array('queued'));

		// no records found, so stop here
		if(empty($records)) return false;

		// reserve update stack
		$updateIds = array();

		// loop the records
		foreach($records as $record)
		{
			// if the sent date is smaller than the current date, update status to 'sent'
			if(date('Y-m-d H:i:s', $record['sent']) < date('Y-m-d H:i:s')) $updateIds[] = $record['id'];
		}

		// if don't need to update any record, stop here
		if(empty($updateIds)) return false;

		// update all mailings that are queued and were sent
		return (int) $db->update('mailmotor_mailings', array('status' => 'sent'), 'id IN (' . implode(',', $updateIds) . ')');
	}
}
