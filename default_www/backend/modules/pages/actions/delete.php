<?php

/**
 * BackendPagesDelete
 * This is the delete-action, it will delete a page
 *
 * @package		backend
 * @subpackage	pages
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendPagesDelete extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// init var
		$success = false;

		// get parameters
		$id = $this->getParameter('id', 'int');

		// validate
		if($id === null) $this->redirect(BackendModel::createURLForAction('templates') .'&error=non-existing');

		// cannot have children
		if(BackendPagesModel::getFirstChildId($id) !== false) $this->redirect(BackendModel::createURLForAction('templates') .'&error=non-existing');

		// get page (we need the title)
		$page = BackendPagesModel::get($id);

		// valid page?
		if(!empty($page))
		{
			// delete the page
			$success = BackendPagesModel::delete($id);

			// delete search indexes
			if(method_exists('BackendSearchModel', 'removeIndex')) BackendSearchModel::removeIndex('pages', $id);

			// build cache
			BackendPagesModel::buildCache();
		}

		// page is deleted, so redirect to the overview
		if($success) $this->redirect(BackendModel::createURLForAction('index') .'&id='. $page['parent_id'] .'&report=deleted&var='. urlencode($page['title']));
		else $this->redirect(BackendModel::createURLForAction('edit') .'&error=non-existing');
	}
}

?>