<?php

/**
 * In this file we store all generic functions that we will be using in the events module
 *
 * @package		backend
 * @subpackage	events
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Dave Lens <dave@netlash.com>
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
class BackendEventsModel
{
	const QRY_DATAGRID_BROWSE = 'SELECT i.id, i.revision_id, UNIX_TIMESTAMP(i.starts_on) AS starts_on, UNIX_TIMESTAMP(i.ends_on) AS ends_on, i.title, UNIX_TIMESTAMP(i.publish_on) AS publish_on, i.num_comments AS comments
									FROM events AS i
									WHERE i.status = ? AND i.language = ?';
	const QRY_DATAGRID_BROWSE_COMMENTS = 'SELECT i.id, UNIX_TIMESTAMP(i.created_on) AS created_on, i.author, i.text,
											p.id AS event_id, p.title AS post_title, m.url AS post_url
											FROM events_comments AS i
											INNER JOIN events AS p ON i.event_id = p.id AND i.language = p.language
											INNER JOIN meta AS m ON p.meta_id = m.id
											WHERE i.status = ? AND i.language = ?
											GROUP BY i.id';
	const QRY_DATAGRID_BROWSE_REVISIONS = 'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on
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

		// events rss title
		if(BackendModel::getModuleSetting('events', 'rss_title_'. BL::getWorkingLanguage(), null) == '')
		{
			// add warning
			$warnings[] = array('message' => sprintf(BL::err('RSSTitle', 'events'), BackendModel::createURLForAction('settings', 'events')));
		}

		// events rss description
		if(BackendModel::getModuleSetting('events', 'rss_description_'. BL::getWorkingLanguage(), null) == '')
		{
			// add warning
			$warnings[] = array('message' => sprintf(BL::err('RSSDescription', 'events'), BackendModel::createURLForAction('settings', 'events')));
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

		// get db
		$db = BackendModel::getDB(true);

		// delete eventspost records
		$db->delete('events', 'id IN ('. implode(',', $ids) .') AND language = ?', array(BL::getWorkingLanguage()));
		$db->delete('events_comments', 'event_id IN ('. implode(',', $ids) .') AND language = ?', array(BL::getWorkingLanguage()));

		// get used meta ids
		$metaIds = (array) $db->getColumn('SELECT meta_id
											FROM events AS p
											WHERE id IN ('. implode(',', $ids) .') AND language = ?', array(BL::getWorkingLanguage()));

		// delete meta
		if(!empty($metaIds)) $db->delete('meta', 'id IN ('. implode(',', $metaIds) .')');

		// invalidate the cache for events
		BackendModel::invalidateFrontendCache('events', BL::getWorkingLanguage());
	}


	/**
	 * Deletes one or more comments
	 *
	 * @return	void
	 * @param	array $ids		The id(s) of the comment(s) to delete.
	 */
	public static function deleteComments($ids)
	{
		// make sure $ids is an array
		$ids = (array) $ids;

		// get db
		$db = BackendModel::getDB(true);

		// get eventspost ids
		$itemIds = (array) $db->getColumn('SELECT i.event_id
											FROM events_comments AS i
											WHERE i.id IN ('. implode(',', $ids) .') AND i.language = ?', array(BL::getWorkingLanguage()));

		// update record
		$db->delete('events_comments', 'id IN ('. implode(',', $ids) .') AND language = ?', array(BL::getWorkingLanguage()));

		// recalculate the comment count
		if(!empty($itemIds)) self::reCalculateCommentCount($itemIds);

		// invalidate the cache for events
		BackendModel::invalidateFrontendCache('events', BL::getWorkingLanguage());
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

		// get eventspost ids
		$itemIds = (array) $db->getColumn('SELECT i.event_id
											FROM events_comments AS i
											WHERE status = ? AND i.language = ?', array('spam', BL::getWorkingLanguage()));

		// update record
		$db->delete('events_comments', 'status = ? AND language = ?', array('spam', BL::getWorkingLanguage()));

		// recalculate the comment count
		if(!empty($itemIds)) self::reCalculateCommentCount($itemIds);

		// invalidate the cache for events
		BackendModel::invalidateFrontendCache('events', BL::getWorkingLanguage());
	}


	/**
	 * Checks if an item exists
	 *
	 * @return	bool
	 * @param	int $id		The id of the eventspost to check for existence.
	 */
	public static function exists($id)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT i.id
														FROM events AS i
														WHERE i.id = ? AND i.language = ?',
														array((int) $id, BL::getWorkingLanguage()));
	}


	/**
	 * Checks if a comment exists
	 *
	 * @return	int
	 * @param	int $id		The id of the comment to check for existence.
	 */
	public static function existsComment($id)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT COUNT(id)
														FROM events_comments AS i
														WHERE i.id = ? AND i.language = ?',
														array((int) $id, BL::getWorkingLanguage()));
	}


	/**
	 * Get all data for a given id
	 *
	 * @return	array
	 * @param	int $id		The Id of the eventspost to fetch?
	 */
	public static function get($id)
	{
		return (array) BackendModel::getDB()->getRecord('SELECT i.*, UNIX_TIMESTAMP(i.starts_on) AS starts_on, UNIX_TIMESTAMP(i.ends_on) AS ends_on, UNIX_TIMESTAMP(i.publish_on) AS publish_on, UNIX_TIMESTAMP(i.created_on) AS created_on, UNIX_TIMESTAMP(i.edited_on) AS edited_on,
															m.url
															FROM events AS i
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
																p.id AS event_id, p.title AS post_title, m.url AS post_url, p.language AS post_language
																FROM events_comments AS i
																INNER JOIN events AS p ON i.event_id = p.id AND i.language = p.language
																INNER JOIN meta AS m ON p.meta_id = m.id
																WHERE i.language = ?
																GROUP BY i.id
																LIMIT ?, ?',
																array(BL::getWorkingLanguage(), $offset, $limit));
		}

		// get data and return it
		return (array) BackendModel::getDB()->getRecords('SELECT i.id, UNIX_TIMESTAMP(i.created_on) AS created_on, i.author, i.email, i.website, i.text, i.type, i.status,
															p.id AS event_id, p.title AS post_title, m.url AS post_url, p.language AS post_language
															FROM events_comments AS i
															INNER JOIN events AS p ON i.event_id = p.id AND i.language = p.language
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
															INNER JOIN events AS i ON mt.other_id = i.id
															WHERE mt.module = ? AND mt.tag_id = ? AND i.status = ? AND i.language = ?',
															array('events', (int) $tagId, 'active', BL::getWorkingLanguage()));

		// loop items and create url
		foreach($items as &$row) $row['url'] = BackendModel::createURLForAction('edit', 'events', null, array('id' => $row['url']));

		// return
		return $items;
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
															p.id AS event_id, p.title AS post_title, m.url AS post_url
															FROM events_comments AS i
															INNER JOIN events AS p ON i.event_id = p.id AND i.language = p.language
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
															FROM events_comments AS i
															WHERE i.id IN ('. implode(',', $ids) .')');
	}


	/**
	 * Get a count per comment
	 *
	 * @return	array
	 */
	public static function getCommentStatusCount()
	{
		return (array) BackendModel::getDB()->getPairs('SELECT i.status, COUNT(i.id)
															FROM events_comments AS i
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
																FROM events_comments AS i
																INNER JOIN events AS p ON i.event_id = p.id AND i.language = p.language
																INNER JOIN meta AS m ON p.meta_id = m.id
																WHERE i.status = ? AND p.status = ? AND i.language = ?
																ORDER BY i.id DESC
																LIMIT ?',
																array((string) $status, 'active', BL::getWorkingLanguage(), (int) $limit));

		// loop entries
		foreach($comments as $key => &$row)
		{
			// add full url
			$row['full_url'] = BackendModel::getURLForBlock('events', 'detail', $row['language']) .'/'. $row['url'];
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
		return (int) BackendModel::getDB()->getVar('SELECT MAX(id) FROM events LIMIT 1');
	}


	/**
	 * Get all data for a given revision
	 *
	 * @return	array
	 * @param	int $id				The id of the eventspost.
	 * @param	int $revisionId		The revision to get.
	 */
	public static function getRevision($id, $revisionId)
	{
		return (array) BackendModel::getDB()->getRecord('SELECT i.*, UNIX_TIMESTAMP(i.publish_on) AS publish_on, UNIX_TIMESTAMP(i.created_on) AS created_on, UNIX_TIMESTAMP(i.edited_on) AS edited_on, m.url
															FROM events AS i
															INNER JOIN meta AS m ON m.id = i.meta_id
															WHERE i.id = ? AND i.revision_id = ?',
															array((int) $id, (int) $revisionId));
	}


	/**
	 * Retrieve the unique URL for an item
	 *
	 * @return	string
	 * @param	string $URL			The URL to base on.
	 * @param	int[optional] $id	The id of the eventspost to ignore.
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
											FROM events AS i
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
											FROM events AS i
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
	 * Inserts an item into the database
	 *
	 * @return	int
	 * @param	array $item		The data to insert.
	 */
	public static function insert(array $item)
	{
		// insert and return the new revision id
		$item['revision_id'] = BackendModel::getDB(true)->insert('events', $item);

		// invalidate the cache for events
		BackendModel::invalidateFrontendCache('events', BL::getWorkingLanguage());

		// return the new revision id
		return $item['revision_id'];
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
		$commentCounts = (array) $db->getPairs('SELECT i.event_id, COUNT(i.id) AS comment_count
												FROM events_comments AS i
												INNER JOIN events AS p ON i.event_id = p.id AND i.language = p.language
												WHERE i.status = ? AND i.event_id IN ('. implode(',', $ids) .') AND i.language = ? AND p.status = ?
												GROUP BY i.event_id',
												array('published', BL::getWorkingLanguage(), 'active'));

		// loop posts
		foreach($ids as $id)
		{
			// get count
			$count = (isset($commentCounts[$id])) ? (int) $commentCounts[$id] : 0;

			// update
			$db->update('events', array('num_comments' => $count), 'id = ? AND language = ?', array($id, BL::getWorkingLanguage()));
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
			BackendModel::getDB(true)->update('events', array('status' => 'archived'), 'id = ? AND status = ?', array($item['id'], $item['status']));

			// get the record of the exact item we're editing
			$revision = self::getRevision($item['id'], $item['revision_id']);

			// if it used to be a draft that we're now publishing, remove drafts
			if($revision['status'] == 'draft') BackendModel::getDB(true)->delete('events', 'id = ? AND status = ?', array($item['id'], $revision['status']));
		}

		// don't want revision id
		unset($item['revision_id']);

		// how many revisions should we keep
		$rowsToKeep = (int) BackendModel::getModuleSetting('events', 'max_num_revisions', 20);

		// set type of archive
		$archiveType = ($item['status'] == 'active' ? 'archived' : $item['status']);

		// get revision-ids for items to keep
		$revisionIdsToKeep = (array) BackendModel::getDB()->getColumn('SELECT i.revision_id
																		 FROM events AS i
																		 WHERE i.id = ? AND i.status = ? AND i.language = ?
																		 ORDER BY i.edited_on DESC
																		 LIMIT ?',
																		 array($item['id'], $archiveType, BL::getWorkingLanguage(), $rowsToKeep));

		// delete other revisions
		if(!empty($revisionIdsToKeep)) BackendModel::getDB(true)->delete('events', 'id = ? AND status = ? AND revision_id NOT IN ('. implode(', ', $revisionIdsToKeep) .')', array($item['id'], $archiveType));

		// insert new version
		$item['revision_id'] = BackendModel::getDB(true)->insert('events', $item);

		// invalidate the cache for events
		BackendModel::invalidateFrontendCache('events', BL::getWorkingLanguage());

		// return the new revision id
		return $item['revision_id'];
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
		return BackendModel::getDB(true)->update('events_comments', $item, 'id = ?', array((int) $item['id']));
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

		// get eventspost ids
		$itemIds = (array) BackendModel::getDB()->getColumn('SELECT i.event_id
																FROM events_comments AS i
																WHERE i.id IN ('. implode(',', $ids) .')');

		// update record
		BackendModel::getDB(true)->execute('UPDATE events_comments
											SET status = ?
											WHERE id IN ('. implode(',', $ids) .')',
											array((string) $status));

		// recalculate the comment count
		if(!empty($itemIds)) self::reCalculateCommentCount($itemIds);

		// invalidate the cache for events
		BackendModel::invalidateFrontendCache('events', BL::getWorkingLanguage());
	}
}

?>