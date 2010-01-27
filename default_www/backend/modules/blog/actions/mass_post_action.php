<?php

/**
 * BackendBlogMassPostAction
 *
 * This action is used to update one or more blogposts (delete, ...)
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author 		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendBlogMassPostAction extends BackendBaseAction
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

		// action to execute
		$action = SpoonFilter::getGetValue('action', array('delete'), 'delete');

		// no id's provided
		if(!isset($_GET['id'])) $this->redirect(BackendModel::createURLForAction('index') .'&error=non-existing');

		// at least one id
		else
		{
			// redefine id's
			$aIds = (array) $_GET['id'];

			// delete comment(s)
			if($action == 'delete') BackendBlogModel::delete($aIds);
		}

		// redirect
		$this->redirect(BackendModel::createURLForAction('index') .'&report='. $action);
	}
}

?>