<?php

/**
 * In this file we store all generic functions that we will be using in the faq module
 *
 * @package		backend
 * @subpackage	faq
 *
 * @author		Lester Lievens <lester@netlash.com>
<<<<<<< HEAD
 * @author		Matthias Mullie <matthias@netlash.com>
 * @author		Annelies Van Extergem <annelies@netlash.com>
=======
 * @author		Matthias Mullie <matthias@mullie.eu>
>>>>>>> 170c938d964cfe1376b48d9def10036168378b89
 * @since		2.1
 */
class BackendFaqModel
{
	const QRY_DATAGRID_BROWSE = 'SELECT i.id, i.category_id, i.question, i.num_views, i.num_usefull_yes, i.num_usefull_no, i.hidden, i.sequence
									FROM faq_questions AS i
									WHERE i.language = ? AND i.category_id = ?
									ORDER BY i.sequence ASC';
	const QRY_DATAGRID_BROWSE_CATEGORIES = 'SELECT i.id, i.title, COUNT(p.id) AS num_items, i.sequence
											FROM faq_categories AS i
											LEFT OUTER JOIN faq_questions AS p ON i.id = p.category_id AND p.language = i.language
											WHERE i.language = ?
											GROUP BY i.id
											ORDER BY i.sequence ASC';


	/**
	 * Delete a question
	 *
	 * @return	void
	 * @param	int $id		The id of the question to delete.
	 */
	public static function delete($id)
	{
		// delete item
		BackendModel::getDB(true)->delete('faq_questions', 'id = ?', array((int) $id));

		// delete tags
		BackendTagsModel::saveTags($id, '', 'faq');
	}


	/**
	 * Deletes a category
	 *
	 * @return	void
	 * @param	int $id		The id of the category to delete.
	 */
	public static function deleteCategory($id)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = BackendModel::getDB(true);

		// get item
		$item = self::getCategory($id);

