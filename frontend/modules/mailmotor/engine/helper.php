<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using to communicate with CampaignMonitor
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class FrontendMailmotorCMHelper
{
	/**
	 * Checks if a group exists by its CampaignMonitor ID
	 *
	 * @param string $id The id of the group on Campaign Monitor.
	 * @return bool
	 */
	public static function existsGroupByCampaignMonitorID($id)
	{
		return (bool) FrontendModel::getDB()->getVar(
			'SELECT COUNT(mg.id)
			 FROM mailmotor_groups AS mg
			 INNER JOIN mailmotor_campaignmonitor_ids AS mci ON mci.other_id = mg.id
			 WHERE mci.cm_id = ? AND mci.type = ?',
			array($id, 'list')
		);
	}

	/**
	 * Inserts a record into the mailmotor_campaignmonitor_ids table
	 *
	 * @param string $type The type for the item.
	 * @param string $otherId The id of the item.
	 * @return string
	 */
	public static function getCampaignMonitorID($type, $otherId)
	{
		return FrontendModel::getDB()->getVar(
			'SELECT cm_id
			 FROM mailmotor_campaignmonitor_ids
			 WHERE type = ? AND other_id = ?',
			array($type, $otherId)
		);
	}

	/**
	 * Returns the client ID from the settings
	 *
	 * @return string
	 */
	public static function getClientID()
	{
		return (string) FrontendModel::getModuleSetting('mailmotor', 'cm_client_id');
	}

	/**
	 * Returns the CampaignMonitor object
	 *
	 * @param int[optional] $listId The default list id to use.
	 * @return CampaignMonitor
	 */
	public static function getCM($listId = null)
	{
		// campaignmonitor reference exists
		if(!Spoon::exists('campaignmonitor'))
		{
			// check if the CampaignMonitor class exists
			if(!SpoonFile::exists(PATH_LIBRARY . '/external/campaignmonitor.php'))
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
			Spoon::set('campaignmonitor', $cm);

			// get the default list ID
			$listId = (!empty($listId)) ? $listId : self::getDefaultListID();

			// set the default list ID
			$cm->setListId($listId);
		}

		// return the CampaignMonitor object
		return Spoon::get('campaignmonitor');
	}

	/**
	 * Returns the default list ID
	 *
	 * @return string
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
	 * @param string $email The e-mail address to subscribe.
	 * @param string[optional] $groupId The id of the group to subscribe to.
	 * @return bool
	 */
	public static function subscribe($email, $groupId = null)
	{
		// get objects
		$db = FrontendModel::getDB(true);
		$cm = self::getCM();

		// set groupID
		$groupId = !empty($groupId) ? $groupId : FrontendMailmotorModel::getDefaultGroupID();

		// get campaign monitor list id
		$listId = self::getCampaignMonitorID('list', $groupId);

		// group ID found
		if(FrontendMailmotorModel::existsGroup($groupId) && $cm->subscribe($email, $email, array(), true, $listId))
		{
			// set variables
			$subscriber['email'] = $email;
			$subscriber['source'] = 'website';
			$subscriber['created_on'] = FrontendModel::getUTCDate('Y-m-d H:i:s');

			// insert/update the user
			$db->execute(
				'INSERT INTO mailmotor_addresses(email, source, created_on)
				 VALUES (?, ?, ?)
				 ON DUPLICATE KEY UPDATE source = ?, created_on = ?',
				array(
					$subscriber['email'],
					$subscriber['source'],
					$subscriber['created_on'],
					$subscriber['source'],
					$subscriber['created_on']
				)
			);

			// set variables
			$subscriberGroup['email'] = $email;
			$subscriberGroup['group_id'] = $groupId;
			$subscriberGroup['status'] = 'subscribed';
			$subscriberGroup['subscribed_on'] = FrontendModel::getUTCDate('Y-m-d H:i:s');

			// insert/update the user
			$db->execute(
				'INSERT INTO mailmotor_addresses_groups(email, group_id, status, subscribed_on)
				 VALUES (?, ?, ?, ?)
				 ON DUPLICATE KEY UPDATE group_id = ?, status = ?, subscribed_on = ?',
				array(
					$subscriberGroup['email'],
					$subscriberGroup['group_id'],
					$subscriberGroup['status'],
					$subscriberGroup['subscribed_on'],
					$subscriberGroup['group_id'],
					$subscriberGroup['status'],
					$subscriberGroup['subscribed_on']
				)
			);

			// user subscribed
			return true;
		}

		// user not subscribed
		return false;
	}

	/**
	 * Unsubscribes an e-mail address from CampaignMonitor and our database
	 *
	 * @param string $email The e-mail address to unsubscribe.
	 * @param string[optional] $groupId The id of the group to unsubscribe from.
	 * @return bool
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
				$cm->unsubscribe($email, $groupCMId);
			}

			// for the unsubscribe function we ignore any errors
			catch(Exception $e)
			{
				// stop here if something went wrong with CM
				return false;
			}

			// set variables
			$subscriber['status'] = 'unsubscribed';
			$subscriber['unsubscribed_on'] = FrontendModel::getUTCDate('Y-m-d H:i:s');

			// unsubscribe the user
			$db->update('mailmotor_addresses_groups', $subscriber, 'email = ? AND group_id = ?', array($email, $groupId));

			// user unsubscribed
			return true;
		}

		// user not unsubscribed
		return false;
	}
}
