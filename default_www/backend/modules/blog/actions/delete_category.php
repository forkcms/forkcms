<?php

/**
 * BackendBlogDeleteCategory
 * This action will delete a category
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendBlogDeleteCategory extends BackendBaseActionDelete
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
		if(BackendBlogModel::existsCategory($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get all data for the item we want to edit
			$this->record = (array) BackendBlogModel::getCategory($this->id);

			// delete item
			BackendBlogModel::deleteCategory($this->id);

			// user was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('categories') .'&report=deleted-category&var='. urlencode($this->record['name']));
		}

		// something went wrong
		else $this->redirect(BackendModel::createURLForAction('categories') .'&error=non-existing');
	}
}

?>