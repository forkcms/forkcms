<?php

/**
 * BackendAnalyticsDeleteLandingPage
 * This action will delete a landing page
 *
 * @package		backend
 * @subpackage	analytics
 *
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.0
 */
class BackendAnalyticsDeleteLandingPage extends BackendBaseActionDelete
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

		// does the item exist
		if(BackendAnalyticsModel::existsLandingPage($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get all data for the user we want to edit
			$this->record = (array) BackendAnalyticsModel::getLandingPage($this->id);

			// reset some data
			if(empty($this->record)) $this->redirect(BackendModel::createURLForAction('index') .'&error=non-existing');

			// delete user
			BackendAnalyticsModel::deleteLandingPage($this->id);

			// user was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('index') .'&report=deleted&var='. urlencode($this->record['page_path']));
		}

		// something went wrong
		else $this->redirect(BackendModel::createURLForAction('index') .'&error=non-existing');
	}
}

?>