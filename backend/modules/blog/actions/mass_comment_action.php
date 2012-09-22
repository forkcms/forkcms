<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action is used to update one or more comments (status, delete, ...)
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class BackendBlogMassCommentAction extends BackendBaseAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// current status
		$from = SpoonFilter::getGetValue('from', array('published', 'moderation', 'spam'), 'published');

		// action to execute
		$action = SpoonFilter::getGetValue('action', array('published', 'moderation', 'spam', 'delete'), 'spam');

		// no id's provided
		if(!isset($_GET['id'])) $this->redirect(BackendModel::createURLForAction('comments') . '&error=no-comments-selected');

		// redefine id's
		$ids = (array) $_GET['id'];

		// delete comment(s)
		if($action == 'delete') BackendBlogModel::deleteComments($ids);

		// spam
		elseif($action == 'spam')
		{
			// is the spamfilter active?
			if(BackendModel::getModuleSetting($this->URL->getModule(), 'spamfilter', false))
			{
				// get data
				$comments = BackendBlogModel::getComments($ids);

				// loop comments
				foreach($comments as $row)
				{
					// unserialize data
					$row['data'] = unserialize($row['data']);

					// check if needed data is available
					if(!isset($row['data']['server']['REMOTE_ADDR'])) continue;
					if(!isset($row['data']['server']['HTTP_USER_AGENT'])) continue;

					// build vars
					$userIp = $row['data']['server']['REMOTE_ADDR'];
					$userAgent = $row['data']['server']['HTTP_USER_AGENT'];
					$content = $row['text'];
					$author = $row['author'];
					$email = $row['email'];
					$url = (isset($row['website']) && $row['website'] != '') ? $row['website'] : null;
					$referrer = (isset($row['data']['server']['HTTP_REFERER'])) ? $row['data']['server']['HTTP_REFERER'] : null;
					$others = $row['data']['server'];

					// submit as spam
					BackendModel::submitSpam($userIp, $userAgent, $content, $author, $email, $url, null, 'comment', $referrer, $others);
				}
			}

			// set new status
			BackendBlogModel::updateCommentStatuses($ids, $action);
		}

		// other actions (status updates)
		else
		{
			// published?
			if($action == 'published')
			{
				// is the spamfilter active?
				if(BackendModel::getModuleSetting($this->URL->getModule(), 'spamfilter', false))
				{
					// get data
					$comments = BackendBlogModel::getComments($ids);

					// loop comments
					foreach($comments as $row)
					{
						// previous status is spam
						if($row['status'] == 'spam')
						{
							// unserialize data
							$row['data'] = unserialize($row['data']);

							// check if needed data is available
							if(!isset($row['data']['server']['REMOTE_ADDR'])) continue;
							if(!isset($row['data']['server']['HTTP_USER_AGENT'])) continue;

							// build vars
							$userIp = $row['data']['server']['REMOTE_ADDR'];
							$userAgent = $row['data']['server']['HTTP_USER_AGENT'];
							$content = $row['text'];
							$author = $row['author'];
							$email = $row['email'];
							$url = (isset($row['website']) && $row['website'] != '') ? $row['website'] : null;
							$referrer = (isset($row['data']['server']['HTTP_REFERER'])) ? $row['data']['server']['HTTP_REFERER'] : null;
							$others = $row['data']['server'];

							// submit as spam
							BackendModel::submitHam($userIp, $userAgent, $content, $author, $email, $url, null, 'comment', $referrer, $others);
						}
					}
				}
			}

			// set new status
			BackendBlogModel::updateCommentStatuses($ids, $action);
		}

		// define report
		$report = (count($ids) > 1) ? 'comments-' : 'comment-';

		// init var
		if($action == 'published') $report .= 'moved-published';
		if($action == 'moderation') $report .= 'moved-moderation';
		if($action == 'spam') $report .= 'moved-spam';
		if($action == 'delete') $report .= 'deleted';

		// redirect
		$this->redirect(BackendModel::createURLForAction('comments') . '&report=' . $report . '#tab' . SpoonFilter::ucfirst($from));
	}
}
