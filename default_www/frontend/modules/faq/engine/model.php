<?php

/**
 * In this file we store all generic functions that we will be using in the faq module
 *
 * @package		frontend
 * @subpackage	faq
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @since		2.1
 */
class FrontendFaqModel implements FrontendTagsInterface
{
	/**
	 * Get a specific item
	 *
	 * @return	array
	 * @param	string $url		The url of the item.
	 */
	public static function get($url)
	{
		return (array) FrontendModel::getDB()->getRecord('SELECT i.*, m.url, c.title AS category_title, m2.url AS category_url
															FROM faq_questions AS i
															INNER JOIN meta AS m ON i.meta_id = m.id
															INNER JOIN faq_categories AS c ON i.category_id = c.id
															INNER JOIN meta AS m2 ON c.meta_id = m2.id
															WHERE m.url = ? AND i.language = ? AND i.hidden = ?
															ORDER BY i.sequence',
															array((string) $url, FRONTEND_LANGUAGE, 'N'));
	}


	/**
	 * Get all items in a category
	 *
	 * @return	array
	 * @param	int $categoryId			The id of the category.
	 */
	public static function getAllForCategory($categoryId, $limit = null)
	{
		// get items
		if($limit != null) $items = (array) FrontendModel::getDB()->getRecords('SELECT i.*, m.url
																				FROM faq_questions AS i
																				INNER JOIN meta AS m ON i.meta_id = m.id
																				WHERE i.category_id = ? AND i.language = ? AND i.hidden = ?
																				ORDER BY i.sequence
																				LIMIT ?',
																				array((int) $categoryId, FRONTEND_LANGUAGE, 'N', (int) $limit));
		else $items = (array) FrontendModel::getDB()->getRecords('SELECT i.*, m.url
																	FROM faq_questions AS i
																	INNER JOIN meta AS m ON i.meta_id = m.id
																	WHERE i.category_id = ? AND i.language = ? AND i.hidden = ?
																	ORDER BY i.sequence',
																	array((int) $categoryId, FRONTEND_LANGUAGE, 'N'));

		// init var
		$link = FrontendNavigation::getURLForBlock('faq', 'detail');

		// build url
		foreach($items as &$item) $item['full_url'] = $link . '/' . $item['url'];

		// return 'em
		return $items;
	}


	/**
	 * Get all categories
	 *
	 * @return	array
	 */
	public static function getCategories()
	{
		$items = (array) FrontendModel::getDB()->getRecords('SELECT i.*, m.url
																FROM faq_categories AS i
																INNER JOIN meta AS m ON i.meta_id = m.id
																WHERE i.language = ?
																ORDER BY i.sequence',
																array(FRONTEND_LANGUAGE));

		// init var
		$link = FrontendNavigation::getURLForBlock('faq', 'category');

		// build url
		foreach($items as &$item) $item['full_url'] = $link . '/' . $item['url'];

		// return 'em
		return $items;
	}


	/**
	 * Get a category
	 *
	 * @return	array
	 * @param	string $url		The url of the item.
	 */
	public static function getCategory($url)
	{
		return (array) FrontendModel::getDB()->getRecord('SELECT i.*, m.url
															FROM faq_categories AS i
															INNER JOIN meta AS m ON i.meta_id = m.id
															WHERE m.url = ? AND i.language = ?
															ORDER BY i.sequence',
															array((string) $url, FRONTEND_LANGUAGE));
	}


	/**
	 * Fetch the list of tags for a list of items
	 *
	 * @return	array
	 * @param	array $ids	The ids of the items to grab.
	 */
	public static function getForTags(array $ids)
	{
		// fetch items
		$items = (array) FrontendModel::getDB()->getRecords('SELECT i.question AS title, m.url
																FROM faq_questions AS i
																INNER JOIN meta AS m ON m.id = i.meta_id
																WHERE i.hidden = ? AND i.id IN (' . implode(',', $ids) . ')
																ORDER BY i.question',
																array('N'));

		// has items
		if(!empty($items))
		{
			// init var
			$link = FrontendNavigation::getURLForBlock('faq', 'detail');

			// reset url
			foreach($items as &$row) $row['full_url'] = $link . '/' . $row['url'];
		}

		// return
		return $items;
	}


	/**
	 * Get the id of an item by the full URL of the current page.
	 * Selects the proper part of the full URL to get the item's id from the database.
	 *
	 * @return	int					The id that corresponds with the given full URL.
	 * @param	FrontendURL $URL	The current URL.
	 */
	public static function getIdForTags(FrontendURL $URL)
	{
		// select the proper part of the full URL
		$itemURL = (string) $URL->getParameter(1);

		// return the item
		return self::get($itemURL);
	}


