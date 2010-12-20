<?php

/**
 * FrontendMailmotorCMHelper
 * In this file we store all generic functions that we will be using to communicate with CampaignMonitor
 *
 * @package		frontend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class FrontendMailmotorCMHelper
{
	/**
	 * Returns the CampaignMonitor object
	 *
	 * @return	CampaignMonitor
	 * @param	int[optional] $listId
	 */
	public static function getCM($listId = null)
	{
		// campaignmonitor reference exists
		if(!Spoon::isObjectReference('campaignmonitor'))
		{
			// check if the CampaignMonitor class exists
			if(!SpoonFile::exists(PATH_LIBRARY .'/external/campaignmonitor.php'))
			{
				// the class doesn't exist, so throw an exception
				throw new SpoonFileException('The CampaignMonitor wrapper class is not found. Please locate and place it in /library/external');
			}

			// require CampaignMonitor class
			require_once 'external/campaignmonitor.php';

			// set login data
			$url = FrontendModel::getModuleSetting('mailmotor', 'cm_url');
			$username = FrontendModel::getModuleSetting('mailmotor', 'cm_username');
			$password = FrontendModel::getModuleSetting('mailmotor', 'cm_password');

			// init CampaignMonitor object
			$cm = new CampaignMonitor($url, $username, $password, 5, self::getClientId());

			// set CampaignMonitor object reference
			Spoon::setObjectReference('campaignmonitor', $cm);

			// get the default list ID
			$listId = (!empty($listId)) ? $listId : self::getDefaultListID();

			// set the default list ID
			$cm->setListId($listId);
		}

		// return the CampaignMonitor object
		return Spoon::getObjectReference('campaignmonitor');
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
	 * Checks if a group exists by its CampaignMonitor ID
	 *
	 * @return	bool
	 * @param	string $id
	 */
	public static function existsGroupByCampaignMonitorID($id)
	{
		// get DB
		$db = FrontendModel::getDB();

		// return the results
		return (bool) $db->getNumRows('SELECT mg.*
										FROM mailmotor_groups AS mg
										INNER JOIN mailmotor_campaignmonitor_ids AS mci ON mci.other_id = mg.id
										WHERE mci.cm_id = ? AND mci.type = ?;', array($id, 'list'));
	}


	/**
	 * Inserts a record into the mailmotor_campaignmonitor_ids table
	 *
	 * @return	string
	 * @param	string $type
	 * @param	string $otherId
	 */
	public static function getCampaignMonitorID($type, $otherId)
	{
		// insert the campaignmonitor ID
		return FrontendModel::getDB()->getVar('SELECT cm_id FROM mailmotor_campaignmonitor_ids WHERE type = ? AND other_id = ?;', array($type, $otherId));
	}


	/**
	 * Returns the CM IDs for a given list of group IDs
	 *
	 * @return	array
	 * @param 	array $groupIds
	 */
	public static function getCampaignMonitorIDsForGroups($groupIds, $unsubscribe = false)
	{
		// check if groups are set,
		$groups = (empty($groupIds) && !$unsubscribe) ? array(FrontendMailmotorModel::getDefaultGroupID()) : $groupIds;

		// stop here if no groups were set
		if(empty($groups)) return array();

		// fetch campaignmonitor IDs
		return (array) FrontendModel::getDB()->getColumn('SELECT mci.cm_id
															FROM mailmotor_campaignmonitor_ids AS mci
															WHERE mci.type = ? AND mci.other_id IN ('. implode(',', $groups) .');',
															array('list'));
	}


	/**
	 * Returns the default list ID
	 *
	 * @return	string
	 */
	public static function getDefaultListID()
	{
		// fetch default group ID
		$groupId = FrontendMailmotorModel::getDefaultGroupID();

		// fetch the CM ID for this group
		return self::getCampaignMonitorID('list', $groupId);
	}


	/**
	 * Subscribes an e-mail address and send him/her to CampaignMonitor
	 *
	 * @return	bool
	 * @param	string $email
	 * @param	string[optional] $groupId
	 */
	public static function subscribe($email, $groupId = null)
	{
		// get objects
		$db = FrontendModel::getDB(true);
		$cm = self::getCM();

		// set groupID
		$groupId = !empty($groupId) ? $groupId : FrontendMailmotorModel::getDefaultGroupID();

		// group ID found
		if(FrontendMailmotorModel::existsGroup($groupId) && $cm->subscribe($email, $email))
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
	 * Unsubscribes an e-mail address from CampaignMonitor and our database
	 *
	 * @return	bool
	 * @param	string $email
	 * @param	string[optional] $groupId
	 */
	public static function unsubscribe($email, $groupId = null)
	{
		// get objects
		$db = FrontendModel::getDB(true);
		$cm = self::getCM();

		// set group ID
		$groupId = !empty($groupId) ? $groupId : FrontendMailmotorModel::getDefaultGroupID();

		// get group CM ID
		$groupCMId = self::getCampaignMonitorID('list', $groupId);

		// group exists
		if(FrontendMailmotorModel::existsGroup($groupId))
		{
			try
			{
				// unsubscribe the email from this group
				self::getCM()->unsubscribe($email, $groupCMId);
			}

			// for the unsubscribe function we ignore any errors
			catch(Exception $e)
			{
				// stop here if something went wrong with CM
				return false;
			}

			// set variables
			$subscriber = array();
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