		// any items?
		if(!empty($item))
		{
			// delete meta
			$db->delete('meta', 'id = ?', array($item['meta_id']));

			// delete category
			$db->delete('faq_categories', 'id = ?', array($id));

			// update category for the posts that might be in this category
			$db->update('faq_questions', array('category_id' => null), 'category_id = ?', array($id));

			// invalidate the cache for blog
			BackendModel::invalidateFrontendCache('blog', BL::getWorkingLanguage());
		}
	}


	/**
	 * Checks if it is allowed to delete the category
	 *
	 * @return	bool
	 * @param	int $id		The id of the category.
	 */
	public static function deleteCategoryAllowed($id)
	{
		return (BackendModel::getDB()->getVar('SELECT COUNT(id)
												FROM faq_questions AS i
												WHERE i.category_id = ? AND i.language = ?',
												array((int) $id, BL::getWorkingLanguage())) == 0);
	}


	/**
	 * Checks if an item exists
	 *
	 * @return	bool
	 * @param	int $id		The id of the item to check for existence.
	 */
	public static function exists($id)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT COUNT(i.id)
														FROM faq_questions AS i
														WHERE i.id = ? AND i.language = ?',
														array((int) $id, BL::getWorkingLanguage()));
	}


	/**
	 * Checks if a category exists
	 *
	 * @return	bool
	 * @param	int $id		The id of the category to check for existence.
	 */
	public static function existsCategory($id)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT COUNT(i.id)
														FROM faq_categories AS i
														WHERE i.id = ? AND i.language = ?',
														array((int) $id, BL::getWorkingLanguage()));
	}


	/**
	 * Get a question by id
	 *
	 * @return	array
	 * @param	int $id		The id of the item to fetch?
	 */
	public static function get($id)
	{
		return (array) BackendModel::getDB()->getRecord('SELECT i.*, m.url
															FROM faq_questions AS i
															INNER JOIN meta AS m ON m.id = i.meta_id
															WHERE i.id = ? AND i.language = ?',
															array((int) $id, BL::getWorkingLanguage()));
	}


	/**
	 * Get all items by a given tag id
	 *
	 * @return	array
	 * @param	int $tagId	The id of the tag.
	 */
	public static function getByTag($tagId)
	{
		// get the items
		$items = (array) BackendModel::getDB()->getRecords('SELECT i.id AS url, i.question AS name, mt.module
															FROM modules_tags AS mt
															INNER JOIN tags AS t ON mt.tag_id = t.id
															INNER JOIN faq_questions AS i ON mt.other_id = i.id
															WHERE mt.module = ? AND mt.tag_id = ? AND i.language = ?',
															array('faq', (int) $tagId, BL::getWorkingLanguage()));

		// loop items and create url
		foreach($items as &$row) $row['url'] = BackendModel::createURLForAction('edit', 'faq', null, array('id' => $row['url']));

		// return
		return $items;
	}


	/**
	 * Get all categories
	 *
	 * @return	array
	 * @param	bool[optional] $includeCount	Include the count?
	 */
	public static function getCategories($includeCount = false)
	{
		// get db
		$db = BackendModel::getDB();

		// we should include the count
		if($includeCount)
		{
			return (array) $db->getPairs('SELECT i.id, CONCAT(i.title, " (",  COUNT(p.category_id) ,")") AS title
															FROM faq_categories AS i
															LEFT OUTER JOIN faq_questions AS p ON i.id = p.category_id AND i.language = p.language
															WHERE i.language = ?
															GROUP BY i.id',
															array(BL::getWorkingLanguage()));
		}

		// get records and return them
		return (array) $db->getPairs('SELECT i.id, i.title
														FROM faq_categories AS i
														WHERE i.language = ?',
														array(BL::getWorkingLanguage()));
	}


	/**
	 * Get all data for a given category
	 *
	 * @return	array
	 * @param	int $id		The id of the category to fetch.
	 */
	public static function getCategory($id)
	{
		return (array) BackendModel::getDB()->getRecord('SELECT i.*
															FROM faq_categories AS i
															WHERE i.id = ? AND i.language = ?',
															array((int) $id, BL::getWorkingLanguage()));
	}


	/**
	 * Get the max sequence id for category
	 *
	 * @return	int
	 */
	public static function getMaximumCategorySequence()
	{
		return (int) BackendModel::getDB()->getVar('SELECT MAX(i.sequence)
													FROM faq_categories AS i
													WHERE i.language = ?',
													array(BL::getWorkingLanguage()));
	}


	/**
	 * Get the max sequence id for question
	 *
	 * @return	int
	 * @param	int $id		The category id.
	 */
	public static function getMaximumSequence($id)
	{
		return (int) BackendModel::getDB()->getVar('SELECT MAX(i.sequence)
													FROM faq_questions AS i
													WHERE i.category_id = ?',
													array((int) $id));
	}


	/**
	 * Retrieve the unique URL for an item
	 *
	 * @return	string
	 * @param	string $URL			The URL to base on.
	 * @param	int[optional] $id	The id of the item to ignore.
	 */
	public static function getURL($URL, $id = null)
	{
		// redefine URL
		$URL = SpoonFilter::urlise((string) $URL);

		// get db
		$db = BackendModel::getDB();

		// new item
		if($id === null)
		{
			// get number of categories with this URL
			$number = (int) $db->getVar('SELECT COUNT(i.id)
											FROM faq_questions AS i
											INNER JOIN meta AS m ON i.meta_id = m.id
											WHERE i.language = ? AND m.url = ?',
											array(BL::getWorkingLanguage(), $URL));

			// already exists
			if($number != 0)
			{
				// add number
				$URL = BackendModel::addNumber($URL);

				// try again
				return self::getURL($URL);
			}
		}

		// current category should be excluded
		else
		{
			// get number of items with this URL
			$number = (int) $db->getVar('SELECT COUNT(i.id)
											FROM faq_questions AS i
											INNER JOIN meta AS m ON i.meta_id = m.id
											WHERE i.language = ? AND m.url = ? AND i.id != ?',
											array(BL::getWorkingLanguage(), $URL, $id));

			// already exists
			if($number != 0)
			{
				// add number
				$URL = BackendModel::addNumber($URL);

				// try again
				return self::getURL($URL, $id);
			}
		}

		// return the unique URL!
		return $URL;
	}


	/**
	 * Retrieve the unique URL for a category
	 *
	 * @return	string
	 * @param	string $URL			The string wheron the URL will be based.
	 * @param	int[optional] $id	The id of the category to ignore.
	 */
	public static function getURLForCategory($URL, $id = null)
	{
		// redefine URL
		$URL = SpoonFilter::urlise((string) $URL);

		// get db
		$db = BackendModel::getDB();

		// new category
		if($id === null)
		{
			// get number of categories with this URL
			$number = (int) $db->getVar('SELECT COUNT(i.id)
											FROM faq_categories AS i
											INNER JOIN meta AS m ON i.meta_id = m.id
											WHERE i.language = ? AND m.url = ?',
											array(BL::getWorkingLanguage(), $URL));

			// already exists
			if($number != 0)
			{
				// add number
				$URL = BackendModel::addNumber($URL);

				// try again
				return self::getURLForCategory($URL);
			}
		}

		// current category should be excluded
		else
		{
			// get number of items with this URL
			$number = (int) $db->getVar('SELECT COUNT(i.id)
											FROM faq_categories AS i
											INNER JOIN meta AS m ON i.meta_id = m.id
											WHERE i.language = ? AND m.url = ? AND i.id != ?',
											array(BL::getWorkingLanguage(), $URL, $id));

			// already exists
			if($number != 0)
			{
				// add number
				$URL = BackendModel::addNumber($URL);

				// try again
				return self::getURLForCategory($URL, $id);
			}
		}

		// return the unique URL!
		return $URL;
	}


	/**
	 * Inserts an item into the database
	 *
	 * @return	int
	 * @param	array $item		The data to insert.
	 */
	public static function insert(array $item)
	{
		// insert
		$insertId = BackendModel::getDB(true)->insert('faq_questions', $item);

		// invalidate the cache for blog
		BackendModel::invalidateFrontendCache('blog', BL::getWorkingLanguage());

		// return the id
		return $insertId;
	}


	/**
	 * Inserts a new category into the database
	 *
	 * @return	int
	 * @param	array $item				The data for the category to insert.
	 * @param	array[optional] $meta	The metadata for the category to insert.
	 */
	public static function insertCategory(array $item, $meta = null)
	{
		// get db
		$db = BackendModel::getDB(true);

		// meta given?
		if($meta !== null) $item['meta_id'] = $db->insert('meta', $meta);

		// create category
		$item['id'] = $db->insert('faq_categories', $item);

		// invalidate the cache for faq
		BackendModel::invalidateFrontendCache('faq', BL::getWorkingLanguage());

		// return the id
		return $item['id'];
	}


	/**
	 * Update an existing item
	 *
	 * @return	int
	 * @param	array $item		The new data.
	 */
	public static function update(array $item)
	{
		// update
		BackendModel::getDB(true)->update('faq_questions', $item, 'id = ?', array((int) $item['id']));

		// invalidate the cache for blog
		BackendModel::invalidateFrontendCache('faq', BL::getWorkingLanguage());
	}


	/**
	 * Update an existing category
	 *
	 * @return	int
	 * @param	array $item	The updated values.
	 */
	public static function updateCategory(array $item)
	{
		// update
		BackendModel::getDB(true)->update('faq_categories', $item, 'id = ?', array($item['id']));

		// invalidate the cache for blog
		BackendModel::invalidateFrontendCache('faq', BL::getWorkingLanguage());
	}
}

?>