	/**
	 * Get all items in a category
	 *
	 * @return	array
	 * @param	int $limit			The number of items to return.
	 */
	public static function getMostRead($limit)
	{
		// get items
		$items = (array) FrontendModel::getDB()->getRecords('SELECT i.*, m.url
																FROM faq_questions AS i
																INNER JOIN meta AS m ON i.meta_id = m.id
																WHERE i.num_views > 0 AND i.language = ? AND i.hidden = ?
																ORDER BY i.num_views DESC
																LIMIT ?',
																array(FRONTEND_LANGUAGE, 'N', (int) $limit));

		// init var
		$link = FrontendNavigation::getURLForBlock('faq', 'detail');

		// build url
		foreach($items as &$item) $item['full_url'] = $link . '/' . $item['url'];

		// return 'em
		return $items;
	}


	/**
	 * Get related items based on tags
	 *
	 * @return	array
	 * @param	int $id					The id of the item to get related items for.
	 * @param	int[optional] $limit	The maximum number of items to retrieve.
	 */
	public static function getRelated($id, $limit = 5)
	{
		// redefine
		$id = (int) $id;
		$limit = (int) $limit;

		// get the related IDs
		$relatedIDs = (array) FrontendTagsModel::getRelatedItemsByTags($id, 'faq', 'faq');

		// no items
		if(empty($relatedIDs)) return array();

		// get link
		$link = FrontendNavigation::getURLForBlock('faq', 'detail');

		// get items
		$items = (array) FrontendModel::getDB()->getRecords('SELECT i.id, i.question, m.url
																FROM faq_questions AS i
																INNER JOIN meta AS m ON i.meta_id = m.id
																WHERE i.language = ? AND i.hidden = ? AND i.id IN(' . implode(',', $relatedIDs) . ')
																ORDER BY i.question
																LIMIT ?',
																array(FRONTEND_LANGUAGE, 'N', $limit), 'id');

		// loop items
		foreach($items as &$row)
		{
			$row['full_url'] = $link . '/' . $row['url'];
		}

		// return
		return $items;
	}


	/**
	 * Increase the number of views for this item
	 *
	 * @return	array
	 * @param	int $id									The id of the item.
	 * @param	bool $usefull							Was this question userfull?
	 * @param	mixed[optional] $previousFeedback		What was the previous feedback?
	 */
	public static function updateFeedback($id, $usefull, $previousFeedback = null)
	{
		// feedback hasn't changed so don't update the counters
		if($previousFeedback !== null && $usefull == $previousFeedback) return;

		// get db
		$db = FrontendModel::getDB(true);

		// update counter with current feedback (increase)
		if($usefull) $db->execute('UPDATE faq_questions SET num_usefull_yes = num_usefull_yes + 1 WHERE id = ?', array((int) $id));
		else $db->execute('UPDATE faq_questions SET num_usefull_no = num_usefull_no + 1 WHERE id = ?', array((int) $id));

		// update counter with previous feedback (decrease)
		if($previousFeedback) $db->execute('UPDATE faq_questions SET num_usefull_yes = num_usefull_yes - 1 WHERE id = ?', array((int) $id));
		elseif($previousFeedback !== null) $db->execute('UPDATE faq_questions SET num_usefull_no = num_usefull_no - 1 WHERE id = ?', array((int) $id));
	}


	/**
	 * Increase the number of views for this item
	 *
	 * @return	array
	 * @param	int $id		The id of the item.
	 */
	public static function increaseViewCount($id)
	{
		FrontendModel::getDB(true)->execute('UPDATE faq_questions SET num_views = num_views + 1 WHERE id = ?', array((int) $id));
	}


	/**
	 * Parse the search results for this module
	 *
	 * Note: a module's search function should always:
	 * 		- accept an array of entry id's
	 * 		- return only the entries that are allowed to be displayed, with their array's index being the entry's id
	 *
	 *
	 * @return	array
	 * @param	array $ids		The ids of the found results.
	 */
	public static function search(array $ids)
	{
		// get items
		$items = (array) FrontendModel::getDB()->getRecords('SELECT i.id, i.question AS title, i.answer AS text, m.url,
																c.title AS category_title, m2.url AS category_url
																FROM faq_questions AS i
																INNER JOIN meta AS m ON i.meta_id = m.id
																INNER JOIN faq_categories AS c ON c.id = i.category_id
																INNER JOIN meta AS m2 ON c.meta_id = m2.id
																WHERE i.hidden = ? AND i.language = ? AND i.id IN (' . implode(',', $ids) . ')',
																array('N', FRONTEND_LANGUAGE), 'id');

		// prepare items for search
		foreach($items as &$item)
		{
			// get the full url
			$item['full_url'] = FrontendNavigation::getURLForBlock('faq', 'detail') . '/' . $item['url'];

			// create an url object for this item
			$URL = new FrontendURL($item['full_url']);

			// create a breadcrumb object for this item
			$breadcrumb = new FrontendBreadcrumb($URL);

			// add the item itself to the breadcrumb
			$breadcrumb->addElement($item['category_title'], FrontendNavigation::getURLForBlock('faq', 'category') . '/' . $item['category_url']);
			$breadcrumb->addElement($item['title'], $item['full_url']);

			// save the parsed breadcrumb
			$item['breadcrumb'] = $breadcrumb->getItems();
		}

		// return
		return $items;
	}
}

?>