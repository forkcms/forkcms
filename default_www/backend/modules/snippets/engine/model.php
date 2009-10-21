<?php

/**
 * BackendSnippetsModel
 *
 * In this file we store all generic functions that we will be using in the SnippetsModule
 *
 *
 * @package		backend
 * @subpackage	snippets
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendSnippetsModel
{
	// overview of the items
	const QRY_BROWSE = 'SELECT s.id, s.title
						FROM snippets AS s
						WHERE s.status = ?;';

	// overview of the revisions for an item
	const QRY_BROWSE_REVISIONS = 'SELECT s.id, s.revision_id, s.title, UNIX_TIMESTAMP(s.edited_on) AS edited_on
									FROM snippets AS s
									WHERE s.status = ? AND s.id = ?
									ORDER BY s.edited_on DESC;';


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
										FROM snippets AS s
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
										FROM snippets AS s
										WHERE s.id = ? AND s.revision_id = ?
										LIMIT 1;',
										array($id, $revisionId));
	}


	/**
	 * Add a new spotlight-item
	 *
	 * @return	int
	 * @param	array $values
	 */
	public static function insert(array $values)
	{
		// get db
		$db = BackendModel::getDB();

		// calculate new id
		$newId = (int) $db->getVar('SELECT MAX(id) FROM snippets LIMIT 1;') + 1;

		// build array
		$values['id'] = $newId;
		$values['user_id'] = BackendAuthentication::getUser()->getUserId();
		$values['language'] = BL::getWorkingLanguage();
		$values['hidden'] = ($values['hidden']) ? 'N' : 'Y';
		$values['status'] = 'active';
		$values['created_on'] = date('Y-m-d H:i:s');
		$values['edited_on'] = date('Y-m-d H:i:s');

		// insert and return the insertId
		$db->insert('snippets', $values);

		// insert the new id
		return $newId;
	}




















	/**
	 * Delete a spotlight-item
	 *
	 * @return	void
	 * @param	int $id
	 */
	public static function delete($id)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = BackendModel::getDB();

		// delete all records
		$db->delete('snippets', 'id = ?', $id);
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
												FROM snippets AS s
												WHERE s.id = ? AND s.status = ?;',
												array($id, 'active')) >= 1);

		// fallback, this doesn't hold the active status in account
		return ($db->getNumRows('SELECT s.id
									FROM snippets AS s
									WHERE s.revision_id = ?;',
									array($id)) >= 1);
	}


	/**
	 * Update an existing spotlight-item
	 *
	 * @return	int
	 * @param	int $id
	 * @param	array $values
	 */
	public static function update($id, array $values)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = BackendModel::getDB();

		// get current version
		$version = (array) self::get($id);

		// build array
		$values['id'] = $id;
		$values['user_id'] = BackendAuthentication::getUser()->getUserId();
		$values['language'] = BL::getWorkingLanguage();
		$values['hidden'] = ($values['hidden']) ? 'N' : 'Y';
		$values['status'] = 'active';
		$values['created_on'] = date('Y-m-d H:i:s', $version['created_on']);
		$values['edited_on'] = date('Y-m-d H:i:s');
		$values['sequence'] = $version['sequence'];

		// archive all older versions
		$db->update('snippets', array('status' => 'archived'), 'id = ?', array($id));

		// insert new version
		$db->insert('snippets', $values);

		// how many revisions should we keep
		$rowsToKeep = (int) BackendModel::getModuleSetting(null, 'maximum_number_of_revisions', 5);

		// get revision-ids for items to keep
		$revisionIdsToKeep = (array) $db->getColumn('SELECT s.revision_id
														FROM snippets AS s
														WHERE s.id = ? AND s.status = ?
														ORDER BY s.edited_on DESC
														LIMIT ?;',
														array($id, 'archived', $rowsToKeep));

		// delete other revisions
		if(!empty($revisionIdsToKeep)) $db->delete('snippets', 'id = ? AND status = ? AND revision_id NOT IN('. implode(', ', $revisionIdsToKeep) .')', array($id, 'archived'));

		// return id
		return $id;
	}


	/**
	 * Update the sequence
	 *
	 * @return	bool
	 * @param	array $newIdsSequence
	 */
	public static function updateSequence(array $newIdsSequence)
	{
		// get db
		$db = BackendModel::getDB();

		// init var
		$sequence = 1;

		// loop ids in correct order
		foreach($newIdsSequence as $id)
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