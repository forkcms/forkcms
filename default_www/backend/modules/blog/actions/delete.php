<?php

/**
 * This action will delete a blogpost
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author		Dave Lens <dave@netlash.com>
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class BackendBlogDelete extends BackendBaseActionDelete
{
	/**
	 * The id of the category where is filtered on
	 *
	 * @var	int
	 */
	private $categoryId;


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
		if($this->id !== null && BackendBlogModel::exists($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// set category id
			$this->categoryId = SpoonFilter::getGetValue('category', null, null, 'int');
			if($this->categoryId == 0) $this->categoryId = null;

			// get data
			$this->record = (array) BackendBlogModel::get($this->id);

			// delete item
			BackendBlogModel::delete($this->id);

			// delete search indexes
			if(is_callable(array('BackendSearchModel', 'removeIndex'))) BackendSearchModel::removeIndex('blog', $this->id);

			// build redirect URL
			$redirectUrl = BackendModel::createURLForAction('index') . '&report=deleted&var=' . urlencode($this->record['title']);

			// append to redirect URL
			if($this->categoryId != null) $redirectUrl .= '&category=' . $this->categoryId;

			// item was deleted, so redirect
			$this->redirect($redirectUrl);
		}

		// something went wrong
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}
}

?>