<?php

/**
 * In this file we store all generic functions that we will be available through the API
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendBlogAPI
{
	/**
	 * Get the comments
	 *
	 * @return	array						An array with the comments
	 * @param	string[optional] $status	The type of comments to get. Possible values are: published, moderation, spam.
	 * @param	int[optional] $limit		The maximum number of items to retrieve.
	 * @param	int[optional] $offset		The offset.
	 */
	public static function commentsGet($status = null, $limit = 30, $offset = 0)
	{
		// authorize
		if(API::authorize())
		{
			// redefine
			if($status !== null) $status = (string) $status;
			$limit = (int) $limit;
			$offset = (int) $offset;

			// validate
			if($limit > 10000) API::output(API::ERROR, array('message' => 'Limit can\'t be larger then 10000.'));

			// get comments
			$comments = (array) BackendBlogModel::getAllCommentsForStatus($status, $limit, $offset);

			// init var
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

			// return
			return $return;
		}
	}


	/**
	 * Update a comment
	 *
	 * @return	void
	 * @param	int $id								The id of the comment.
	 * @param	string[optional] $status			The new status for the comment. Possible values are: published, moderation, spam.
	 * @param	string[optional] $text				The new text for the comment.
	 * @param	string[optional] $authorName		The new author for the comment.
	 * @param	string[optional] $authorEmail		The new email for the comment.
	 * @param	string[optional] $authorWebsite		The new website for the comment.
	 */
	public static function commentsUpdate($id, $status = null, $text = null, $authorName = null, $authorEmail = null, $authorWebsite = null)
	{
		// authorize
		if(API::authorize())
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
				if($text !== null) $item['text'] = $text;
				if($authorName !== null) $item['author'] = $authorName;
				if($authorEmail !== null) $item['email'] = $authorEmail;
				if($authorWebsite !== null) $item['website'] = $authorWebsite;

				// update the comment
				BackendBlogModel::updateComment($id, $item);
			}

			// change the status if needed
			if($status !== null) BackendBlogModel::updateCommentStatuses(array($id), $status);
		}
	}


	/**
	 * Update the status for multiple comments at once.
	 *
	 * @return	void
	 * @param	array $id		The id/ids of the comment(s) to update.
	 * @param	string $status	The new status for the comment. Possible values are: published, moderation, spam.
	 */
	public static function commentsUpdateStatus($id, $status)
	{
		// authorize
		if(API::authorize())
		{
			// redefine
			if(!is_array($id)) $id = array($id);
			$status = (string) $status;

			// update statuses
			BackendBlogModel::updateCommentStatuses($id, $status);
		}
	}
}

?>