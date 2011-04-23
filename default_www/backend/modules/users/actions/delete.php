<?php

/**
 * This is the delete-action, it will deactivate and mark the user as deleted
 *
 * @package		backend
 * @subpackage	users
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendUsersDelete extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the user exist
		if($this->id !== null && BackendUsersModel::exists($this->id) && BackendAuthentication::getUser()->getUserId() != $this->id)
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get data
			$user = new BackendUser($this->id);

			// God-users can't be deleted
			if($user->isGod()) $this->redirect(BackendModel::createURLForAction('index') . '&error=cant-delete-god');

			// delete item
			BackendUsersModel::delete($this->id);

			// item was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('index') . '&report=deleted&var=' . $user->getSetting('nickname'));
		}

		// no user found, throw an exceptions, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}
}

?>