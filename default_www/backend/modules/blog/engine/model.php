<?php

/**
 * BackendBlogModel
 * In this file we store all generic functions that we will be using in the blog module
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
	const QRY_DATAGRID_BROWSE = 'SELECT i.id, i.user_id, i.title, UNIX_TIMESTAMP(i.publish_on) AS publish_on, i.num_comments AS comments
								FROM blog_posts AS i
								WHERE i.status = ?';
	const QRY_DATAGRID_BROWSE_CATEGORIES = 'SELECT i.id, i.name
											FROM blog_categories AS i
											WHERE i.language = ?';
	const QRY_DATAGRID_BROWSE_COMMENTS = 'SELECT i.id, UNIX_TIMESTAMP(i.created_on) AS created_on, i.author, i.text,
											p.id AS post_id, p.title AS post_title, m.url AS post_url
											FROM blog_comments AS i
											INNER JOIN blog_posts AS p ON i.post_id = p.id
											INNER JOIN meta AS m ON p.meta_id = m.id
											WHERE i.status = ?
											GROUP BY i.id';
	const QRY_DATAGRID_BROWSE_DRAFTS = 'SELECT i.id, i.user_id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.num_comments AS comments
										FROM blog_posts AS i
										WHERE i.status = ? AND i.user_id = ?';
	const QRY_DATAGRID_BROWSE_RECENT = 'SELECT i.id, i.user_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.num_comments AS comments
										FROM blog_posts AS i
										WHERE i.status = ?
										ORDER BY i.edited_on DESC
										LIMIT 4';
	const QRY_DATAGRID_BROWSE_REVISIONS = 'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on
											FROM blog_posts AS i
											WHERE i.status = ? AND i.id = ?
											ORDER BY i.edited_on DESC';
	const QRY_DATAGRID_BROWSE_SPECIFIC_DRAFTS = 'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on
													FROM blog_posts AS i
													WHERE i.status = ? AND i.id = ? AND i.user_id = ?
													ORDER BY i.edited_on DESC';


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

		// return
		return $warnings;
	}


	/**
	 * Deletes one or more blogposts
	 *
	 * @return	void
	 * @param 	mixed $ids	The ids to delete.
	 */
	public static function delete($ids)
	{
		// get db
		$db = BackendModel::getDB(true);

		// if $ids is not an array, make one
		$ids = (!is_array($ids)) ? array($ids) : $ids;

		// delete blogpost records
		$db->execute('DELETE FROM blog_posts WHERE id IN('. implode(',', $ids) .');');
		$db->execute('DELETE FROM blog_comments WHERE post_id IN('. implode(',', $ids) .');');
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

		// delete category
		$db->delete('blog_categories', 'id = ?', $id);

		// default category
		$defaultCategoryId = BackendModel::getSetting('blog', 'default_category_'. BL::getWorkingLanguage(), null);

		// update category for the posts that might be in this category
		$db->update('blog_posts', array('category_id' => $defaultCategoryId), 'category_id = ?', $defaultCategoryId);
	}


	/**
	 * Deletes one or more comments
	 *
	 * @return	void
	 * @param	array $ids	The id(s) of the comment(s) to delete.
	 */
	public static function deleteComments(array $ids)
	{
		// get db
		$db = BackendModel::getDB(true);

		// get blogpost ids
		$postIds = (array) $db->getColumn('SELECT i.post_id
											FROM blog_comments AS i
											WHERE i.id IN('. implode(',', $ids) .');');

		// update record
		$db->execute('DELETE FROM blog_comments
						WHERE id IN('. implode(',', $ids) .');');


		// recalculate the comment count
		if(!empty($postIds)) self::reCalculateCommentCount($postIds);
	}


	/**
	 * Checks if a blogpost exists
	 *
	 * @return	int
	 * @param	int $id		The id of the blogpost to check for existence.
	 */
	public static function exists($id)
	{
		// exists?
		return BackendModel::getDB()->getNumRows('SELECT i.id
													FROM blog_posts AS i
													WHERE i.id = ?;',
													(int) $id);
	}


	/**
	 * Checks if a category exists
	 *
	 * @return	int
	 * @param	int $id		The id of the category to check for existence.
	 */
	public static function existsCategory($id)
	{
		// exists?
		return BackendModel::getDB()->getNumRows('SELECT id AS i
													FROM blog_categories AS i
													WHERE i.id = ?;', (int) $id);
	}


	/**
	 * Get all data for a given id
	 *
	 * @return	array
	 * @param	int $id		The Id of the blogpost to fetch?
	 */
	public static function get($id)
	{
		// redefine
		$id = (int) $id;

		// get record and return it
		return (array) $db = BackendModel::getDB()->getRecord('SELECT i.*, UNIX_TIMESTAMP(i.publish_on) AS publish_on, UNIX_TIMESTAMP(i.created_on) AS created_on, UNIX_TIMESTAMP(i.edited_on) AS edited_on,
																m.url
																FROM blog_posts AS i
																INNER JOIN meta AS m ON m.id = i.meta_id
																WHERE i.id = ? AND i.status = ?
																LIMIT 1;',
																array($id, 'active'));
	}


	/**
	 * Get all categories
	 *
	 * @return	array
	 */
	public static function getCategories()
	{
		// get records and return them
		$categories = (array) BackendModel::getDB()->getPairs('SELECT i.id, i.name
																FROM blog_categories AS i;');

		// no categories?
		if(empty($categories))
		{
			// build array
			$category['language'] = BL::getWorkingLanguage();
			$category['name'] = 'default';
			$category['url'] = 'default';

			// insert category
			$id = self::insertCategory($category);

			// store in settings
			BackendModel::setSetting('blog', 'default_category_'. BL::getWorkingLanguage(), $id);

			// recall
			return self::getCategories();
		}

		// return the categories
		return $categories;
	}


	/**
	 * Get all data for a given id
	 *
	 * @return	array
	 * @param	int $id		The id of the category to fetch.
	 */
	public static function getCategory($id)
	{
		// get record and return it
		return (array) BackendModel::getDB()->getRecord('SELECT i.*
															FROM blog_categories AS i
															WHERE i.id = ?;', (int) $id);
	}


	/**
	 * Get a category id by name
	 *
	 * @return	int
	 * @param	string $name					The name of the category.
	 * @param	string[optional] $language		The language to use, if not provided we will use the working language.
	 */
	public static function getCategoryId($name, $language = null)
	{
		// redefine
		$name = (string) $name;
		$language = ($language !== null) ? (string) $language : BackendLanguage::getWorkingLanguage();

		// exists?
		return (int) BackendModel::getDB()->getVar('SELECT i.id
													FROM blog_categories AS i
													WHERE i.name = ? AND i.language = ?;',
													array($name, $language));
	}


	/**
	 * Get a draft
	 *
	 * @return	array
	 * @param	int $id			The id of the post.
	 * @param	int $draftId	The draft
	 */
	public static function getDraft($id, $draftId)
	{
		// redefine
		$id = (int) $id;
		$draftId = (int) $draftId;

		// get record and return it
		return (array) BackendModel::getDB()->getRecord('SELECT i.*, UNIX_TIMESTAMP(i.publish_on) AS publish_on, UNIX_TIMESTAMP(i.created_on) AS created_on, UNIX_TIMESTAMP(i.edited_on) AS edited_on,
														m.url
														FROM blog_posts AS i
														INNER JOIN meta AS m ON m.id = i.meta_id
														WHERE i.id = ? AND i.revision_id = ?;',
														array($id, $draftId));
	}


	/**
	 * Get the latest comments for a given type
	 *
	 * @return	array
	 * @param	string $status			The status for the comments to retrieve.
	 * @param	int[optional] $limit	The maximum number of items to retrieve.
	 */
	public static function getLatestComments($status, $limit = 10)
	{
		// redefine
		$status = (string) $status;
		$limit = (int) $limit;

		// return the comments (order by id, this is faster then on date, the higher the id, the more recent
		$return = (array) BackendModel::getDB()->getRecords('SELECT i.id, i.author, i.text, UNIX_TIMESTAMP(i.created_on) AS created_in,
																	p.title, p.language, m.url
																FROM blog_comments AS i
																INNER JOIN blog_posts AS p ON i.post_id = p.id
																INNER JOIN meta AS m ON p.meta_id = m.id
																WHERE i.status = ? AND p.status = ?
																ORDER BY i.id DESC
																LIMIT ?;',
																array($status, 'active', $limit));

		// loop entries
		foreach($return as $key => &$row)
		{
			// add full url
			$row['full_url'] = BackendModel::getURLForBlock('blog', 'detail', $row['language']) .'/'. $row['url'];
		}

		// return
		return $return;
	}


	/**
	 * Get the maximum id
	 *
	 * @return	int
	 */
	public static function getMaximumId()
	{
		// return
		return (int) BackendModel::getDB()->getVar('SELECT MAX(id) FROM blog_posts LIMIT 1;');
	}


	/**
	 * Get all data for a given revision
	 *
	 * @return	array
	 * @param	int $id				The id of the blogpost.
	 * @param	int $revisionId		The revision to get.
	 */
	public static function getRevision($id, $revisionId)
	{
		// redefine
		$id = (int) $id;
		$revisionId = (int) $revisionId;

		// get record and return it
		return (array) BackendModel::getDB()->getRecord('SELECT i.*, UNIX_TIMESTAMP(i.publish_on) AS publish_on, UNIX_TIMESTAMP(i.created_on) AS created_on, UNIX_TIMESTAMP(i.edited_on) AS edited_on, m.url
															FROM blog_posts AS i
															INNER JOIN meta AS m ON m.id = i.meta_id
															WHERE i.id = ? AND i.revision_id = ?;',
															array($id, $revisionId));
	}


	/**
	 * Get a count per comment
	 *
	 * @return	array
	 */
	public static function getCommentStatusCount()
	{
		// return
		return (array) BackendModel::getDB()->getPairs('SELECT i.status, COUNT(i.id)
															FROM blog_comments AS i
															GROUP BY i.status;');
	}


	/**
	 * Retrieve the unique URL for an item
	 *
	 * @return	string						The URL to base on.
	 * @param	int[optional] $itemId		The id of the blogpost to ignore.
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
			$number = (int) $db->getNumRows('SELECT i.id
												FROM blog_posts AS i
												INNER JOIN meta AS m ON i.meta_id = m.id
												WHERE i.language = ? AND m.url = ?;',
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
			$number = (int) $db->getNumRows('SELECT i.id
												FROM blog_posts AS i
												INNER JOIN meta AS m ON i.meta_id = m.id
												WHERE i.language = ? AND m.url = ? AND i.id != ?;',
												array(BL::getWorkingLanguage(), $URL, $itemId));

			// already exists
			if($number != 0)
			{
				// add number
				$URL = BackendModel::addNumber($URL);

				// try again
				return self::getURL($URL, $itemId);
			}
		}

		return $URL;
	}


	/**
	 * Retrieve the unique URL for a category
	 *
	 * @return	string
	 * @param	string $URL						The string wheron the URL will be based.
	 * @param	int[optional] $categoryId		The id of the category to ignore.
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
			$number = (int) $db->getNumRows('SELECT i.id
												FROM blog_categories AS i
												WHERE i.language = ? AND i.url = ?;',
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
			$number = (int) $db->getNumRows('SELECT i.id
												FROM blog_categories AS i
												WHERE i.language = ? AND i.url = ? AND i.id != ?;',
												array(BL::getWorkingLanguage(), $URL, $categoryId));

			// already exists
			if($number != 0)
			{
				// add number
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
	 * @param	array $item		The data to insert.
	 */
	public static function insert(array $item)
	{
		// get db
		$db = BackendModel::getDB(true);

		// calculate new id
		$newId = (int) $db->getVar('SELECT MAX(id) FROM blog_posts LIMIT 1;') + 1;

		// build array
		$item['id'] = $newId;

		// insert and return the insertId
		$db->insert('blog_posts', $item);

		// return the new id
		return $newId;
	}


	/**
	 * Inserts a new category into the database
	 *
	 * @return	int
	 * @param	array $item		The data for the category to insert.
	 */
	public static function insertCategory(array $item)
	{
		// create category
		return BackendModel::getDB(true)->insert('blog_categories', $item);
	}


	/**
	 * Insert a draft
	 *
	 * @return	int
	 * @param	int $id	The id of the blogpost.
	 * @param	array $item			The item to insert.
	 */
	public static function insertDraft($id, array $item)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = BackendModel::getDB(true);

		// remove old
		$db->delete('blog_posts', 'id = ? AND user_id = ? AND status = ?', array($id, BackendAuthentication::getUser()->getUserId(), 'draft'));

		// alter
		$item['id'] = $id;
		$item['status'] = 'draft';
		$item['edited_on'] = BackendModel::getUTCDate();

		// insert and return the insertId
		$newId = $db->insert('blog_posts', $item);

		// return the new id
		return $newId;
	}


	/**
	 * Recalculate the commentcount
	 *
	 * @return	bool
	 * @param	array $ids	The id(s) of the post wherefor the commentcount should be recalculated.
	 */
	public static function reCalculateCommentCount(array $ids)
	{
		// init var
		$uniqueIds = array();

		// make unique ids
		foreach($ids as $id) if(!in_array($id, $uniqueIds)) $uniqueIds[] = $id;

		// validate
		if(empty($uniqueIds)) return false;

		// get db
		$db = BackendModel::getDB(true);

		// get counts
		$commentCounts = (array) $db->getPairs('SELECT i.post_id, COUNT(i.id) AS comment_count
												FROM blog_comments AS i
												WHERE i.status = ? AND i.post_id IN('. implode(',', $uniqueIds) .')
												GROUP BY i.post_id;',
												array('published'));

		// loop posts
		foreach($uniqueIds as $id)
		{
			// get count
			$count = (isset($commentCounts[$id])) ? (int) $commentCounts[$id] : 0;

			// update
			$db->update('blog_posts', array('num_comments' => $count), 'id = ?', array($id));
		}

		// return
		return true;
	}


	/**
	 * Update an existing blogpost
	 *
	 * @return	int
	 * @param	int $id			The id of the post to update.
	 * @param	array $item		The new data.
	 */
	public static function update($id, array $item)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = BackendModel::getDB(true);

		// get current version
		$version = self::get($id);

		// no previous version found (draft)
		if(empty($version)) $version['language'] = BackendLanguage::getWorkingLanguage();

		// build array
		$item['id'] = $id;
		$item['status'] = 'active';
		$item['language'] = $version['language'];
		$item['edited_on'] = BackendModel::getUTCDate();

		// archive all older versions
		$db->update('blog_posts', array('status' => 'archived'), 'id = ?', array($id));

		// insert new version
		$db->insert('blog_posts', $item);

		// how many revisions should we keep
		$rowsToKeep = (int) BackendModel::getSetting('blog', 'maximum_number_of_revisions', 5);

		// get revision-ids for items to keep
		$revisionIdsToKeep = (array) $db->getColumn('SELECT i.revision_id
													 FROM blog_posts AS i
													 WHERE i.id = ? AND i.status = ?
													 ORDER BY i.edited_on DESC
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
	 * @param	int $id			The id of the category to update.
	 * @param	array $item		The new data.
	 */
	public static function updateCategory($id, array $item)
	{
		// update category
		BackendModel::getDB(true)->update('blog_categories', $item, 'id = ?', (int) $id);
	}


	/**
	 * Updates one or more comments' status
	 *
	 * @return	void
	 * @param	array $ids			The id(s) of the comment(s) to change the status for.
	 * @param	string $status		The new status.
	 */
	public static function updateCommentStatuses(array $ids, $status)
	{
		// get db
		$db = BackendModel::getDB(true);

		// @later	if the new status is spam, we should submit it to Akismet!
		// get blogpost ids
		$postIds = (array) $db->getColumn('SELECT i.post_id
											FROM blog_comments AS i
											WHERE i.id IN('. implode(',', $ids) .');');

		// update record
		$db->execute('UPDATE blog_comments
						SET status = ?
						WHERE id IN('. implode(',', $ids) .');',
						$status);

		// recalculate the comment count
		if(!empty($postIds)) self::reCalculateCommentCount($postIds);
	}
}

?>