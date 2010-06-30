<?php

/**
 * FrontendBlogModel
 * In this file we store all generic functions that we will be using in the blog module
 *
 * @package		frontend
 * @subpackage	blog
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @author		Dave Lens <dave@netlash.com>
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendBlogModel
{
	/**
	 * Add a comment
	 *
	 * @return	int
	 * @param	array $comment	The comment to add.
	 */
	public static function addComment(array $comment)
	{
		// get db
		$db = FrontendModel::getDB(true);

		// insert comment
		$insertId = (int) $db->insert('blog_comments', $comment);

		// increment comment_count
		$db->execute('UPDATE blog_posts
						SET num_comments = num_comments + 1
						WHERE id = ?;',
						$comment['post_id']);

		// return comment id
		return $insertId;
	}


	/**
	 * Get an item
	 *
	 * @return	array
	 * @param	string $URL		The URL for the item.
	 */
	public static function get($URL)
	{
		// redefine
		$URL = (string) $URL;

		// get the blogposts
		return (array) FrontendModel::getDB()->getRecord('SELECT bp.id, bp.language, bp.title, bp.introduction, bp.text,
															bc.name AS category_name, bc.url AS category_url,
															UNIX_TIMESTAMP(bp.publish_on) AS publish_on, bp.user_id,
															bp.allow_comments,
															m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
															m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
															m.title AS meta_title, m.title_overwrite AS meta_title_overwrite,
															m.url
															FROM blog_posts AS bp
															INNER JOIN blog_categories AS bc ON bp.category_id = bc.id
															INNER JOIN meta AS m ON bp.meta_id = m.id
															WHERE bp.status = ? AND bp.language = ? AND bp.hidden = ? AND bp.publish_on <= ? AND m.url = ?
															LIMIT 1;',
															array('active', FRONTEND_LANGUAGE, 'N', FrontendModel::getUTCDate('Y-m-d H:i') .':00', $URL));
	}


	/**
	 * Get all blogposts (at least a chunk)
	 *
	 * @return	array
	 * @param	int[optional] $limit		The number of items to get.
	 * @param	int[optional] $offset		The offset.
	 */
	public static function getAll($limit = 10, $offset = 0)
	{
		// redefine
		$limit = (int) $limit;
		$offset = (int) $offset;

		// get the blogposts
		$items = (array) FrontendModel::getDB()->getRecords('SELECT bp.id, bp.language, bp.title, bp.introduction, bp.text, bp.num_comments AS comments_count,
																bc.name AS category_name, bc.url AS category_url,
																UNIX_TIMESTAMP(bp.publish_on) AS publish_on, bp.user_id,
																m.url
																FROM blog_posts AS bp
																INNER JOIN blog_categories AS bc ON bp.category_id = bc.id
																INNER JOIN meta AS m ON bp.meta_id = m.id
																WHERE bp.status = ? AND bp.language = ? AND bp.hidden = ? AND bp.publish_on <= ?
																ORDER BY bp.publish_on DESC
																LIMIT ?, ?;',
																array('active', FRONTEND_LANGUAGE, 'N', FrontendModel::getUTCDate('Y-m-d H:i') .':00', $offset, $limit), 'id');

		// no results?
		if(empty($items)) return array();

		// init var
		$postIds = array();
		$blogLink = FrontendNavigation::getURLForBlock('blog', 'detail');
		$categoryLink = FrontendNavigation::getURLForBlock('blog', 'category');

		// loop
		foreach($items as $key => $row)
		{
			// ids
			$postIds[] = (int) $row['id'];

			// URLs
			$items[$key]['full_url'] = $blogLink .'/'. $row['url'];
			$items[$key]['category_full_url'] = $categoryLink .'/'. $row['category_url'];

			// comments
			if($row['comments_count'] > 0) $items[$key]['comments'] = true;
			if($row['comments_count'] > 1) $items[$key]['comments_multiple'] = true;
		}

		// get all tags
		$tags = FrontendTagsModel::getForMultipleItems('blog', $postIds);

		// loop tags
		foreach($tags as $postId => $tags) $items[$postId]['tags'] = $tags;

		// return
		return $items;
	}


	/**
	 * Get all categories used in blog
	 *
	 * @return	array
	 */
	public static function getAllCategories()
	{
		return (array) FrontendModel::getDB()->getRecords('SELECT bc.id, bc.name AS label, bc.url, COUNT(bc.id) AS total
															FROM blog_categories AS bc
															INNER JOIN blog_posts AS bp ON bc.id = bp.category_id AND bc.language = bp.language
															WHERE bc.language = ? AND bp.status = ? AND bp.hidden = ? AND bp.publish_on <= ?
															GROUP BY bc.id;',
															array(FRONTEND_LANGUAGE, 'active', 'N', FrontendModel::getUTCDate('Y-m-d H:i') .':00'), 'id');
	}


	/**
	 * Get the number of blog posts
	 *
	 * @return	int
	 */
	public static function getAllCount()
	{
		return (int) FrontendModel::getDB()->getVar('SELECT COUNT(bp.id) AS count
														FROM blog_posts AS bp
														WHERE bp.status = ? AND bp.language = ? AND bp.hidden = ? AND bp.publish_on <= ?;',
														array('active', FRONTEND_LANGUAGE, 'N', FrontendModel::getUTCDate('Y-m-d H:i') .':00'), 'id');
	}


	/**
	 * Get all blogposts (at least a chunk)
	 *
	 * @return	array
	 * @param	string $categoryURL		The URL of the category to retrieve the posts for.
	 * @param	int[optional] $limit	The number of items to get.
	 * @param	int[optional] $offset	The offset.
	 */
	public static function getAllForCategory($categoryURL, $limit = 10, $offset = 0)
	{
		// redefine
		$categoryURL = (string) $categoryURL;
		$limit = (int) $limit;
		$offset = (int) $offset;

		// get the blogposts
		$items = (array) FrontendModel::getDB()->getRecords('SELECT bp.id, bp.language, bp.title, bp.introduction, bp.text, bp.num_comments AS comments_count,
																bc.name AS category_name, bc.url AS category_url,
																UNIX_TIMESTAMP(bp.publish_on) AS publish_on, bp.user_id,
																m.url
																FROM blog_posts AS bp
																INNER JOIN blog_categories AS bc ON bp.category_id = bc.id
																INNER JOIN meta AS m ON bp.meta_id = m.id
																WHERE bp.status = ? AND bp.language = ? AND bp.hidden = ? AND bp.publish_on <= ? AND bc.url = ?
																ORDER BY bp.publish_on DESC
																LIMIT ?, ?;',
																array('active', FRONTEND_LANGUAGE, 'N', FrontendModel::getUTCDate('Y-m-d H:i') .':00', $categoryURL, $offset, $limit), 'id');

		// no results?
		if(empty($items)) return array();

		// init var
		$postIds = array();
		$blogLink = FrontendNavigation::getURLForBlock('blog', 'detail');
		$categoryLink = FrontendNavigation::getURLForBlock('blog', 'category');

		// loop
		foreach($items as $key => $row)
		{
			// ids
			$postIds[] = (int) $row['id'];

			// URLs
			$items[$key]['full_url'] = $blogLink .'/'. $row['url'];
			$items[$key]['category_full_url'] = $categoryLink .'/'. $row['category_url'];

			// comments
			if($row['comments_count'] > 0) $items[$key]['comments'] = true;
			if($row['comments_count'] > 1) $items[$key]['comments_multiple'] = true;
		}

		// get all tags
		$tags = FrontendTagsModel::getForMultipleItems('blog', $postIds);

		// loop tags
		foreach($tags as $postId => $tags) $items[$postId]['tags'] = $tags;

		// return
		return $items;
	}


	/**
	 * Get the number of blog post in a given category
	 *
	 * @return	int
	 * @param	string $URL		The URL for the category.
	 */
	public static function getAllForCategoryCount($categoryURL)
	{
		// redefine
		$categoryURL = (string) $categoryURL;

		// return the number of items
		return (int) FrontendModel::getDB()->getVar('SELECT COUNT(bp.id) AS count
														FROM blog_posts AS bp
														INNER JOIN blog_categories AS bc ON bp.category_id = bc.id
														WHERE bp.status = ? AND bp.language = ? AND bp.hidden = ? AND bp.publish_on <= ? AND bc.url = ?;',
														array('active', FRONTEND_LANGUAGE, 'N', FrontendModel::getUTCDate('Y-m-d H:i') .':00', $categoryURL));
	}


	public static function getAllForDateRange($start, $end, $limit = 10, $offset = 0)
	{
		// redefine
		$start = (int) $start;
		$end = (int) $end;
		$limit = (int) $limit;
		$offset = (int) $offset;

		// get the blogposts
		$items = (array) FrontendModel::getDB()->getRecords('SELECT bp.id, bp.language, bp.title, bp.introduction, bp.text, bp.num_comments AS comments_count,
																bc.name AS category_name, bc.url AS category_url,
																UNIX_TIMESTAMP(bp.publish_on) AS publish_on, bp.user_id,
																m.url
																FROM blog_posts AS bp
																INNER JOIN blog_categories AS bc ON bp.category_id = bc.id
																INNER JOIN meta AS m ON bp.meta_id = m.id
																WHERE bp.status = ? AND bp.language = ? AND bp.hidden = ? AND bp.publish_on BETWEEN ? AND ?
																ORDER BY bp.publish_on DESC
																LIMIT ?, ?;',
																array('active', FRONTEND_LANGUAGE, 'N', FrontendModel::getUTCDate('Y-m-d H:i', $start), FrontendModel::getUTCDate('Y-m-d H:i', $end), $offset, $limit), 'id');

		// no results?
		if(empty($items)) return array();

		// init var
		$postIds = array();
		$blogLink = FrontendNavigation::getURLForBlock('blog', 'detail');

		// loop
		foreach($items as $key => $row)
		{
			// ids
			$postIds[] = (int) $row['id'];

			// URLs
			$items[$key]['full_url'] = $blogLink .'/'. $row['url'];

			// comments
			if($row['comments_count'] > 0) $items[$key]['comments'] = true;
			if($row['comments_count'] > 1) $items[$key]['comments_multiple'] = true;
		}

		// get all tags
		$tags = FrontendTagsModel::getForMultipleItems('blog', $postIds);

		// loop tags
		foreach($tags as $postId => $tags) $items[$postId]['tags'] = $tags;

		// return
		return $items;
	}


	/**
	 * Get the number of items in a date range
	 */
	public static function getAllForDateRangeCount($start, $end)
	{
		// redefine
		$start = (int) $start;
		$end = (int) $end;

		// return the number of items
		return (int) FrontendModel::getDB()->getVar('SELECT COUNT(bp.id) AS count
														FROM blog_posts AS bp
														INNER JOIN blog_categories AS bc ON bp.category_id = bc.id
														WHERE bp.status = ? AND bp.language = ? AND bp.hidden = ? AND bp.publish_on BETWEEN ? AND ?;',
														array('active', FRONTEND_LANGUAGE, 'N', FrontendModel::getUTCDate('Y-m-d H:i:s', $start), FrontendModel::getUTCDate('Y-m-d H:i:s', $end)));

	}


	/**
	 * Get the statistics for the blog
	 *
	 * @return	array
	 */
	public static function getArchiveNumbers()
	{
		// grab stats
		$numbers = FrontendModel::getDB()->getPairs('SELECT DATE_FORMAT(bp.publish_on, "%Y%m") AS month, COUNT(bp.id)
													FROM blog_posts AS bp
													INNER JOIN meta AS m ON bp.meta_id = m.id
													WHERE bp.status = ? AND bp.language = ? AND bp.hidden = ? AND bp.publish_on <= ?
													GROUP BY month;',
													array('active', FRONTEND_LANGUAGE, 'N', FrontendModel::getUTCDate('Y-m-d H:i') .':00'));

		// init vars
		$stats = array();
		$link = FrontendNavigation::getURLForBlock('blog', 'archive');
		$firstYear = (int) date('Y');
		$lastYear = 0;

		// loop the numbers
		foreach($numbers as $key => $count)
		{
			// init vars
			$year = substr($key, 0, 4);
			$month = substr($key, 4, 2);

			// reset
			if($year < $firstYear) $firstYear = $year;
			if($year > $lastYear) $lastYear = $year;

			// generate timestamp
			$timestamp = gmmktime(00, 00, 00, $month, 01, $year);

			// initialize if needed
			if(!isset($stats[$year])) $stats[$year] = array('url' => $link .'/'. $year, 'label' => $year, 'total' => 0, 'months' => null);

			// increment the total
			$stats[$year]['total'] += (int) $count;
			$stats[$year]['months'][$key] = array('url' => $link .'/'. $year .'/'. $month, 'label' => $timestamp, 'total' => $count);
		}

		// loop years
		for($i = $firstYear; $i <= $lastYear;  $i++)
		{
			// year missing
			if(!isset($stats[$i])) $stats[$i] = array('url' => null, 'label' => $i, 'total' => 0, 'months' => null);
		}

		// sort
		krsort($stats);

		// reset stats
		foreach($stats as &$row)
		{
			// remove url for empty years
			if($row['total'] == 0) $row['url'] = null;

			// any months?
			if(!empty($row['months']))
			{
				// sort months
				krsort($row['months']);
			}
		}

		// return
		return $stats;
	}


	/**
	 * Get the comments for an item
	 *
	 * @return	array
	 * @param	int $id		The ID of the blogpost to get the comments for.
	 */
	public static function getComments($id)
	{
		// redefine
		$id = (int) $id;

		// get the comments
		$comments = (array) FrontendModel::getDB()->getRecords('SELECT bc.id, UNIX_TIMESTAMP(bc.created_on) AS created_on, bc.text, bc.data,
																bc.author, bc.email, bc.website
																FROM blog_comments AS bc
																WHERE bc.post_id = ? AND bc.status = ?
																ORDER BY bc.created_on ASC;',
																array($id, 'published'));

		// loop comments
		foreach($comments as &$row) $row['gravatar_id'] = md5($row['email']);

		// return the comments
		return $comments;
	}


	/**
	 * Get a draft for an item
	 *
	 * @return	array
	 * @param	string $URL		The URL for the item to get.
	 * @param	int $draft		The draftID.
	 */
	public static function getDraft($URL, $draft)
	{
		// redefine
		$URL = (string) $URL;
		$draft = (int) $draft;

		// get the item
		return (array) FrontendModel::getDB()->getRecord('SELECT bp.id, bp.language, bp.title, bp.introduction, bp.text,
															bc.name AS category_name, bc.url AS category_url,
															UNIX_TIMESTAMP(bp.publish_on) AS publish_on, bp.user_id,
															bp.allow_comments,
															m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
															m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
															m.title AS meta_title, m.title_overwrite AS meta_title_overwrite,
															m.url
															FROM blog_posts AS bp
															INNER JOIN blog_categories AS bc ON bp.category_id = bc.id
															INNER JOIN meta AS m ON bp.meta_id = m.id
															WHERE bp.status = ? AND bp.language = ? AND bp.hidden = ? AND bp.revision_id = ? AND m.url = ?
															LIMIT 1;',
															array('draft', FRONTEND_LANGUAGE, 'N', $draft, $URL));
	}


	/**
	 * Fetch the list of tags for a list of items
	 *
	 * @return	array
	 * @param	array $ids
	 */
	public static function getForTags(array $ids)
	{
		// fetch items
		$items = (array) FrontendModel::getDB()->getRecords('SELECT p.title, m.url
															FROM blog_posts AS p
															INNER JOIN meta AS m ON m.id = p.meta_id
															WHERE p.status = ? AND
															p.hidden = ? AND
															p.id IN ('. implode(',', $ids) .')
															ORDER BY p.publish_on DESC;', array('active', 'N'));

		// has items
		if(!empty($items))
		{
			// init var
			$link = FrontendNavigation::getURLForBlock('blog', 'detail');

			// reset url
			foreach($items as &$row) $row['url'] = $link .'/'. $row['url'];
		}

		// return
		return $items;
	}


	/**
	 * Get an array with the previous and the next post
	 *
	 * @return	array
	 * @param	int $id		The id of the current item.
	 */
	public static function getNavigation($id)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = FrontendModel::getDB();

		// get date for current item
		$date = (int) $db->getVar('SELECT UNIX_TIMESTAMP(i.publish_on)
									FROM blog_posts AS i
									WHERE i.id = ?;',
									array($id));

		// validate
		if($date == 0) return array();

		// init var
		$return = array();

		// get previous post
		$return['previous'] = $db->getRecord('SELECT bp.id, bp.title, m.url
											FROM blog_posts AS bp
											INNER JOIN meta AS m ON bp.meta_id = m.id
											WHERE bp.status = ? AND bp.language = ? AND bp.hidden = ? AND bp.publish_on < ? AND bp.id != ?
											ORDER BY bp.publish_on DESC
											LIMIT 1;',
											array('active', FRONTEND_LANGUAGE, 'N', date('Y-m-d H:i:s', $date), $id));

		// get next post
		$return['next'] = $db->getRecord('SELECT bp.id, bp.title, m.url
											FROM blog_posts AS bp
											INNER JOIN meta AS m ON bp.meta_id = m.id
											WHERE bp.status = ? AND bp.language = ? AND bp.hidden = ? AND bp.publish_on > ? AND bp.publish_on <= ?
											ORDER BY bp.publish_on ASC
											LIMIT 1;',
											array('active', FRONTEND_LANGUAGE, 'N', date('Y-m-d H:i:s', $date), FrontendModel::getUTCDate('Y-m-d H:i') .':00'));

		// return
		return $return;
	}


	/**
	 * Get recent comments
	 *
	 * @return	array
	 * @param	int $limit	The number of comments to get.
	 */
	public static function getRecentComments($limit = 5)
	{
		// redefine
		$limit = (int) $limit;

		// init var
		$return = array();

		// get comments
		$comments = (array) FrontendModel::getDB()->getRecords('SELECT bc.id, bc.author, bc.website, bc.email, UNIX_TIMESTAMP(bc.created_on) AS created_on, bc.text,
																bp.id AS post_id, bp.title AS post_title,
																m.url AS post_url
																FROM blog_comments AS bc
																INNER JOIN blog_posts AS bp ON bc.post_id = bp.id
																INNER JOIN meta AS m ON bp.meta_id = m.id
																WHERE bc.status = ? AND bp.status = ? AND bp.language = ? AND bp.hidden = ? AND bp.publish_on <= ?
																ORDER BY bc.created_on DESC
																LIMIT ?;',
																array('published', 'active', FRONTEND_LANGUAGE, 'N', FrontendModel::getUTCDate('Y-m-d H:i') .':00', $limit));

		// validate
		if(empty($comments)) return $return;

		// get link
		$link = FrontendNavigation::getURLForBlock('blog', 'detail');

		// loop comments
		foreach($comments as &$row)
		{
			// add some URLs
			$row['post_full_url'] = $link .'/'. $row['post_url'];
			$row['full_url'] = $link .'/'. $row['post_url'] .'#'. FL::getAction('Comment') .'-'. $row['id'];
			$row['gravatar_id'] = md5($row['email']);
		}

		// return
		return $comments;
	}


	/**
	 * Get moderation status for an author
	 *
	 * @return	bool
	 * @param	string $author	The name for the author.
	 * @param	string $email	The emailaddress for the author.
	 */
	public static function isModerated($author, $email)
	{
		// redefine
		$author = (string) $author;
		$email = (string) $email;

		// does the author has a moderated comment before?
		return (bool) (FrontendModel::getDB()->getNumRows('SELECT bc.author, bc.email
															FROM blog_comments AS bc
															WHERE bc.status = ? AND bc.author = ? AND bc.email = ?;',
															array('published', $author, $email)) > 0);
	}
}

?>