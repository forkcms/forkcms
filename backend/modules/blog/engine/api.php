<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be available through the API
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class BackendBlogAPI
{
	/**
	 * Get the comments
	 *
	 * @param string[optional] $status The type of comments to get. Possible values are: published, moderation, spam.
	 * @param int[optional] $limit The maximum number of items to retrieve.
	 * @param int[optional] $offset The offset.
	 * @return array
	 */
	public static function commentsGet($status = null, $limit = 30, $offset = 0)
	{
		// authorize
		if(API::authorize() && API::isValidRequestMethod('GET'))
		{
			// redefine
			if($status !== null) $status = (string) $status;
			$limit = (int) $limit;
			$offset = (int) $offset;

			// validate
			if($limit > 10000) API::output(API::ERROR, array('message' => 'Limit can\'t be larger than 10000.'));

			// get comments
			$comments = (array) BackendModel::getDB()->getRecords(
				'SELECT i.id, UNIX_TIMESTAMP(i.created_on) AS created_on, i.author, i.email, i.website, i.text, i.type, i.status,
				 p.id AS post_id, p.title AS post_title, m.url AS post_url, p.language AS post_language
				 FROM blog_comments AS i
				 INNER JOIN blog_posts AS p ON i.post_id = p.id AND i.language = p.language
				 INNER JOIN meta AS m ON p.meta_id = m.id
				 WHERE p.status = ?
				 GROUP BY i.id
				 LIMIT ?, ?',
				array('active', $offset, $limit)
			);

			$return = array('comments' => null);

			// build return array
			foreach($comments as $row)
			{
				// create array
				$item['comment'] = array();

				// article meta data
				$item['comment']['article']['@attributes']['id'] = $row['post_id'];
				$item['comment']['article']['@attributes']['lang'] = $row['post_language'];
				$item['comment']['article']['title'] = $row['post_title'];
				$item['comment']['article']['url'] = SITE_URL . BackendModel::getURLForBlock('blog', 'detail', $row['post_language']) . '/' . $row['post_url'];

				// set attributes
				$item['comment']['@attributes']['id'] = $row['id'];
				$item['comment']['@attributes']['created_on'] = date('c', $row['created_on']);
				$item['comment']['@attributes']['status'] = $row['status'];

				// set content
				$item['comment']['text'] = $row['text'];
				$item['comment']['url'] = $item['comment']['article']['url'] . '#comment-' . $row['id'];

				// author data
				$item['comment']['author']['@attributes']['email'] = $row['email'];
				$item['comment']['author']['name'] = $row['author'];
				$item['comment']['author']['website'] = $row['website'];

				// add
				$return['comments'][] = $item;
			}

			return $return;
		}
	}

	/**
	 * Get a single comment
	 *
	 * @param int $id The id of the comment.
	 * @return array
	 */
	public static function commentsGetById($id)
	{
		// authorize
		if(API::authorize() && API::isValidRequestMethod('GET'))
		{
			// get comment
			$comment = (array) BackendBlogModel::getComment($id);

			// init var
			$return = array('comments' => null);

			// any comment found?
			if(empty($comment)) return $return;

			// create array
			$item['comment'] = array();

			// article meta data
			$item['comment']['article']['@attributes']['id'] = $comment['post_id'];
			$item['comment']['article']['@attributes']['lang'] = $comment['language'];
			$item['comment']['article']['title'] = $comment['post_title'];
			$item['comment']['article']['url'] = SITE_URL . BackendModel::getURLForBlock('blog', 'detail', $comment['language']) . '/' . $comment['post_url'];

			// set attributes
			$item['comment']['@attributes']['id'] = $comment['id'];
			$item['comment']['@attributes']['created_on'] = date('c', $comment['created_on']);
			$item['comment']['@attributes']['status'] = $comment['status'];

			// set content
			$item['comment']['text'] = $comment['text'];
			$item['comment']['url'] = $item['comment']['article']['url'] . '#comment-' . $comment['id'];

			// author data
			$item['comment']['author']['@attributes']['email'] = $comment['email'];
			$item['comment']['author']['name'] = $comment['author'];
			$item['comment']['author']['website'] = $comment['website'];

			// add
			$return['comments'][] = $item;

			return $return;
		}
	}

	/**
	 * Update a comment
	 *
	 * @param int $id The id of the comment.
	 * @param string[optional] $status The new status for the comment. Possible values are: published, moderation, spam.
	 * @param string[optional] $text The new text for the comment.
	 * @param string[optional] $authorName The new author for the comment.
	 * @param string[optional] $authorEmail The new email for the comment.
	 * @param string[optional] $authorWebsite The new website for the comment.
	 */
	public static function commentsUpdate($id, $status = null, $text = null, $authorName = null, $authorEmail = null, $authorWebsite = null)
	{
		// authorize
		if(API::authorize() && API::isValidRequestMethod('POST'))
		{
			// redefine
			$id = (int) $id;
			if($status !== null) $status = (string) $status;
			if($text !== null) $text = (string) $text;
			if($authorName !== null) $authorName = (string) $authorName;
			if($authorEmail !== null) $authorEmail = (string) $authorEmail;
			if($authorWebsite !== null) $authorWebsite = (string) $authorWebsite;

			// validate
			if($status === null && $text === null && $authorName === null && $authorEmail === null && $authorWebsite === null) API::output(API::ERROR, array('message' => 'No data provided.'));

			// update
			if($text !== null || $authorName !== null || $authorEmail != null || $authorWebsite !== null)
			{
				$item['id'] = (int) $id;
				if($text !== null) $item['text'] = $text;
				if($authorName !== null) $item['author'] = $authorName;
				if($authorEmail !== null) $item['email'] = $authorEmail;
				if($authorWebsite !== null) $item['website'] = $authorWebsite;

				// update the comment
				BackendBlogModel::updateComment($item);
			}

			// change the status if needed
			if($status !== null) BackendBlogModel::updateCommentStatuses(array($id), $status);
		}
	}

	/**
	 * Update the status for multiple comments at once.
	 *
	 * @param array $id The id/ids of the comment(s) to update.
	 * @param string $status The new status for the comment. Possible values are: published, moderation, spam.
	 */
	public static function commentsUpdateStatus($id, $status)
	{
		// authorize
		if(API::authorize() && API::isValidRequestMethod('POST'))
		{
			// redefine
			if(!is_array($id)) $id = (array) explode(',', $id);
			$status = (string) $status;

			// update statuses
			BackendBlogModel::updateCommentStatuses($id, $status);
		}
	}
}
