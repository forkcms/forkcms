<?php

/**
 * BackendUsersDelete
 *
 * This is the delete-action, it will deactivate and set the user as deleted
 *
 * @package		backend
 * @subpackage	users
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
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

		// does the user exists
		if(BackendUsersModel::exists($this->id) && BackendAuthentication::getUser()->getUserId() != $this->id)
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get all data for the user we want to edit
			$this->record = (array) BackendUsersModel::get($this->id);

			// delete user
			BackendUsersModel::delete($this->id);

			// user was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('index') .'&report=delete&var='. $this->record['username']);
		}

		// no user found, throw an exceptions, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') .'&error=non-existing');
	}
}
?>