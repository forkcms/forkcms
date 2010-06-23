<?php

/**
 * BackendBlogMassCommentAction
 * This action is used to update one or more comments (status, delete, ...)
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendBlogMassCommentAction extends BackendBaseAction
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// current status
		$from = SpoonFilter::getGetValue('from', array('published', 'moderation', 'spam'), 'published');

		// action to execute
		$action = SpoonFilter::getGetValue('action', array('published', 'moderation', 'spam', 'delete'), 'spam');

		// no id's provided
		if(!isset($_GET['id'])) $this->redirect(BackendModel::createURLForAction('comments') .'&error=no-comments-selected');

		// at least one id
		else
		{
			// redefine id's
			$ids = (array) $_GET['id'];

			// delete comment(s)
			if($action == 'delete') BackendBlogModel::deleteComments($ids);

			// other actions (status updates)
			else BackendBlogModel::updateCommentStatuses($ids, $action);

			// define report
			$report = (count($ids) > 1) ? 'comments-' : 'comment-';

			// init var
			if($action == 'published') $report .= 'moved-published';
			if($action == 'moderation') $report .= 'moved-moderation';
			if($action == 'spam') $report .= 'moved-spam';
			if($action == 'delete') $report .= 'deleted';

			// redirect
			$this->redirect(BackendModel::createURLForAction('comments') .'&report='. $report .'#tab'. ucfirst($from));
		}
	}
}

?>