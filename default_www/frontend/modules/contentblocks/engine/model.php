<?php

/**
 * FrontendContentblocksModel
 * In this file we store all generic functions that we will be using in the contentblocks module
 *
 * @package		frontend
 * @subpackage	contentblocks
 *
 * @author		Dave Lens <dave@netlash.com>
 * @author		Tijs verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendContentblocksModel
{
	/**
	 * Get a snippet
	 *
	 * @return	array
	 * @param	string $id	The id of the snippet to fetch
	 */
	public static function get($id)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = FrontendModel::getDB();

		// get data
		return (array) $db->getRecord('SELECT c.id, c.title, c.content
										FROM contentblocks AS c
										WHERE c.id = ? AND c.status = ? AND c.hidden = ? AND c.language = ?;',
										array($id, 'active', 'N', FRONTEND_LANGUAGE));
	}
}

?>