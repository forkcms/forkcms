<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action will delete a landing page
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class BackendAnalyticsDeleteLandingPage extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && BackendAnalyticsModel::existsLandingPage($this->id))
		{
			parent::execute();
			$this->record = (array) BackendAnalyticsModel::getLandingPage($this->id);

			// delete item
			BackendAnalyticsModel::deleteLandingPage($this->id);

			// trigger event
			BackendModel::triggerEvent($this->getModule(), 'after_delete_landing_page', array('id' => $this->id));

			// item was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('index') . '&report=deleted&var=' . urlencode($this->record['page_path']));
		}

		// something went wrong
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}
}
