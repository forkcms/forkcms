<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action will delete a blogpost
 *
 * @author Dave Lens <dave.lens@netlash.com>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
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

			// delete the images
			SpoonFile::delete(FRONTEND_FILES_PATH . '/blog/images/source/' . $this->record['image']);
			BackendModel::deleteThumbnails(FRONTEND_FILES_PATH . '/blog/images', $this->record['image']);

			// trigger event
			BackendModel::triggerEvent($this->getModule(), 'after_delete', array('id' => $this->id));

			// delete search indexes
			if(is_callable(array('BackendSearchModel', 'removeIndex'))) BackendSearchModel::removeIndex($this->getModule(), $this->id);

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
