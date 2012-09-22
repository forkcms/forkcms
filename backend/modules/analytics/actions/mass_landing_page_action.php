<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action is used to perform mass actions on landing pages (delete, ...)
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class BackendAnalyticsMassLandingPageAction extends BackendBaseAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$action = SpoonFilter::getGetValue('action', array('delete'), 'delete');

		// no id's provided
		if(!isset($_GET['id']))
		{
			$this->redirect(BackendModel::createURLForAction('landing_pages') . '&error=no-items-selected');
		}

		// at least one id
		else
		{
			// delete items
			if($action == 'delete') BackendAnalyticsModel::deleteLandingPage((array) $_GET['id']);
		}

		// redirect
		$this->redirect(BackendModel::createURLForAction('landing_pages') . '&report=' . $action);
	}
}
