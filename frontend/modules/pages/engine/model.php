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
 * @author Matthias Mullie <matthias@mullie.eu>
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
		$items = (array) FrontendModel::getDB()->getRecords(
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
	 * @param int $ids The id of the item to grab the subpages for.
	 * @return array
	 */
	public static function getSubpages($id)
	{
		// fetch items
		$items = (array) FrontendModel::getDB()->getRecords(
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
		$db = FrontendModel::getDB();

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

	/**
	 * This will output the right url for the sitemap.
	 *
	 * @param array $data This is the data provided by the sitemap
	 * @param string $language
	 * @return array
	 */
	public static function sitemap(array $data, $language)
	{
		$pageUrl = (string) $data['url'];
		$pageId = (int) FrontendModel::getDB()->getVar(
			'SELECT p.id
			 FROM pages AS p
			 INNER JOIN meta AS m ON m.id = p.meta_id
			 WHERE m.url = ? AND m.sitemap_id = ? AND p.language = ?',
			array($pageUrl, (int) $data['id'], (string) $language)
		);

		$data['full_url'] = SITE_URL . FrontendNavigation::getURL($pageId, $language);
		return $data;
	}

	/**
	 * A function that is used for the sitemap. This will go trough all the blog data and find images
	 *
	 * @param string $language
	 * @return array
	 */
	public static function sitemapImages()
	{
		$returnData = array();
		$data = (array) FrontendModel::getDB()->getRecords(
			'SELECT p.id, pb.html, p.title, p.language, m.url
			 FROM pages AS p
			 INNER JOIN pages_blocks AS pb ON pb.revision_id = p.revision_id
			 INNER JOIN meta AS m ON m.id = p.meta_id
			 WHERE p.hidden = ? AND p.status = ? AND p.publish_on < ? AND pb.html != ?',
			array('N', 'active', date('Y-m-d H:i') . ':00', '')
		);

		foreach($data as $key => $block)
		{
			// get the blog posts image data
			$blockImages = (array) FrontendModel::getImagesFromHtml($block['html']);

			// don't add the post if we don't have any images
			if(empty($blockImages)) continue;

			$url = FrontendNavigation::getURL($block['id'], $block['language']);
			$tmpData = array(
				'full_url' => $url,
				'action' => 'detail',
				'language' => $block['language'],
				'images' => array()
			);

			// add the images to the data
			foreach($blockImages as $image) $tmpData['images'][] = $image;

			// add a description to the image
			foreach($tmpData['images'] as $key => $image)
			{
				if(isset($image['alt']) && $image['alt'] == '') $tmpData['images'][$key]['alt'] = $block['title'];
				$tmpData['images'][$key]['description'] = $block['html'];
			}

			$returnData[] = $tmpData;
		}

		return $returnData;
	}
}
