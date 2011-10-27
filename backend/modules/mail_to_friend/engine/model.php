<?php

/**
 * In this file we store all generic functions that we will be using in the mail_to_friend module
 *
 * @package		backend
 * @subpackage	mail_to_friend
 *
 * @author		Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @since		2.6.10
 */
class BackendMailToFriendModel
{
	/**
	 * Browse the send pages
	 *
	 * @var	string
	 */
	const QRY_BROWSE_MAILS = 'SELECT m.id, m.own, m.friend, UNIX_TIMESTAMP(m.created_on) AS send_on
								FROM mail_to_friend AS m
								WHERE m.language = ?';


	/**
	 * Checks if an item exists
	 *
	 * @return	bool
	 * @param	int $id		The item id.
	 */
	public static function exists($id)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT COUNT(i.id)
														FROM mail_to_friend AS i
														WHERE i.id = ?',
														(int) $id);
	}


	/**
	 * Fetches an item
	 *
	 * @return	array
	 * @param	int $id		The id of the item to fetch.
	 */
	public static function get($id)
	{
		$data = (array) BackendModel::getDB()->getRecord('SELECT i.*, UNIX_TIMESTAMP(i.created_on) AS created_on
															FROM mail_to_friend AS i
															WHERE i.id = ?',
															(int) $id);

		// unserialize the data
		$data['own'] = unserialize($data['own']);
		$data['friend'] = unserialize($data['friend']);

		// return
		return $data;
	}


	/**
	 * Fetches all the items ready for export
	 *
	 * @return	array
	 */
	public static function getAllForExport()
	{
		$data = (array) BackendModel::getDB()->getRecords('SELECT i.*
															FROM mail_to_friend AS i
															WHERE i.language = ?',
															array(BL::getWorkingLanguage()));

		// the return data
		$returnData = array();

		// loop the data
		foreach($data as $key => $mail)
		{
			// set the return data
			$returnData[$key] = array();

			// unserialize the data
			$mail['own'] = unserialize($mail['own']);
			$mail['friend'] = unserialize($mail['friend']);

			// set the id
			$returnData[$key]['id'] = $mail['id'];

			// loop the users information
			foreach($mail['own'] as $userKey => $userInfo) $returnData[$key]['own' . ucfirst($userKey)] = $userInfo;

			// loop the friends information
			foreach($mail['friend'] as $friendKey => $friendInfo) $returnData[$key]['friend' . ucfirst($friendKey)] = $friendInfo;

			// set the extra information
			$returnData[$key]['page'] = $mail['page'];
			$returnData[$key]['date'] = $mail['created_on'];
		}

		// return the data
		return $returnData;
	}


	/**
	 * Fetches a certain value from a serialized array
	 *
	 * @return	string
	 * @param	string $data		The serialized data.
	 * @param	string $value		The value to fetch.
	 */
	public static function getDataGridData($data, $value)
	{
		// unserialize the data
		$data = unserialize($data);

		// check if the value is set
		if(!isset($data[$value])) return;

		// return the value
		return $data[$value];
	}
}
