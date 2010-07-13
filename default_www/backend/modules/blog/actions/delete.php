<?php

/**
 * BackendBlogDelete
 * This action will delete a blogpost
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author 		Dave Lens <dave@netlash.com>
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendBlogDelete extends BackendBaseActionDelete
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
		if(BackendBlogModel::exists($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get all data for the item we want to edit
			$this->record = (array) BackendBlogModel::get($this->id);

			// reset some data
			if(empty($this->record)) $this->record['title'] = '';

			// delete item
			BackendBlogModel::delete($this->id);

			// delete search indexes
			if(method_exists('BackendSearchModel', 'removeIndex')) BackendSearchModel::removeIndex('blog', $this->id);

			// user was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('index') .'&report=deleted&var='. urlencode($this->record['title']));
		}

		// something went wrong
		else $this->redirect(BackendModel::createURLForAction('index') .'&error=non-existing');
	}
}

?>