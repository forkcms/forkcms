<?php

/**
 * SpotlightDelete
 *
 * This is the delete-action, it will delete an spotlight-item
 *
 * @package		backend
 * @subpackage	spotlight
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class SpotlightDelete extends BackendBaseActionDelete
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
		if(BackendSpotlightModel::exists($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get all data for the user we want to edit
			$this->record = (array) BackendSpotlightModel::get($this->id);

			// delete user
			BackendSpotlightModel::delete($this->id);

			// user was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('index') .'?report=delete&var='. $this->record['title']);
		}

		// no user found, throw an exceptions, because somebody is fucking with our url
		else $this->redirect(BackendModel::createURLForAction('index') .'?error=non-existing');
	}
}

?>