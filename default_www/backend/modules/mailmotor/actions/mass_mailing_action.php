<?php

/**
 * BackendMailmotorMassMailingAction
 * This action is used to update one or more mailings (delete, ...)
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author 		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorMassMailingAction extends BackendBaseAction
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
		if(!isset($_GET['id'])) $this->redirect(BackendModel::createURLForAction('index') .'&error=no-items-selected');

		// at least one id
		else
		{
			// redefine id's
			$aIds = (array) $_GET['id'];

			// delete comment(s)
			if($action == 'delete') BackendMailmotorCMHelper::deleteMailings($aIds);
		}

		// redirect
		$this->redirect(BackendModel::createURLForAction('index') .'&report=delete_mailings');
	}
}

?>