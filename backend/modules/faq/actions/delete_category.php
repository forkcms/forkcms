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
 * @author Lester Lievens <lester.lievens@netlash.com>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class BackendFaqDeleteCategory extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && BackendFaqModel::existsCategory($this->id))
		{
			$this->record = (array) BackendFaqModel::getCategory($this->id);

			if(BackendFaqModel::deleteCategoryAllowed($this->id))
			{
				parent::execute();

				// delete item
				BackendFaqModel::deleteCategory($this->id);
				BackendModel::triggerEvent($this->getModule(), 'after_delete_category', array('item' => $this->record));

				// category was deleted, so redirect
				$this->redirect(BackendModel::createURLForAction('categories') . '&report=deleted-category&var=' . urlencode($this->record['title']));
			}
			else $this->redirect(BackendModel::createURLForAction('categories') . '&error=delete-category-not-allowed&var=' . urlencode($this->record['title']));
		}
		else $this->redirect(BackendModel::createURLForAction('categories') . '&error=non-existing');
	}
}
