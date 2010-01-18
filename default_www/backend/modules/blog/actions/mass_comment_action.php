<?php

/**
 * BackendBlogCommentStatus
 *
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
		if(!isset($_GET['id'])) $this->redirect(BackendModel::createURLForAction('comments') .'&error=non-existing');

		// at least one id
		else
		{
			// redefine id's
			$aIds = (array) $_GET['id'];

			// delete comment(s)
			if($action == 'delete') BackendBlogModel::deleteComments($aIds);

			// other actions (status updates)
			else BackendBlogModel::updateCommentStatuses($aIds, $action);
		}

		// redirect
		$this->redirect(BackendModel::createURLForAction('comments') .'&report='. $action .'#tab'. ucfirst($from));
	}
}

?>