<?php

/**
 * BackendBlogCommentStatus
 *
 * This action is used to move one or more comments to published, unmoderated or spam status.
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendBlogCommentStatus extends BackendBaseAction
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

		// new status
		$to = SpoonFilter::getGetValue('to', array('published', 'moderation', 'spam'), 'spam');

		// no id's provided
		if(!isset($_GET['id'])) $this->redirect(BackendModel::createURLForAction('comments') .'&error=non-existing');

		// at least one id
		else
		{
			// redefine id's
			$aIds = (array) $_GET['id'];

			// update the status for every id
			foreach($aIds as $id) BackendBlogModel::updateCommentStatus($id, $to);
		}

		// redirect
		$this->redirect(BackendModel::createURLForAction('comments') .'&report='. $to .'#tab'. ucfirst($from));
	}
}

?>