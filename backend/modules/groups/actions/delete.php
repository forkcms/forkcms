<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the delete-action, it will delete an item.
 *
 * @author Jeroen Van den Bossche <jeroenvandenbossche@gmail.com>
 */
class BackendGroupsDelete extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		$this->id = $this->getParameter('id', 'int');

		// group exists and id is not null?
		if($this->id !== null && BackendGroupsModel::exists($this->id))
		{
			parent::execute();

			// get record
			$this->record = BackendGroupsModel::get($this->id);

			// delete group
			BackendGroupsModel::delete($this->id);

			// trigger event
			BackendModel::triggerEvent($this->getModule(), 'after_delete', array('id' => $this->id));

			// item was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('index') . '&report=deleted&var=' . urlencode($this->record['name']));
		}

		// no item found, redirect to the overview with an error
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}
}
