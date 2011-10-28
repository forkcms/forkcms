<?php

/**
 * This action will delete a category
 *
 * @package		backend
 * @subpackage	faq
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.1
 */
class BackendFaqDeleteCategory extends BackendBaseActionDelete
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
		if($this->id !== null && BackendFaqModel::existsCategory($this->id))
		{
			// get data
			$this->record = (array) BackendFaqModel::getCategory($this->id);

			// allowed to delete the category?
			if(BackendFaqModel::deleteCategoryAllowed($this->id))
			{
				// call parent, this will probably add some general CSS/JS or other required files
				parent::execute();

				// delete item
				BackendFaqModel::deleteCategory($this->id);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_delete_category', array('item' => $this->record));

				// category was deleted, so redirect
				$this->redirect(BackendModel::createURLForAction('categories') . '&report=deleted-category&var=' . urlencode($this->record['title']));
			}

			// delete category not allowed
			else $this->redirect(BackendModel::createURLForAction('categories') . '&error=delete-category-not-allowed&var=' . urlencode($this->record['title']));
		}

		// something went wrong
		else $this->redirect(BackendModel::createURLForAction('categories') . '&error=non-existing');
	}
}

?>