<?php

/**
 * FrontendMailmotorModel
 * In this file we store all generic functions that we will be using in the mailmotor module
 *
 * @package		frontend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class FrontendMailmotorModel
{
	const QRY_DATAGRID_BROWSE_SENT = 'SELECT
										mm.id,
										mm.name,
										UNIX_TIMESTAMP(mm.send_on) AS send_on,
										mm.status
										FROM mailmotor_mailings AS mm
										LEFT OUTER JOIN mailmotor_campaigns AS mc ON mc.id = mm.campaign_id
										WHERE mm.status = ? AND mm.language = ?';


	/**
	 * Deletes one or more e-mail addresses
	 *
	 * @return	void
	 * @param 	mixed $emails	The emails to delete.
	 */
	public static function deleteAddresses($emails)
	{
		// get DB
		$db = FrontendModel::getDB(true);

		// if $ids is not an array, make one
		$emails = (!is_array($emails)) ? array($emails) : $emails;

		// delete records
		$db->delete('mailmotor_addresses', 'email IN("'. implode('","', $emails) .'");');
		$db->delete('mailmotor_addresses_groups', 'email IN("'. implode('","', $emails) .'");');
	}


	/**
	 * Checks if a given e-mail address exists in the mailmotor_addresses table
	 *
	 * @return	bool
	 * @param	string $email
	 */
	public static function exists($email)
	{
		// check the results
		return (bool) FrontendModel::getDB()->getNumRows('SELECT email FROM mailmotor_addresses WHERE email = ?;', array($email));
	}


	/**
	 * Checks if a group exists
	 *
	 * @return	bool
	 * @param	int $group
	 */
	public static function existsGroup($id)
	{
		// return the results
		return (bool) (FrontendModel::getDB()->getNumRows('SELECT id FROM mailmotor_groups WHERE id = ?;', array($id)) > 0);
	}


	/**
	 * Get all data for a given mailing
	 *
	 * @return	array
	 * @param	int $id		The id of the mailing.
	 */
	public static function get($id)
	{
		// get record and return it
		$record = (array) FrontendModel::getDB()->getRecord('SELECT mm.*
																FROM mailmotor_mailings AS mm
																WHERE mm.id = ?;',
																array((int) $id));

		// record is empty, stop here
		if(empty($record)) return array();

		// unserialize data field
		$record['data'] = unserialize($record['data']);

		// return the record
		return $record;
	}


	/**
	 * Returns the client ID from the settings
	 *
	 * @return	string
	 */
	public static function getClientID()
	{
		return (string) FrontendModel::getModuleSetting('mailmotor', 'cm_client_id');
	}


	/**
	 * Returns the default group ID
	 *
	 * @return	int
	 */
	public static function getDefaultGroupID()
	{
		return (int) FrontendModel::getDB()->getVar('SELECT mg.id
														FROM mailmotor_groups AS mg
														WHERE mg.is_default = ? AND mg.language = ?
														LIMIT 1;',
														array('Y', FRONTEND_LANGUAGE));
	}


	/**
	 * Get all groups for a given e-mail address
	 *
	 * @return	array
	 * @param	string $email
	 * @param	int	$excludeId
	 */
	public static function getGroupIDsByEmail($email, $excludeId = null)
	{
		// get DB
		$db = FrontendModel::getDB();

		// return records
		$records = (array) $db->getColumn('SELECT mg.id
											FROM mailmotor_groups AS mg
											LEFT OUTER JOIN mailmotor_addresses_groups AS mag ON mag.group_id = mg.id
											WHERE mag.email = ?
											GROUP BY mg.id;',
											array($email));

		// excludeId set
		if(!empty($excludeId))
		{
			// check for the exclude ID key
			$key = array_search($excludeId, $records);

			// unset this value from the records
			unset($records[$key]);
		}

		// return the records
		return $records;
	}


	/**
	 * Get a preview URL to the specific mailing
	 *
	 * @return	string
	 * @param	int $id
	 * @param	string[optional] $contentType
	 */
	public static function getMailingPreviewURL($id, $contentType = 'html', $forCM = false)
	{
		// check input
		$contentType = SpoonFilter::getValue($contentType, array('html', 'plain'), 'html');
		$forCM = SpoonFilter::getValue($forCM, array(false, true), false, 'int');

		// return the URL
		return SITE_URL . FrontendNavigation::getURLForBlock('mailmotor', 'detail') .'/'. $id .'?type='. $contentType . (($forCM == 1) ? '&cm='. $forCM : '');
	}


	/**
	 * Inserts a new e-mail address into the database
	 *
	 * @return	bool
	 * @param	array $item		The data to insert for the address.
	 */
	public static function insertAddress(array $item, $unsubscribe = false)
	{
		// get DB
		$db = FrontendModel::getDB(true);

		// set record values
		$record = array();
		$record['email'] = $item['email'];
		$record['source'] = $item['source'];
		$record['created_on'] = $item['created_on'];

		// insert record
		$db->insert('mailmotor_addresses', $record);

		// if groups are empty, add the user to the default group for this working language
		if(empty($item['groups']) && !$unsubscribe) $item['groups'][] = self::getDefaultGroupID();

		// return true;
		if(empty($item['groups'])) return true;

		// insert record(s)
		foreach($item['groups'] as $id)
		{
			// set variables
			$variables = array();
			$variables['group_id'] = $id;
			$variables['status'] = 'subscribed';
			$variables['subscribed_on'] = FrontendModel::getUTCDate('Y-m-d H:i:s');
			$variables['email'] = $item['email'];

			// insert the record
			$db->insert('mailmotor_addresses_groups', $variables);
		}

		// return true
		return true;
	}


	/**
	 * Checks if a given e-mail address is subscribed in our database
	 *
	 * @return	bool
	 * @param	string $email
	 * @param	int[optional] $groupId
	 */
	public static function isSubscribed($email, $groupId = null)
	{
		// get DB
		$db = FrontendModel::getDB();

		// no group ID set
		$groupId = (int) (empty($groupId) ? self::getDefaultGroupID() : $groupId);

		// check the results
		return (bool) $db->getNumRows('SELECT ma.email
										FROM mailmotor_addresses AS ma
										INNER JOIN mailmotor_addresses_groups AS mag ON mag.email = ma.email
										WHERE ma.email = ? AND mag.group_id = ? AND mag.status = ?;',
										array((string) $email, $groupId, 'subscribed'));
	}


	/**
	 * Subscribes an e-mail address
	 *
	 * @return	bool
	 * @param	string $email
	 * @param	string[optional] $groupId
	 */
	public static function subscribe($email, $groupId = null)
	{
		// get objects
		$db = FrontendModel::getDB(true);

		// set groupID
		$groupId = !empty($groupId) ? $groupId : self::getDefaultGroupID();

		// subscribe the user in CM
		if(self::existsGroup($groupId))
		{
			// set variables
			$subscriber['email'] = $email;
			$subscriber['source'] = 'website';
			$subscriber['created_on'] = FrontendModel::getUTCDate('Y-m-d H:i:s');

			// insert/update the user
			$db->execute('INSERT INTO mailmotor_addresses(email, source, created_on)
							VALUES (?, ?, ?)
							ON DUPLICATE KEY UPDATE source = ?, created_on = ?;',
							array($subscriber['email'], $subscriber['source'], $subscriber['created_on'],
									$subscriber['source'], $subscriber['created_on']));

			// set variables
			$subscriberGroup['email'] = $email;
			$subscriberGroup['group_id'] = $groupId;
			$subscriberGroup['status'] = 'subscribed';
			$subscriberGroup['subscribed_on'] = FrontendModel::getUTCDate('Y-m-d H:i:s');

			// insert/update the user
			$db->execute('INSERT INTO mailmotor_addresses_groups(email, group_id, status, subscribed_on)
							VALUES (?, ?, ?, ?)
							ON DUPLICATE KEY UPDATE group_id = ?, status = ?, subscribed_on = ?;',
							array($subscriberGroup['email'], $subscriberGroup['group_id'], $subscriberGroup['status'], $subscriberGroup['subscribed_on'],
									$subscriberGroup['group_id'], $subscriberGroup['status'], $subscriberGroup['subscribed_on']));

			// user subscribed
			return true;
		}

		// user not subscribed
		return false;
	}


	/**
	 * Unsubscribes an e-mail address
	 *
	 * @return	bool
	 * @param	string $email
	 * @param	string[optional] $groupId
	 */
	public static function unsubscribe($email, $groupId = null)
	{
		// get objects
		$db = FrontendModel::getDB(true);

		// set groupID
		$groupId = !empty($groupId) ? $groupId : self::getDefaultGroupID();

		// unsubscribe the user in CM
		if(self::existsGroup($groupId))
		{
			// set variables
			$subscriber['status'] = 'unsubscribed';
			$subscriber['unsubscribed_on'] = FrontendModel::getUTCDate('Y-m-d H:i:s');

			// unsubscribe the user
			$db->update('mailmotor_addresses_groups', $subscriber, 'email = ?', $email);

			// user unsubscribed
			return true;
		}

		// user not unsubscribed
		return false;
	}
}

?>