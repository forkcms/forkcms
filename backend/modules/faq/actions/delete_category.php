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
 * @author Lester Lievens <lester@netlash.com>
 */
class BackendFaqDeleteCategory extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && BackendFaqModel::existsCategory($this->id))
		{
			parent::execute();

			// get item
			$this->record = BackendFaqModel::getCategory($this->id);

			// delete item
			BackendFaqModel::deleteCategory($this->id);

			// trigger event
			BackendModel::triggerEvent($this->getModule(), 'after_delete_category', array('id' => $this->id));

			// item was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('categories') . '&report=deleted&var=' . urlencode($this->record['name']));
		}

		// something went wrong
		else $this->redirect(BackendModel::createURLForAction('categories') . '&error=non-existing');
	}
}
