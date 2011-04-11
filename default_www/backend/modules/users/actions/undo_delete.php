<?php

/**
 * This is the undo-delete-action, it will restore a deleted user
 *
 * @package		backend
 * @subpackage	users
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendUsersUndoDelete extends BackendBaseAction
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// get parameters
		$email = $this->getParameter('email', 'string');

		// does the user exist
		if($email !== null)
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// delete item
			if(BackendUsersModel::undoDelete($email))
			{
				// get user
				$user = new BackendUser(null, $email);

				// item was deleted, so redirect
				$this->redirect(BackendModel::createURLForAction('edit') . '&id=' . $user->getUserId() . '&report=restored&var=' . $user->getSetting('nickname') . '&highlight=row-' . $user->getUserId());
			}

			// invalid user
			else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
		}

		// no user found, throw an exceptions, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}
}

?>