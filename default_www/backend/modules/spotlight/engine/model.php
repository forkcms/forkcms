<?php

/**
 * BackendSpotlightModel
 *
 * In this file we store all generic functions that we will be using in the SpotlightModule
 *
 *
 * @package		backend
 * @subpackage	spotlight
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendSpotlightModel
{
	// overview of the items
	const QRY_BROWSE = 'SELECT s.id, s.title, s.sequence
						FROM spotlight AS s
						WHERE s.status = ?
						ORDER BY s.sequence ASC;';

	// overview of the revisions for an item
	const QRY_BROWSE_REVISIONS = 'SELECT s.id, s.revision_id, s.title, UNIX_TIMESTAMP(s.edited_on) AS edited_on
									FROM spotlight AS s
									WHERE s.status = ? AND s.id = ?
									ORDER BY s.edited_on DESC;';


	public static function delete($id)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = BackendModel::getDB();

		// delete all records
		$db->delete('spotlight', 'id = ?', $id);
	}


	/**
	 * Does the spotlight-item exists
	 *
	 * @return	bool
	 * @param	int $id
	 * @param	bool[optional] $active
	 */
	public static function exists($id, $active = true)
	{
		// redefine
		$id = (int) $id;
		$active = (bool) $active;

		// get db
		$db = BackendModel::getDB();

		// if the item should also be active, there should be at least one row to return true
		if($active) return ($db->getNumRows('SELECT s.id
												FROM spotlight AS s
												WHERE s.id = ? AND s.status = ?;',
												array($id, 'active')) >= 1);

		// fallback, this doesn't hold the active status in account
		return ($db->getNumRows('SELECT s.id
									FROM spotlight AS s
									WHERE s.revision_id = ?;',
									array($id)) >= 1);
	}


	/**
	 * Get all data for a given id
	 *
	 * @return	array
	 * @param	int $id
	 */
	public static function get($id)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = BackendModel::getDB();

		// get record and return it
		return (array) $db->getRecord('SELECT s.*, UNIX_TIMESTAMP(created_on) AS created_on, UNIX_TIMESTAMP(edited_on) AS edited_on
										FROM spotlight AS s
										WHERE s.id = ? AND s.status = ?
										LIMIT 1;',
										array($id, 'active'));
	}


	/**
	 * Get all data for a given revision
	 *
	 * @return	array
	 * @param	int $id
	 * @param	int $revisionId
	 */
	public static function getRevision($id, $revisionId)
	{
		// redefine
		$id = (int) $id;
		$revisionId = (int) $revisionId;

		// get db
		$db = BackendModel::getDB();

		// get record and return it
		return (array) $db->getRecord('SELECT s.*, UNIX_TIMESTAMP(created_on) AS created_on, UNIX_TIMESTAMP(edited_on) AS edited_on
										FROM spotlight AS s
										WHERE s.id = ? AND s.revision_id = ?
										LIMIT 1;',
										array($id, $revisionId));
	}



	/**
	 * Add a new spotlight-item
	 *
	 * @return	int
	 * @param	array $aValues
	 */
	public static function insert(array $aValues)
	{
		// redefine
		$aValues = (array) $aValues;

		// get db
		$db = BackendModel::getDB();

		// calculate new id
		$newId = (int) $db->getVar('SELECT MAX(s.id)
									FROM spotlight AS s
									LIMIT 1;') + 1;

		// calculate new sequence
		$newSequence = (int) $db->getVar('SELECT MAX(s.sequence)
											FROM spotlight AS s
											LIMIT 1;') + 1;

		// build array
		$aValues['id'] = $newId;
		$aValues['user_id'] = BackendAuthentication::getUser()->getUserId();
		$aValues['language'] = BL::getWorkingLanguage();
		$aValues['hidden'] = ($aValues['hidden']) ? 'N' : 'Y';
		$aValues['status'] = 'active';
		$aValues['created_on'] = date('Y-m-d H:i:s');
		$aValues['edited_on'] = date('Y-m-d H:i:s');
		$aValues['sequence'] = $newSequence;

		// insert and return the insertId
		$db->insert('spotlight', $aValues);

		// insert the new id
		return $newId;
	}


	/**
	 * Update an existing spotlight-item
	 *
	 * @return	int
	 * @param	int $id
	 * @param	array $aValues
	 */
	public static function update($id, array $aValues)
	{
		// redefine
		$id = (int) $id;
		$aValues = (array) $aValues;

		// get db
		$db = BackendModel::getDB();

		// get current version
		$aVersion = (array) self::get($id);

		// build array
		$aValues['id'] = $id;
		$aValues['user_id'] = BackendAuthentication::getUser()->getUserId();
		$aValues['language'] = BL::getWorkingLanguage();
		$aValues['hidden'] = ($aValues['hidden']) ? 'N' : 'Y';
		$aValues['status'] = 'active';
		$aValues['created_on'] = date('Y-m-d H:i:s', $aVersion['created_on']);
		$aValues['edited_on'] = date('Y-m-d H:i:s');
		$aValues['sequence'] = $aVersion['sequence'];

		// archive all older versions
		$db->update('spotlight', array('status' => 'archived'), 'id = ?', array($id));

		// insert new version
		$db->insert('spotlight', $aValues);

		// how many revisions should we keep
		$rowsToKeep = (int) BackendModel::getModuleSetting(null, 'maximum_number_of_revisions', 5);

		// get revision-ids for items to keep
		$aRevisionIdsToKeep = (array) $db->getColumn('SELECT s.revision_id
														FROM spotlight AS s
														WHERE s.id = ? AND s.status = ?
														ORDER BY s.edited_on DESC
														LIMIT ?;',
														array($id, 'archived', $rowsToKeep));

		// delete other revisions
		if(!empty($aRevisionIdsToKeep)) $db->delete('spotlight', 'id = ? AND status = ? AND revision_id NOT IN('. implode(', ', $aRevisionIdsToKeep) .')', array($id, 'archived'));

		// return id
		return $id;
	}


	/**
	 * Update the sequence
	 *
	 * @return	bool
	 * @param	array $aNewIdsSequence
	 */
	public static function updateSequence(array $aNewIdsSequence)
	{
		// redefine
		$aNewIdsSequence = (array) $aNewIdsSequence;

		// get db
		$db = BackendModel::getDB();

		// init var
		$sequence = 1;

		// loop ids in correct order
		foreach($aNewIdsSequence as $id)
		{
			// update
			$db->update('spotlight', array('sequence' => $sequence), 'id = ?', array($id));

			// increment counter
			$sequence++;
		}

		// return
		return true;
	}
}

?>