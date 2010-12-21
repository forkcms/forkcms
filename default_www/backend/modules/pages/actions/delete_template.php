<?php

/**
 * BackendPagesDeleteTemplate
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
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// init var
		$success = false;

		// get parameters
		$id = $this->getParameter('id', 'int');

		// validate
		if($id === null) $this->redirect(BackendModel::createURLForAction('templates') .'&error=non-existing');

		// get template (we need the title)
		$template = BackendPagesModel::getTemplate($id);

		// valid template?
		if(!empty($template))
		{
			// delete the page
			$success = BackendPagesModel::deleteTemplate($id);

			// build cache
			BackendPagesModel::buildCache(BL::getWorkingLanguage());
		}

		// page is deleted, so redirect to the overview
		if($success) $this->redirect(BackendModel::createURLForAction('templates') .'&report=deleted-template&var='. urlencode($template['label']));
		else $this->redirect(BackendModel::createURLForAction('templates') .'&error=non-existing');
	}
}

?>