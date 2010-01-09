<?php

/**
 * BackendTagsModel
 *
 * In this file we store all generic functions that we will be using in the TagsModule
 *
 *
 * @package		backend
 * @subpackage	tags
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendTagsModel
{

	public static function getStartsWith($query, $limit = 10)
	{
		// redefine
		$query = (string) $query;
		$limit = (int) $limit;

		// get db
		$db = BackendModel::getDB();

		// make the call
		return (array) $db->retrieve('SELECT t.tag AS name, t.tag AS value
										FROM tags AS t
										WHERE t.tag LIKE ?
										ORDER BY t.tag ASC
										LIMIT ?;',
										array($query .'%', $limit));
	}

}

?>