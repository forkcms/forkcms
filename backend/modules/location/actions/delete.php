<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action will delete an item
 *
 * @author Matthias Mullie <matthias@mullie.eu>
 */
class BackendLocationDelete extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && BackendLocationModel::exists($this->id))
		{
			parent::execute();

			// get all data for the item we want to edit
			$this->record = (array) BackendLocationModel::get($this->id);

			// delete item
			BackendLocationModel::delete($this->id);

			// delete search indexes
			// @todo why is this commented out
			// BackendSearchModel::removeIndex($this->getModule(), $this->id);

			// trigger event
			BackendModel::triggerEvent($this->getModule(), 'after_delete', array('id' => $this->id));

			// user was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('index') . '&report=deleted&var=' . urlencode($this->record['title']));
		}

		// something went wrong
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}
}
