<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the delete-action, it will delete a page
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendPagesDelete extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && BackendPagesModel::exists($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// init var
			$success = false;

			// cannot have children
			if(BackendPagesModel::getFirstChildId($this->id) !== false) $this->redirect(BackendModel::createURLForAction('edit') . '&error=non-existing');

			// get page (we need the title)
			$page = BackendPagesModel::get($this->id);

			// valid page?
			if(!empty($page))
			{
				// delete the page
				$success = BackendPagesModel::delete($this->id);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_delete', array('id' => $this->id));

				// delete search indexes
				BackendSearchModel::removeIndex($this->getModule(), $this->id);

				// build cache
				BackendPagesModel::buildCache(BL::getWorkingLanguage());
			}

			// page is deleted, so redirect to the overview
			if($success) $this->redirect(BackendModel::createURLForAction('index') . '&id=' . $page['parent_id'] . '&report=deleted&var=' . urlencode($page['title']));
			else $this->redirect(BackendModel::createURLForAction('edit') . '&error=non-existing');
		}

		// something went wrong
		else $this->redirect(BackendModel::createURLForAction('edit') . '&error=non-existing');
	}
}
