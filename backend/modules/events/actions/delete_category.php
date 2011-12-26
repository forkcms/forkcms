<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action will delete a category
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendEventsDeleteCategory extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && BackendEventsModel::existsCategory($this->id))
		{
			// get data
			$this->record = (array) BackendEventsModel::getCategory($this->id);

			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();
			
			// delete item
			BackendEventsModel::deleteCategory($this->id);

			// trigger event
			BackendModel::triggerEvent($this->getModule(), 'after_delete_category', array('id' => $this->id));
			
			// user was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('categories') . '&report=deleted-category&var=' . urlencode($this->record['title']));
		}

		// something went wrong
		else $this->redirect(BackendModel::createURLForAction('categories') . '&error=non-existing');
	}
}
