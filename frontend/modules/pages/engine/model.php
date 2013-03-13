<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the pages module
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class FrontendPagesModel implements FrontendTagsInterface
{
	/**
	 * Fetch a list of items for a list of ids
	 *
	 * @param array $ids The ids of the items to grab.
	 * @return array
	 */
	public static function getForTags(array $ids)
	{
		// fetch items
		$items = (array) FrontendModel::getContainer()->get('database')->getRecords(
			'SELECT i.id, i.title
			 FROM pages AS i
			 INNER JOIN meta AS m ON m.id = i.meta_id
			 WHERE i.status = ? AND i.hidden = ? AND i.language = ? AND i.publish_on <= ? AND i.id IN (' . implode(',', $ids) . ')
			 ORDER BY i.title ASC',
			array('active', 'N', FRONTEND_LANGUAGE, FrontendModel::getUTCDate('Y-m-d H:i') . ':00')
		);

		// has items
		if(!empty($items))
		{
			// reset url
			foreach($items as &$row) $row['full_url'] = FrontendNavigation::getURL($row['id'], FRONTEND_LANGUAGE);
		}

		// return
		return $items;
	}

	/**
	 * Get the id of an item by the full URL of the current page.
	 * Selects the proper part of the full URL to get the item's id from the database.
	 *
	 * @param FrontendURL $URL The current URL.
	 * @return int
	 */
	public static function getIdForTags(FrontendURL $URL)
	{
		return FrontendNavigation::getPageId($URL->getQueryString());
	}

	/**
	 * Fetch a list of subpages of a page.
	 *
	 * @param int $id The id of the item to grab the subpages for.
	 * @return array
	 */
	public static function getSubpages($id)
	{
		// fetch items
		$items = (array) FrontendModel::getContainer()->get('database')->getRecords(
			'SELECT i.id, i.title, m.description, i.parent_id
			 FROM pages AS i
			 INNER JOIN meta AS m ON m.id = i.meta_id
			 WHERE i.parent_id = ? AND i.status = ? AND i.hidden = ?
			 AND i.language = ? AND i.publish_on <= ?
			 ORDER BY i.sequence ASC',
			array((int) $id, 'active', 'N', FRONTEND_LANGUAGE, FrontendModel::getUTCDate('Y-m-d H:i') . ':00'));

		// has items
		if(!empty($items))
		{
			// reset url
			foreach($items as &$row) $row['full_url'] = FrontendNavigation::getURL($row['id'], FRONTEND_LANGUAGE);
		}

		// return
		return $items;
	}

	/**
	 * Parse the search results for this module
	 *
	 * Note: a module's search function should always:
	 * 		- accept an array of entry id's
	 * 		- return only the entries that are allowed to be displayed, with their array's index being the entry's id
	 *
	 * @param array $ids The ids of the found results.
	 * @return array
	 */
	public static function search(array $ids)
	{
		// get db
		$db = FrontendModel::getContainer()->get('database');

		// define ids's to ignore
		$ignore = array(404);

		// get items
		$items = (array) $db->getRecords(
			'SELECT p.id, p.title, m.url, p.revision_id AS text
			 FROM pages AS p
			 INNER JOIN meta AS m ON p.meta_id = m.id
			 INNER JOIN themes_templates AS t ON p.template_id = t.id
			 WHERE p.id IN (' . implode(', ', $ids) . ') AND p.id NOT IN (' . implode(', ', $ignore) . ') AND p.status = ? AND p.hidden = ? AND p.language = ?',
			array('active', 'N', FRONTEND_LANGUAGE), 'id'
		);

		// prepare items for search
		foreach($items as &$item)
		{
			$item['text'] = implode(' ', (array) $db->getColumn(
				'SELECT pb.html
				 FROM pages_blocks AS pb
				 WHERE pb.revision_id = ?',
				array($item['text']))
			);

			$item['full_url'] = FrontendNavigation::getURL($item['id']);
		}

		return $items;
	}
}
