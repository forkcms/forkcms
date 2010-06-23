<?php

/**
 * FrontendContentBlocksModel
 * In this file we store all generic functions that we will be using in the content_blocks module
 *
 * @package		frontend
 * @subpackage	content_blocks
 *
 * @author		Dave Lens <dave@netlash.com>
 * @author		Tijs verkoyen <tijs@netlash.com>
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class FrontendContentBlocksModel
{
	/**
	 * Get an item.
	 *
	 * @return	array
	 * @param	string $id			The id of the item to fetch.
	 */
	public static function get($id)
	{
		// get db
		$db = FrontendModel::getDB();

		// get data
		return (array) $db->getRecord('SELECT i.id, i.title, i.content
										FROM content_blocks AS i
										WHERE i.id = ? AND i.status = ? AND i.hidden = ? AND i.language = ?;',
										array((int) $id, 'active', 'N', FRONTEND_LANGUAGE));
	}
}

?>