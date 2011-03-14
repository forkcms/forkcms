<?php

/**
 * This is the delete-action, it will delete a template
 *
 * @package		backend
 * @subpackage	pages
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendPagesDeleteTemplate extends BackendBaseActionDelete
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
		if($this->id !== null && BackendPagesModel::existsTemplate($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// init var
			$success = false;

			// get template (we need the title)
			$template = BackendPagesModel::getTemplate($this->id);

			// valid template?
			if(!empty($template))
			{
				// delete the page
				$success = BackendPagesModel::deleteTemplate($this->id);

				// build cache
				BackendPagesModel::buildCache(BL::getWorkingLanguage());
			}

			// page is deleted, so redirect to the overview
			if($success) $this->redirect(BackendModel::createURLForAction('templates') . '&report=deleted-template&var=' . urlencode($template['label']));
			else $this->redirect(BackendModel::createURLForAction('templates') . '&error=non-existing');
		}

		// something went wrong
		else $this->redirect(BackendModel::createURLForAction('templates') . '&error=non-existing');
	}
}

?>