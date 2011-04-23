<?php

/**
 * In this file we store all generic functions that we will be using in the blog module
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Dave Lens <dave@netlash.com>
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
class BackendBlogModel
{
	const QRY_DATAGRID_BROWSE = 'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.publish_on) AS publish_on, i.user_id, i.num_comments AS comments
									FROM blog_posts AS i
									WHERE i.status = ? AND i.language = ?';
	const QRY_DATAGRID_BROWSE_FOR_CATEGORY = 'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.publish_on) AS publish_on, i.user_id, i.num_comments AS comments
												FROM blog_posts AS i
												WHERE i.category_id = ? AND i.status = ? AND i.language = ?';
	const QRY_DATAGRID_BROWSE_CATEGORIES = 'SELECT i.id, i.title, COUNT(p.id) AS num_items
											FROM blog_categories AS i
											LEFT OUTER JOIN blog_posts AS p ON i.id = p.category_id AND p.status = ? AND p.language = i.language
											WHERE i.language = ?
											GROUP BY i.id';
	const QRY_DATAGRID_BROWSE_COMMENTS = 'SELECT i.id, UNIX_TIMESTAMP(i.created_on) AS created_on, i.author, i.text,
											p.id AS post_id, p.title AS post_title, m.url AS post_url
											FROM blog_comments AS i
											INNER JOIN blog_posts AS p ON i.post_id = p.id AND i.language = p.language
											INNER JOIN meta AS m ON p.meta_id = m.id
											WHERE i.status = ? AND i.language = ?
											GROUP BY i.id';
	const QRY_DATAGRID_BROWSE_DRAFTS = 'SELECT i.id, i.user_id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.num_comments AS comments
										FROM blog_posts AS i
										INNER JOIN
										(
											SELECT MAX(i.revision_id) AS revision_id
											FROM blog_posts AS i
											WHERE i.status = ? AND i.user_id = ? AND i.language = ?
											GROUP BY i.id
										) AS p
										WHERE i.revision_id = p.revision_id';
	const QRY_DATAGRID_BROWSE_DRAFTS_FOR_CATEGORY = 'SELECT i.id, i.user_id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.num_comments AS comments
														FROM blog_posts AS i
														INNER JOIN
														(
															SELECT MAX(i.revision_id) AS revision_id
															FROM blog_posts AS i
															WHERE i.category_id = ? AND i.status = ? AND i.user_id = ? AND i.language = ?
															GROUP BY i.id
														) AS p
														WHERE i.revision_id = p.revision_id';
	const QRY_DATAGRID_BROWSE_RECENT = 'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id, i.num_comments AS comments
										FROM blog_posts AS i
										WHERE i.status = ? AND i.language = ?
										ORDER BY i.edited_on DESC
										LIMIT ?';
	const QRY_DATAGRID_BROWSE_RECENT_FOR_CATEGORY = 'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id, i.num_comments AS comments
													FROM blog_posts AS i
													WHERE i.category_id = ? AND i.status = ? AND i.language = ?
													ORDER BY i.edited_on DESC
													LIMIT ?';
	const QRY_DATAGRID_BROWSE_REVISIONS = 'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id
											FROM blog_posts AS i
											WHERE i.status = ? AND i.id = ? AND i.language = ?
											ORDER BY i.edited_on DESC';
	const QRY_DATAGRID_BROWSE_SPECIFIC_DRAFTS = 'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id
													FROM blog_posts AS i
													WHERE i.status = ? AND i.id = ? AND i.language = ?
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

		// rss title
		if(BackendModel::getModuleSetting('blog', 'rss_title_' . BL::getWorkingLanguage(), null) == '')
		{
			// add warning
			$warnings[] = array('message' => sprintf(BL::err('RSSTitle', 'blog'), BackendModel::createURLForAction('settings', 'blog')));
		}

		// rss description
		if(BackendModel::getModuleSetting('blog', 'rss_description_' . BL::getWorkingLanguage(), null) == '')
		{
			// add warning
			$warnings[] = array('message' => sprintf(BL::err('RSSDescription', 'blog'), BackendModel::createURLForAction('settings', 'blog')));
		}

		// return
		return $warnings;
	}


	/**
	 * Deletes one or more items
	 *
	 * @return	void
	 * @param 	mixed $ids		The ids to delete.
	 */
	public static function delete($ids)
	{
		// make sure $ids is an array
		$ids = (array) $ids;

		// loop and cast to integers
		foreach($ids as &$id) $id = (int) $id;

		// create an array with an equal amount of questionmarks as ids provided
		$idPlaceHolders = array_fill(0, count($ids), '?');

		// get db
		$db = BackendModel::getDB(true);

		// delete records
		$db->delete('blog_posts', 'id IN (' . implode(', ', $idPlaceHolders) . ') AND language = ?', array_merge($ids, array(BL::getWorkingLanguage())));
		$db->delete('blog_comments', 'post_id IN (' . implode(', ', $idPlaceHolders) . ') AND language = ?', array_merge($ids, array(BL::getWorkingLanguage())));

		// get used meta ids
		$metaIds = (array) $db->getColumn('SELECT meta_id
											FROM blog_posts AS p
											WHERE id IN (' . implode(', ', $idPlaceHolders) . ') AND language = ?', array_merge($ids, array(BL::getWorkingLanguage())));

		// delete meta
		if(!empty($metaIds)) $db->delete('meta', 'id IN (' . implode(',', $metaIds) . ')');

		// invalidate the cache for blog
		BackendModel::invalidateFrontendCache('blog', BL::getWorkingLanguage());
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
			$db->delete('blog_categories', 'id = ?', array($id));

			// update category for the posts that might be in this category
			$db->update('blog_posts', array('category_id' => null), 'category_id = ?', array($id));

			// invalidate the cache for blog
			BackendModel::invalidateFrontendCache('blog', BL::getWorkingLanguage());
		}
	}


	/**
	 * Deletes one or more comments
	 *
	 * @return	void
	 * @param	array $ids		The id(s) of the items(s) to delete.
	 */
	public static function deleteComments($ids)
	{
		// make sure $ids is an array
		$ids = (array) $ids;

		// loop and cast to integers
		foreach($ids as &$id) $id = (int) $id;

		// create an array with an equal amount of questionmarks as ids provided
		$idPlaceHolders = array_fill(0, count($ids), '?');

		// get db
		$db = BackendModel::getDB(true);

		// get ids
		$itemIds = (array) $db->getColumn('SELECT i.post_id
											FROM blog_comments AS i
											WHERE i.id IN (' . implode(', ', $idPlaceHolders) . ') AND i.language = ?', array_merge($ids, array(BL::getWorkingLanguage())));

		// update record
		$db->delete('blog_comments', 'id IN (' . implode(', ', $idPlaceHolders) . ') AND language = ?', array_merge($ids, array(BL::getWorkingLanguage())));

		// recalculate the comment count
		if(!empty($itemIds)) self::reCalculateCommentCount($itemIds);

		// invalidate the cache for blog
		BackendModel::invalidateFrontendCache('blog', BL::getWorkingLanguage());
	}


	/**
	 * Delete all spam
	 *
	 * @return	void
	 */
	public static function deleteSpamComments()
	{
		// get db
		$db = BackendModel::getDB(true);

		// get ids
		$itemIds = (array) $db->getColumn('SELECT i.post_id
											FROM blog_comments AS i
											WHERE status = ? AND i.language = ?', array('spam', BL::getWorkingLanguage()));

		// update record
		$db->delete('blog_comments', 'status = ? AND language = ?', array('spam', BL::getWorkingLanguage()));

		// recalculate the comment count
		if(!empty($itemIds)) self::reCalculateCommentCount($itemIds);

		// invalidate the cache for blog
		BackendModel::invalidateFrontendCache('blog', BL::getWorkingLanguage());
	}


	/**
	 * Checks if an item exists
	 *
	 * @return	bool
	 * @param	int $id		The id of the item to check for existence.
	 */
	public static function exists($id)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT i.id
														FROM blog_posts AS i
														WHERE i.id = ? AND i.language = ?',
														array((int) $id, BL::getWorkingLanguage()));
	}


	/**
	 * Checks if a category exists
	 *
	 * @return	int
	 * @param	int $id		The id of the category to check for existence.
	 */
	public static function existsCategory($id)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT COUNT(id)
														FROM blog_categories AS i
														WHERE i.id = ? AND i.language = ?',
														array((int) $id, BL::getWorkingLanguage()));
	}


	/**
	 * Checks if a comment exists
	 *
	 * @return	int
	 * @param	int $id		The id of the item to check for existence.
	 */
	public static function existsComment($id)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT COUNT(id)
														FROM blog_comments AS i
														WHERE i.id = ? AND i.language = ?',
														array((int) $id, BL::getWorkingLanguage()));
	}


	/**
	 * Get all data for a given id
	 *
	 * @return	array
	 * @param	int $id		The Id of the item to fetch?
	 */
	public static function get($id)
	{
		return (array) BackendModel::getDB()->getRecord('SELECT i.*, UNIX_TIMESTAMP(i.publish_on) AS publish_on, UNIX_TIMESTAMP(i.created_on) AS created_on, UNIX_TIMESTAMP(i.edited_on) AS edited_on,
															m.url
															FROM blog_posts AS i
															INNER JOIN meta AS m ON m.id = i.meta_id
															WHERE i.id = ? AND i.status = ? AND i.language = ?',
															array((int) $id, 'active', BL::getWorkingLanguage()));
	}


	/**
	 * Get the comments
	 *
	 * @return	array
	 * @param	string[optional] $status		The type of comments to get.
	 * @param	int[optional] $limit			The maximum number of items to retrieve.
	 * @param	int[optional] $offset			The offset.
	 */
	public static function getAllCommentsForStatus($status, $limit = 30, $offset = 0)
	{
		// redefine
		if($status !== null) $status = (string) $status;
		$limit = (int) $limit;
		$offset = (int) $offset;

		// no status passed
		if($status === null)
		{
			// get data and return it
			return (array) BackendModel::getDB()->getRecords('SELECT i.id, UNIX_TIMESTAMP(i.created_on) AS created_on, i.author, i.email, i.website, i.text, i.type, i.status,
																p.id AS post_id, p.title AS post_title, m.url AS post_url, p.language AS post_language
																FROM blog_comments AS i
																INNER JOIN blog_posts AS p ON i.post_id = p.id AND i.language = p.language
																INNER JOIN meta AS m ON p.meta_id = m.id
																WHERE i.language = ?
																GROUP BY i.id
																LIMIT ?, ?',
																array(BL::getWorkingLanguage(), $offset, $limit));
		}

		// get data and return it
		return (array) BackendModel::getDB()->getRecords('SELECT i.id, UNIX_TIMESTAMP(i.created_on) AS created_on, i.author, i.email, i.website, i.text, i.type, i.status,
															p.id AS post_id, p.title AS post_title, m.url AS post_url, p.language AS post_language
															FROM blog_comments AS i
															INNER JOIN blog_posts AS p ON i.post_id = p.id AND i.language = p.language
															INNER JOIN meta AS m ON p.meta_id = m.id
															WHERE i.status = ? AND i.language = ?
															GROUP BY i.id
															LIMIT ?, ?',
															array($status, BL::getWorkingLanguage(), $offset, $limit));
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
		$items = (array) BackendModel::getDB()->getRecords('SELECT i.id AS url, i.title AS name, mt.module
															FROM modules_tags AS mt
															INNER JOIN tags AS t ON mt.tag_id = t.id
															INNER JOIN blog_posts AS i ON mt.other_id = i.id
															WHERE mt.module = ? AND mt.tag_id = ? AND i.status = ? AND i.language = ?',
															array('blog', (int) $tagId, 'active', BL::getWorkingLanguage()));

		// loop items and create url
		foreach($items as &$row) $row['url'] = BackendModel::createURLForAction('edit', 'blog', null, array('id' => $row['url']));

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
			return (array) BackendModel::getDB()->getPairs('SELECT i.id, CONCAT(i.title, " (",  COUNT(p.category_id) ,")") AS title
															FROM blog_categories AS i
															LEFT OUTER JOIN blog_posts AS p ON i.id = p.category_id AND i.language = p.language AND p.status = ?
															WHERE i.language = ?
															GROUP BY i.id',
															array('active', BL::getWorkingLanguage()));
		}

		// get records and return them
		return (array) BackendModel::getDB()->getPairs('SELECT i.id, i.title
														FROM blog_categories AS i
														WHERE i.language = ?',
														array(BL::getWorkingLanguage()));
	}


	/**
	 * Get all data for a given id
	 *
	 * @return	array
	 * @param	int $id		The id of the category to fetch.
	 */
	public static function getCategory($id)
	{
		return (array) BackendModel::getDB()->getRecord('SELECT i.*
															FROM blog_categories AS i
															WHERE i.id = ? AND i.language = ?',
															array((int) $id, BL::getWorkingLanguage()));
	}


	/**
	 * Get a category id by title
	 *
	 * @return	int
	 * @param	string $title					The title of the category.
	 * @param	string[optional] $language		The language to use, if not provided we will use the working language.
	 */
	public static function getCategoryId($title, $language = null)
	{
		// redefine
		$title = (string) $title;
		$language = ($language !== null) ? (string) $language : BackendLanguage::getWorkingLanguage();

		// exists?
		return (int) BackendModel::getDB()->getVar('SELECT i.id
													FROM blog_categories AS i
													WHERE i.title = ? AND i.language = ?',
													array($title, $language));
	}


	/**
	 * Get all data for a given id
	 *
	 * @return	array
	 * @param	int $id		The Id of the comment to fetch?
	 */
	public static function getComment($id)
	{
		return (array) BackendModel::getDB()->getRecord('SELECT i.*, UNIX_TIMESTAMP(i.created_on) AS created_on,
															p.id AS post_id, p.title AS post_title, m.url AS post_url
															FROM blog_comments AS i
															INNER JOIN blog_posts AS p ON i.post_id = p.id AND i.language = p.language
															INNER JOIN meta AS m ON p.meta_id = m.id
															WHERE i.id = ?
															LIMIT 1',
															array((int) $id));
	}


	/**
	 * Get multiple comments at once
	 *
	 * @return	array
	 * @param	array $ids		The id(s) of the comment(s).
	 */
	public static function getComments(array $ids)
	{
		return (array) BackendModel::getDB()->getRecords('SELECT *
															FROM blog_comments AS i
															WHERE i.id IN (' . implode(', ', array_fill(0, count($ids), '?')) . ')', $ids);
	}


	/**
	 * Get a count per comment
	 *
	 * @return	array
	 */
	public static function getCommentStatusCount()
	{
		return (array) BackendModel::getDB()->getPairs('SELECT i.status, COUNT(i.id)
															FROM blog_comments AS i
															WHERE i.language = ?
															GROUP BY i.status',
															array(BL::getWorkingLanguage()));
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
		// get the comments (order by id, this is faster then on date, the higher the id, the more recent
		$comments = (array) BackendModel::getDB()->getRecords('SELECT i.id, i.author, i.text, UNIX_TIMESTAMP(i.created_on) AS created_in,
																	p.title, p.language, m.url
																FROM blog_comments AS i
																INNER JOIN blog_posts AS p ON i.post_id = p.id AND i.language = p.language
																INNER JOIN meta AS m ON p.meta_id = m.id
																WHERE i.status = ? AND p.status = ? AND i.language = ?
																ORDER BY i.id DESC
																LIMIT ?',
																array((string) $status, 'active', BL::getWorkingLanguage(), (int) $limit));

		// loop entries
		foreach($comments as $key => &$row)
		{
			// add full url
			$row['full_url'] = BackendModel::getURLForBlock('blog', 'detail', $row['language']) . '/' . $row['url'];
		}

		// return
		return $comments;
	}


	/**
	 * Get the maximum id
	 *
	 * @return	int
	 */
	public static function getMaximumId()
	{
		return (int) BackendModel::getDB()->getVar('SELECT MAX(id) FROM blog_posts LIMIT 1');
	}


	/**
	 * Get all data for a given revision
	 *
	 * @return	array
	 * @param	int $id				The id of the item.
	 * @param	int $revisionId		The revision to get.
	 */
	public static function getRevision($id, $revisionId)
	{
		return (array) BackendModel::getDB()->getRecord('SELECT i.*, UNIX_TIMESTAMP(i.publish_on) AS publish_on, UNIX_TIMESTAMP(i.created_on) AS created_on, UNIX_TIMESTAMP(i.edited_on) AS edited_on, m.url
															FROM blog_posts AS i
															INNER JOIN meta AS m ON m.id = i.meta_id
															WHERE i.id = ? AND i.revision_id = ?',
															array((int) $id, (int) $revisionId));
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
											FROM blog_posts AS i
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
											FROM blog_posts AS i
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
											FROM blog_categories AS i
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
											FROM blog_categories AS i
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
		// insert and return the new revision id
		$item['revision_id'] = BackendModel::getDB(true)->insert('blog_posts', $item);

		// invalidate the cache for blog
		BackendModel::invalidateFrontendCache('blog', BL::getWorkingLanguage());

		// return the new revision id
		return $item['revision_id'];
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
		$item['id'] = $db->insert('blog_categories', $item);

		// invalidate the cache for blog
		BackendModel::invalidateFrontendCache('blog', BL::getWorkingLanguage());

		// return the id
		return $item['id'];
	}


	/**
	 * Recalculate the commentcount
	 *
	 * @return	bool
	 * @param	array $ids	The id(s) of the post wherefor the commentcount should be recalculated.
	 */
	public static function reCalculateCommentCount(array $ids)
	{
		// validate
		if(empty($ids)) return false;

		// make unique ids
		$ids = array_unique($ids);

		// get db
		$db = BackendModel::getDB(true);

		// get counts
		$commentCounts = (array) $db->getPairs('SELECT i.post_id, COUNT(i.id) AS comment_count
												FROM blog_comments AS i
												INNER JOIN blog_posts AS p ON i.post_id = p.id AND i.language = p.language
												WHERE i.status = ? AND i.post_id IN (' . implode(',', $ids) . ') AND i.language = ? AND p.status = ?
												GROUP BY i.post_id',
												array('published', BL::getWorkingLanguage(), 'active'));


		// loop items
		foreach($ids as $id)
		{
			// get count
			$count = (isset($commentCounts[$id])) ? (int) $commentCounts[$id] : 0;

			// update
			$db->update('blog_posts', array('num_comments' => $count), 'id = ? AND language = ?', array($id, BL::getWorkingLanguage()));
		}

		return true;
	}


	/**
	 * Update an existing item
	 *
	 * @return	int
	 * @param	array $item		The new data.
	 */
	public static function update(array $item)
	{
		// check if new version is active
		if($item['status'] == 'active')
		{
			// archive all older active versions
			BackendModel::getDB(true)->update('blog_posts', array('status' => 'archived'), 'id = ? AND status = ?', array($item['id'], $item['status']));

			// get the record of the exact item we're editing
			$revision = self::getRevision($item['id'], $item['revision_id']);

			// if it used to be a draft that we're now publishing, remove drafts
			if($revision['status'] == 'draft') BackendModel::getDB(true)->delete('blog_posts', 'id = ? AND status = ?', array($item['id'], $revision['status']));
		}

		// don't want revision id
		unset($item['revision_id']);

		// how many revisions should we keep
		$rowsToKeep = (int) BackendModel::getModuleSetting('blog', 'max_num_revisions', 20);

		// set type of archive
		$archiveType = ($item['status'] == 'active' ? 'archived' : $item['status']);

		// get revision-ids for items to keep
		$revisionIdsToKeep = (array) BackendModel::getDB()->getColumn('SELECT i.revision_id
																		 FROM blog_posts AS i
																		 WHERE i.id = ? AND i.status = ? AND i.language = ?
																		 ORDER BY i.edited_on DESC
																		 LIMIT ?',
																		 array($item['id'], $archiveType, BL::getWorkingLanguage(), $rowsToKeep));

		// delete other revisions
		if(!empty($revisionIdsToKeep)) BackendModel::getDB(true)->delete('blog_posts', 'id = ? AND status = ? AND revision_id NOT IN (' . implode(', ', $revisionIdsToKeep) . ')', array($item['id'], $archiveType));

		// insert new version
		$item['revision_id'] = BackendModel::getDB(true)->insert('blog_posts', $item);

		// invalidate the cache for blog
		BackendModel::invalidateFrontendCache('blog', BL::getWorkingLanguage());

		// return the new revision id
		return $item['revision_id'];
	}


	/**
	 * Update an existing category
	 *
	 * @return	int
	 * @param	array $item				The new data.
	 * @param	array[optional] $meta	The new meta-data.
	 */
	public static function updateCategory(array $item, $meta = null)
	{
		// get db
		$db = BackendModel::getDB(true);

		// update category
		$updated = $db->update('blog_categories', $item, 'id = ?', array((int) $item['id']));

		// meta passed?
		if($meta !== null)
		{
			// get current category
			$category = self::getCategory($item['id']);

			// update the meta
			$db->update('meta', $meta, 'id = ?', array((int) $category['meta_id']));
		}

		// invalidate the cache for blog
		BackendModel::invalidateFrontendCache('blog', BL::getWorkingLanguage());

		// return
		return $updated;
	}


	/**
	 * Update an existing comment
	 *
	 * @return	int
	 * @param	array $item		The new data.
	 */
	public static function updateComment(array $item)
	{
		// update category
		return BackendModel::getDB(true)->update('blog_comments', $item, 'id = ?', array((int) $item['id']));
	}


	/**
	 * Updates one or more comments' status
	 *
	 * @return	void
	 * @param	array $ids			The id(s) of the comment(s) to change the status for.
	 * @param	string $status		The new status.
	 */
	public static function updateCommentStatuses($ids, $status)
	{
		// make sure $ids is an array
		$ids = (array) $ids;

		// loop and cast to integers
		foreach($ids as &$id) $id = (int) $id;

		// create an array with an equal amount of questionmarks as ids provided
		$idPlaceHolders = array_fill(0, count($ids), '?');

		// get ids
		$itemIds = (array) BackendModel::getDB()->getColumn('SELECT i.post_id
																FROM blog_comments AS i
																WHERE i.id IN (' . implode(', ', $idPlaceHolders) . ')', $ids);

		// update record
		BackendModel::getDB(true)->execute('UPDATE blog_comments
											SET status = ?
											WHERE id IN (' . implode(', ', $idPlaceHolders) . ')',
											array_merge(array((string) $status), $ids));

		// recalculate the comment count
		if(!empty($itemIds)) self::reCalculateCommentCount($itemIds);

		// invalidate the cache for blog
		BackendModel::invalidateFrontendCache('blog', BL::getWorkingLanguage());
	}
}

?>