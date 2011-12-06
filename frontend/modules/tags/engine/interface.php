<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we specify the functions a class must implement to work with tags.
 * To use tags in your module simply implement this interface in your module's model class.
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
interface FrontendTagsInterface
{
	/**
	 * Get at least the title and full url for items with the given ids.
	 *
	 * @param array $ids The ids for which to get the corresponding records.
	 * @return array Records with at least the keys 'title' and 'full_url'.
	 */
	public static function getForTags(array $ids);

	/**
	 * Get the id of an item by the full URL of the current page.
	 * Selects the proper part of the full URL to get the item's id from the database.
	 *
	 * @param FrontendURL $URL The current URL.
	 * @return int The id that corresponds with the given full URL.
	 */
	public static function getIdForTags(FrontendURL $URL);
}
