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
	 * Delete a snippets-item
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
	 * Does the snippets-item exists
	 *
	 * @return	bool
	 * @param	int $id
	 * @param	bool[optional] $activeOnly
	 */
	public static function exists($id, $activeOnly = true)
	{
		// redefine
		$id = (int) $id;
		$activeOnly = (bool) $activeOnly;

		// get db
		$db = BackendModel::getDB();

		// if the item should also be active, there should be at least one row to return true
		if($activeOnly) return ($db->getNumRows('SELECT s.id
												FROM snippets AS s
												WHERE s.id = ? AND s.status = ?;',
												array($id, 'active')) >= 1);

		// fallback, this doesn't take the active status in account
		return ($db->getNumRows('SELECT s.id
									FROM snippets AS s
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
		$values['created_on'] = BackendModel::getUTCDate();
		$values['edited_on'] = BackendModel::getUTCDate();

		// insert and return the insertId
		$db->insert('snippets', $values);

		// build array
		$extra['module'] = 'snippets';
		$extra['type'] = 'widget';
		$extra['label'] = 'Snippets';
		$extra['action'] = 'detail';
		$extra['data'] = serialize(array('extra_label' => $values['title'], 'id' => $newId));
		$extra['hidden'] = 'N';
		$extra['sequence'] = '200'. $newId;

		// insert extra
		$db->insert('pages_extras', $extra);

		// insert the new id
		return $newId;
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
		$version = self::get($id);

		// build array
		$values['id'] = $id;
		$values['user_id'] = BackendAuthentication::getUser()->getUserId();
		$values['language'] = $version['language'];
		$values['hidden'] = ($values['hidden']) ? 'N' : 'Y';
		$values['status'] = 'active';
		$values['created_on'] = BackendModel::getUTCDate('Y-m-d H:i:s', $version['created_on']);
		$values['edited_on'] = BackendModel::getUTCDate();

		// archive all older versions
		$db->update('snippets', array('status' => 'archived'), 'id = ?', array($id));

		// insert new version
		$db->insert('snippets', $values);

		// how many revisions should we keep
		$rowsToKeep = (int) BackendModel::getSetting('snippets', 'maximum_number_of_revisions', 5);

		// get revision-ids for items to keep
		$revisionIdsToKeep = (array) $db->getColumn('SELECT s.revision_id
														FROM snippets AS s
														WHERE s.id = ? AND s.status = ?
														ORDER BY s.edited_on DESC
														LIMIT ?;',
														array($id, 'archived', $rowsToKeep));

		// delete other revisions
		if(!empty($revisionIdsToKeep)) $db->delete('snippets', 'id = ? AND status = ? AND revision_id NOT IN('. implode(', ', $revisionIdsToKeep) .')', array($id, 'archived'));

		// build array
		$extra['data'] = serialize(array('extra_label' => $values['title'], 'id' => $id));

		// update extra
		$db->update('pages_extras', $extra, 'module = ? AND type = ? AND sequence = ?', array('snippets', 'widget', '200'. $id));

		// return id
		return $id;
	}
}

?>