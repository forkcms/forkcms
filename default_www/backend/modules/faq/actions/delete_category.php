<?php

/**
 * This action will delete a category
 *
 * @package		backend
 * @subpackage	faq
 *
 * @author		Lester Lievens <lester@netlash.com>
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
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get item
			$this->record = BackendFaqModel::getCategory($this->id);

			// delete item
			BackendFaqModel::deleteCategory($this->id);

			// item was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('categories') . '&report=deleted&var=' . urlencode($this->record['name']));
		}

		// something went wrong
		else $this->redirect(BackendModel::createURLForAction('categories') . '&error=non-existing');
	}
}

?>