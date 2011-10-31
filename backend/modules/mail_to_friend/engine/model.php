<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the mail_to_friend module
 *
 * @author Jelmer Snoeck <jelmer@netlash.com>
 */
class BackendMailToFriendModel
{
	/**
	 * Browse the send pages
	 *
	 * @var	string
	 */
	const QRY_BROWSE_MAILS =
		'SELECT m.id, m.own, m.friend, UNIX_TIMESTAMP(m.created_on) AS send_on
		 FROM mail_to_friend AS m
		 WHERE m.language = ?';

	/**
	 * Checks if an item exists
	 *
	 * @param int $id
	 * @return bool
	 */
	public static function exists($id)
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT COUNT(i.id)
			 FROM mail_to_friend AS i
			 WHERE i.id = ?',
			array((int) $id));
	}

	/**
	 * Fetches an item
	 *
	 * @param int $id
	 * @return array
	 */
	public static function get($id)
	{
		$data = (array) BackendModel::getDB()->getRecord(
			'SELECT i.*, UNIX_TIMESTAMP(i.created_on) AS created_on
			 FROM mail_to_friend AS i
			 WHERE i.id = ?',
			array((int) $id));

		$data['own'] = unserialize($data['own']);
		$data['friend'] = unserialize($data['friend']);

		return $data;
	}

	/**
	 * Fetches all the items ready for export
	 *
	 * @return array
	 */
	public static function getAllForExport()
	{
		$data = (array) BackendModel::getDB()->getRecords(
			'SELECT i.*
			 FROM mail_to_friend AS i
			 WHERE i.language = ?',
			array(BL::getWorkingLanguage()));

		$returnData = array();
		foreach($data as $key => $mail)
		{
			$returnData[$key] = array();
			$mail['own'] = unserialize($mail['own']);
			$mail['friend'] = unserialize($mail['friend']);
			$returnData[$key]['id'] = $mail['id'];

			// loop the users information
			foreach($mail['own'] as $userKey => $userInfo) $returnData[$key]['own' . ucfirst($userKey)] = $userInfo;

			// loop the friends information
			foreach($mail['friend'] as $friendKey => $friendInfo) $returnData[$key]['friend' . ucfirst($friendKey)] = $friendInfo;

			// set the extra information
			$returnData[$key]['page'] = $mail['page'];
			$returnData[$key]['date'] = $mail['created_on'];
		}

		return $returnData;
	}

	/**
	 * Fetches a certain value from a serialized array
	 *
	 * @param string $data
	 * @param string $value
	 * @return string
	 */
	public static function getDataGridData($data, $value)
	{
		$data = unserialize($data);
		if(!isset($data[$value])) return;

		return $data[$value];
	}
}
