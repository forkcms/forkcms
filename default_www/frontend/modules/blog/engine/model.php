<?php

/**
 * FrontendBlogModel
 *
 * In this file we store all generic functions that we will be using in the blog module
 *
 *
 * @package		frontend
 * @subpackage	blog
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class FrontendBlogModel
{
	/**
	 * Add a comment
	 *
	 * @return	int
	 * @param	array $comment
	 */
	public static function addComment(array $comment)
	{
		// get db
		$db = FrontendModel::getDB();

		// insert comment
		$insertId = (int) $db->insert('blog_comments', $comment);

		// increment comment_count
		$db->execute('UPDATE blog_posts SET num_comments = num_comments + 1 WHERE id = ?;', $comment['post_id']);

		// retunr comment id
		return $insertId;
	}


	/**
	 * Get an article
	 *
	 * @return	array
	 * @param	string $url
	 */
	public static function get($url)
	{
		// redefine
		$url = (string) $url;

		// get db
		$db = FrontendModel::getDB();

		// get the blogposts
		return (array) $db->getRecord('SELECT bp.id, bp.language, bp.title, bp.introduction, bp.text,
										bc.name AS category_name, bc.url AS category_url,
										UNIX_TIMESTAMP(bp.publish_on) AS publish_on, bp.user_id,
										m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
										m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
										m.title AS meta_title, m.title_overwrite AS meta_title_overwrite,
										m.url
										FROM blog_posts AS bp
										INNER JOIN blog_categories AS bc ON bp.category_id = bc.id
										INNER JOIN meta AS m ON bp.meta_id = m.id
										WHERE bp.status = ? AND bp.language = ? AND bp.hidden = ? AND bp.publish_on <= ? AND m.url = ?
										LIMIT 1;',
										array('active', FRONTEND_LANGUAGE, 'N', date('Y-m-d H:i') .':00', $url));
	}


	/**
	 * Get all blogposts (at least a chunk)
	 *
	 * @return	array
	 * @param	int[optional] $limit
	 * @param	int[optional] $offset
	 */
	public static function getAll($limit = 10, $offset = 0)
	{
		// redefine
		$limit = (int) $limit;
		$offset = (int) $offset;

		// get db
		$db = FrontendModel::getDB();

		// init
		$return = array();

		// get the blogposts
		$articles = (array) $db->retrieve('SELECT bp.id, bp.language, bp.title, bp.introduction, bp.text, bp.num_comments AS comments_count,
											bc.name AS category_name, bc.url AS category_url,
											UNIX_TIMESTAMP(bp.publish_on) AS publish_on, bp.user_id,
											m.url
											FROM blog_posts AS bp
											INNER JOIN blog_categories AS bc ON bp.category_id = bc.id
											INNER JOIN meta AS m ON bp.meta_id = m.id
											WHERE bp.status = ? AND bp.language = ? AND bp.hidden = ? AND bp.publish_on <= ?
											ORDER BY bp.publish_on DESC
											LIMIT ?, ?;',
											array('active', FRONTEND_LANGUAGE, 'N', date('Y-m-d H:i') .':00', $offset, $limit), 'id');

		// no results?
		if(empty($articles)) return array();

		// init var
		$postIds = array();
		$blogLink = FrontendNavigation::getURLForBlock('blog', 'detail');
		$categoryLink = FrontendNavigation::getURLForBlock('blog', 'category');

		// loop
		foreach($articles as $key => $row)
		{
			// ids
			$postIds[] = (int) $row['id'];

			// urls
			$articles[$key]['full_url'] = $blogLink .'/'. $row['url'];
			$articles[$key]['category_full_url'] = $categoryLink .'/'. $row['category_url'];

			// comments
			if($row['comments_count'] > 0) $articles[$key]['comments'] = true;
			if($row['comments_count'] > 1) $articles[$key]['comments_multiple'] = true;
		}

		// get all tags
		$tags = FrontendTagsModel::getForMultipleItems('blog', $postIds);

		// loop tags
		foreach($tags as $postId => $tags) $articles[$postId]['tags'] = $tags;

		// return
		return $articles;
	}


	public static function getAllCount()
	{
		// get db
		$db = FrontendModel::getDB();

		return (int) $db->getVar('SELECT COUNT(bp.id) AS count
									FROM blog_posts AS bp
									WHERE bp.status = ? AND bp.language = ? AND bp.hidden = ? AND bp.publish_on <= ?;',
									array('active', FRONTEND_LANGUAGE, 'N', date('Y-m-d H:i') .':00'), 'id');
	}



	/**
	 * Get the comments for an article
	 *
	 * @return	array
	 * @param	int $id
	 */
	public static function getComments($id)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = FrontendModel::getDB();

		// get the comments
		return (array) $db->retrieve('SELECT bc.id, UNIX_TIMESTAMP(bc.created_on) AS created_on, bc.text, bc.data,
										bc.author, bc.email, bc.website
										FROM blog_comments AS bc
										WHERE bc.post_id = ? AND bc.status = ?
										ORDER BY bc.created_on ASC;',
										array($id, 'published'));
	}


	/**
	 * Get recent comments
	 *
	 * @return	array
	 * @param	int $limit
	 */
	public static function getRecentComments($limit = 5)
	{
		// redefine
		$limit = (int) $limit;

		// get db
		$db = FrontendModel::getDB();

		// init var
		$return = array();

		// get comments
		$comments = (array) $db->retrieve('SELECT bc.id, bc.author, bc.website, UNIX_TIMESTAMP(bc.created_on) AS created_on,
											bp.id AS post_id, bp.title AS post_title,
											m.url AS post_url
											FROM blog_comments AS bc
											INNER JOIN blog_posts AS bp ON bc.post_id = bp.id
											INNER JOIN meta AS m ON bp.meta_id = m.id
											WHERE bc.status = ? AND bp.status = ? AND bp.language = ? AND bp.hidden = ? AND bp.publish_on <= ?
											ORDER BY bc.created_on DESC
											LIMIT ?;',
											array('published', 'active', FRONTEND_LANGUAGE, 'N', date('Y-m-d H:i') .':00', $limit));

		// validate
		if(empty($comments)) return $return;

		// get link
		$blogLink = FrontendNavigation::getURLForBlock('blog', 'detail');

		// loop comments
		foreach($comments as $row)
		{
			// add some urls
			$row['post_full_url'] = $blogLink .'/'. $row['post_url'];
			$row['full_url'] = $blogLink .'/'. $row['post_url'] .'#'. FL::getAction('Comment') .'-'. $row['id'];

			// add
			$return[] = $row;
		}

		// return
		return $return;
	}


	/**
	 * Get moderation status for an author
	 *
	 * @return	bool
	 * @param	string $author
	 * @param	string $email
	 */
	public static function isModerated($author, $email)
	{
		// redefine
		$author = (string) $author;
		$email = (string) $email;

		// get db
		$db = FrontendModel::getDB();

		// does the author has a moderated comment before?
		return (bool) ($db->getNumRows('SELECT bc.author, bc.email
										FROM blog_comments AS bc
										WHERE bc.status = ? AND bc.author = ? AND bc.email = ?;',
										array('published', $author, $email)) > 0);
	}
}

?>