<?php

/**
 * This action is used to perform mass actions on landing pages (delete, ...)
 *
 * @package		backend
 * @subpackage	analytics
 *
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.0
 */
class BackendAnalyticsMassLandingPageAction extends BackendBaseAction
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
		if(!isset($_GET['id'])) $this->redirect(BackendModel::createURLForAction('landing_pages') . '&error=no-items-selected');

		// at least one id
		else
		{
			// redefine id's
			$ids = (array) $_GET['id'];

			// delete items
			if($action == 'delete') BackendAnalyticsModel::deleteLandingPage($ids);
		}

		// redirect
		$this->redirect(BackendModel::createURLForAction('landing_pages') . '&report=' . $action);
	}
}

?>