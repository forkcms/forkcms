<?php

/**
 * BackendUsersDelete
 * This is the delete-action, it will deactivate and mark the user as deleted
 *
 * @package		backend
 * @subpackage	users
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
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
		if(BackendUsersModel::exists($this->id) && BackendAuthentication::getUser()->getUserId() != $this->id)
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get all data for the item we want to delete
			$this->record = (array) BackendUsersModel::get($this->id);

			// delete item
			BackendUsersModel::delete($this->id);

			// item was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('index') .'&report=deleted&var='. $this->record['username']);
		}

		// no user found, throw an exceptions, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') .'&error=non-existing');
	}
}

?>