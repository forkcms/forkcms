<?php

/**
 * This action is used to perform mass actions on tags (delete, ...)
 *
 * @package		backend
 * @subpackage	tags
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendTagsMassAction extends BackendBaseAction
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
		if(!isset($_GET['id'])) $this->redirect(BackendModel::createURLForAction('index') . '&error=no-selection');

		// at least one id
		else
		{
			// redefine id's
			$aIds = (array) $_GET['id'];

			// delete comment(s)
			if($action == 'delete') BackendTagsModel::delete($aIds);
		}

		// redirect
		$this->redirect(BackendModel::createURLForAction('index') . '&report=deleted');
	}
}

?>