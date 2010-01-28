<?php

/**
 * BackendBlogModel
 *
 * In this file we store all generic functions that we will be using in the blog module
 *
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendBlogModel
{
	const QRY_DATAGRID_BROWSE = "SELECT
								p.id,
								us.value AS author,
								us.user_id AS author_id,
								p.title,
								UNIX_TIMESTAMP(p.publish_on) AS publish_on,
								COUNT(bc.id) AS comments
								FROM blog_posts AS p
								LEFT OUTER JOIN blog_comments AS bc ON bc.post_id = p.id
								INNER JOIN users_settings AS us ON us.user_id = p.user_id AND us.name = 'nickname'
								WHERE p.status = ?
								GROUP BY p.id
								";
	const QRY_DATAGRID_BROWSE_CATEGORIES = 'SELECT
											c.id,
											c.name,
											COUNT(p.id) AS num_posts
											FROM blog_categories AS c
											LEFT OUTER JOIN blog_posts AS p ON p.category_id = c.id
											WHERE c.language = ?
											GROUP BY c.id';
	const QRY_DATAGRID_BROWSE_COMMENTS = 'SELECT id, UNIX_TIMESTAMP(created_on) AS created_on, author, text FROM blog_comments WHERE status = ?;';
	const QRY_DATAGRID_BROWSE_REVISIONS = 'SELECT p.id, p.revision_id, p.title, UNIX_TIMESTAMP(p.edited_on) AS edited_on
									FROM blog_posts AS p
									WHERE p.status = ? AND p.id = ?
									ORDER BY p.edited_on DESC;';

	/**
	 * Checks the settings and optionally returns an array with warnings
	 *
	 * @return	array
	 */
	public static function checkSettings()
	{
		// init var
		$warnings = array();

		// blog rss title
		if(BackendModel::getSetting('blog', 'rss_title_'. BL::getWorkingLanguage(), null) == '')
		{
			// add warning
			$warnings[] = array('message' => sprintf(BL::getError('BlogRSSTitle'), BackendModel::createURLForAction('settings', 'blog')));
		}

		// blog rss description
		if(BackendModel::getSetting('blog', 'rss_description_'. BL::getWorkingLanguage(), null) == '')
		{
			// add warning
			$warnings[] = array('message' => sprintf(BL::getError('BlogRSSDescription'), BackendModel::createURLForAction('settings', 'blog')));
		}

		return $warnings;
	}


	/**
	 * Deletes one or more blogposts
	 *
	 * @return	void
	 * @param array $ids
	 */
	public static function delete(array $ids)
	{
		// get db
		$db = BackendModel::getDB();

		// delete blogpost records
		$db->execute('DELETE p, m FROM blog_posts AS p INNER JOIN meta AS m WHERE m.id = p.meta_id AND p.id IN('. implode(',', $ids) .');');
		$db->execute('DELETE FROM blog_comments WHERE post_id IN('. implode(',', $ids) .');');
	}


	/**
	 * Deletes a category
	 *
	 * @return	void
	 * @param	int $id
	 */
	public static function deleteCategory($id)
	{
		// get db
		$db = BackendModel::getDB();

		// delete category
		$db->delete('blog_categories', 'id = ?', (int) $id);

		// default category
		$defaultCategoryId = BackendModel::getSetting('blog', 'default_category_'. BL::getWorkingLanguage(), null);

		// update category for the posts that might be in this category
		$db->update('blog_posts', array('category_id' => $defaultCategoryId), 'category_id = ?', (int) $defaultCategoryId);
	}


	/**
	 * Deletes one or more comments
	 *
	 * @return	void
	 * @param	array $ids
	 */
	public static function deleteComments(array $ids)
	{
		// get db
		$db = BackendModel::getDB();

		// update record
		$db->execute('DELETE FROM blog_comments WHERE id IN('. implode(',', $ids) .');');
	}


	/**
	 * Checks if a blogpost exists
	 *
	 * @return	int
	 * @param int $id
	 */
	public static function exists($id)
	{
		// get db
		$db = BackendModel::getDB();

		// exists?
		return $db->getNumRows('SELECT id FROM blog_posts WHERE id = ?;', (int) $id);
	}


	/**
	 * Checks if a category exists
	 *
	 * @return	int
	 * @param int $id
	 */
	public static function existsCategory($id)
	{
		// get db
		$db = BackendModel::getDB();

		// exists?
		return $db->getNumRows('SELECT id FROM blog_categories WHERE id = ?;', (int) $id);
	}


	/**
	 * Returns the HTML for a user avatar + his name
	 *
	 * @return	void
	 * @param 	string $author
	 * @param 	string $authorId
	 */
	public static function getAuthorHTML($author, $authorId)
	{
		// fetch the user for this ID
		$record = new BackendUser($authorId);
		$avatar = SITE_URL . FRONTEND_FILES_URL .'/backend_users/avatars/32x32/'. $record->getSetting('avatar');
		$author = unserialize($author);

		// return the img + avatar
		return '<div class="user"><a href="#"><img src="'. $avatar .'" alt="'. $author .'" width="24" height="24" /> '. $author .'</a></div>';
	}


	/**
	 * Get all categories
	 *
	 * @return	array
	 * @param	int $id
	 */
	public static function getCategories()
	{
		// get db
		$db = BackendModel::getDB();

		// get records and return them
		return (array) $db->getPairs('SELECT c.id, c.name FROM blog_categories AS c;');
	}


	/**
	 * Get all data for a given id
	 *
	 * @return	array
	 * @param	int $id
	 */
	public static function getCategory($id)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = BackendModel::getDB();

		// get record and return it
		return (array) $db->getRecord('SELECT * FROM blog_categories
										WHERE id = ?;', (int) $id);
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
		return (array) $db->getRecord('SELECT p.*,
									   UNIX_TIMESTAMP(p.publish_on) AS publish_on,
									   UNIX_TIMESTAMP(p.created_on) AS created_on,
									   UNIX_TIMESTAMP(p.edited_on) AS edited_on,
									   m.url
									   FROM blog_posts AS p
									   INNER JOIN meta AS m ON m.id = p.meta_id
									   WHERE p.id = ? AND status = ?
									   LIMIT 1;', array($id, 'active'));
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
		return (array) $db->getRecord('SELECT p.*,
									   UNIX_TIMESTAMP(p.publish_on) AS publish_on,
									   UNIX_TIMESTAMP(p.created_on) AS created_on,
									   UNIX_TIMESTAMP(p.edited_on) AS edited_on,
									   m.url
									   FROM blog_posts AS p
									   INNER JOIN meta AS m ON m.id = p.meta_id
									   WHERE p.id = ? AND p.revision_id = ?;', array($id, $revisionId));
	}


	/**
	 * Retrieve the unique url for an item
	 *
	 * @return	string
	 * @param	int[optional] $itemId
	 */
	public static function getURL($URL, $itemId = null)
	{
		// redefine URL
		$URL = SpoonFilter::urlise((string) $URL);

		// get db
		$db = BackendModel::getDB();

		// new item
		if($itemId === null)
		{
			// get number of categories with this URL
			$number = (int) $db->getNumRows('SELECT p.id
												FROM blog_posts AS p
												INNER JOIN meta AS m ON p.meta_id = m.id
												WHERE p.language = ? AND m.url = ?;', array(BL::getWorkingLanguage(), $URL));

			// already exists
			if($number != 0)
			{
				// add  number
				$URL = BackendModel::addNumber($URL);

				// try again
				return self::getURL($URL);
			}
		}

		// current category should be excluded
		else
		{
			// get number of items with this url
			$number = (int) $db->getNumRows('SELECT p.id
												FROM blog_posts AS p
												INNER JOIN meta AS m ON p.meta_id = m.id
												WHERE p.language = ? AND m.url = ? AND p.id != ?;', array(BL::getWorkingLanguage(), $URL, $itemId));

			// already exists
			if($number != 0)
			{
				// add  number
				$URL = BackendModel::addNumber($URL);

				// try again
				return self::getURL($URL, $itemId);
			}
		}

		return $URL;
	}


	/**
	 * Retrieve the unique url for a category
	 *
	 * @return	string
	 * @param	int[optional] $categoryId
	 */
	public static function getURLForCategory($URL, $categoryId = null)
	{
		// redefine URL
		$URL = SpoonFilter::urlise((string) $URL);

		// get db
		$db = BackendModel::getDB();

		// new category
		if($categoryId === null)
		{
			// get number of categories with this URL
			$number = (int) $db->getNumRows('SELECT c.id FROM blog_categories AS c WHERE c.language = ? AND c.url = ?;', array(BL::getWorkingLanguage(), $URL));

			// already exists
			if($number != 0)
			{
				// add  number
				$URL = BackendModel::addNumber($URL);

				// try again
				return self::getURLForCategory($URL);
			}
		}

		// current category should be excluded
		else
		{
			// get number of items with this url
			$number = (int) $db->getNumRows('SELECT c.id FROM blog_categories AS C WHERE c.language = ? AND c.url = ? AND c.id != ?;', array(BL::getWorkingLanguage(), $URL, $categoryId));

			// already exists
			if($number != 0)
			{
				// add  number
				$URL = BackendModel::addNumber($URL);

				// try again
				return self::getURLForCategory($URL, $categoryId);
			}
		}

		return $URL;
	}


	/**
	 * Inserts a blogpost into the database
	 *
	 * @return	int
	 * @param	array $item
	 */
	public static function insert(array $item)
	{
		// get db
		$db = BackendModel::getDB();

		// calculate new id
		$newId = (int) $db->getVar('SELECT MAX(id) FROM blog_posts LIMIT 1;') + 1;

		// build array
		$item['id'] = $newId;
		$item['revision_id'] = $newId;

		// insert and return the insertId
		$db->insert('blog_posts', $item);

		// return the new id
		return $newId;
	}


	/**
	 * Inserts a new category into the database
	 *
	 * @return	int
	 * @param	array $item
	 */
	public static function insertCategory(array $item)
	{
		// get db
		$db = BackendModel::getDB();

		// create category
		return $db->insert('blog_categories', $item);
	}


	/**
	 * Update an existing blogpost
	 *
	 * @return	int
	 * @param	int $id
	 * @param	array $item
	 */
	public static function update($id, array $item)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = BackendModel::getDB();

		// get current version
		$version = self::get($id);

		// build array
		$item['id'] = $id;
		$item['status'] = 'active';
		$item['language'] = $version['language'];
		$item['edited_on'] = date('Y-m-d H:i:s');

		// archive all older versions
		$db->update('blog_posts', array('status' => 'archived'), 'id = ?', array($id));

		// insert new version
		$db->insert('blog_posts', $item);

		// how many revisions should we keep
		$rowsToKeep = (int) BackendModel::getSetting('blog', 'maximum_number_of_revisions', 5);

		// get revision-ids for items to keep
		$revisionIdsToKeep = (array) $db->getColumn('SELECT p.revision_id
													 FROM blog_posts AS p
													 WHERE p.id = ? AND p.status = ?
													 ORDER BY p.edited_on DESC
													 LIMIT ?;',
													 array($id, 'archived', $rowsToKeep));

		// delete other revisions
		if(!empty($revisionIdsToKeep)) $db->delete('blog_posts', 'id = ? AND status = ? AND revision_id NOT IN('. implode(', ', $revisionIdsToKeep) .')', array($id, 'archived'));

		// return the id
		return $id;
	}


	/**
	 * Update an existing category
	 *
	 * @return	int
	 * @param	int $id
	 * @param	array $item
	 */
	public static function updateCategory($id, array $item)
	{
		// get db
		$db = BackendModel::getDB();

		// update category
		$db->update('blog_categories', $item, 'id = ?', (int) $id);
	}


	/**
	 * Updates one or more comments' status
	 *
	 * @return	void
	 * @param	array $ids
	 * @param	string $status
	 */
	public static function updateCommentStatuses(array $ids, $status)
	{
		// get db
		$db = BackendModel::getDB();

		// update record
		$db->execute('UPDATE blog_comments SET status = ? WHERE id IN('. implode(',', $ids) .');', $status);
	}
}

?>