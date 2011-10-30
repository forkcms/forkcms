<?php

/**
 * In this file we specify the functions a class must implement to work with tags.
 * To use tags in your module simply implement this interface in your module's model class.
 *
 * @package		frontend
 * @subpackage	tags
 *
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.0
 */
interface FrontendTagsInterface
{
	/**
	 * Get at least the title and full url for items with the given ids.
	 *
	 * @return	array			Records with at least the keys 'title' and 'full_url'.
	 * @param	array $ids		The ids for which to get the corresponding records.
	 */
	public static function getForTags(array $ids);


	/**
	 * Get the id of an item by the full URL of the current page.
	 * Selects the proper part of the full URL to get the item's id from the database.
	 *
	 * @return	int					The id that corresponds with the given full URL.
	 * @param	FrontendURL $URL	The current URL.
	 */
	public static function getIdForTags(FrontendURL $URL);
}

?>