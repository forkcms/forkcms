<?php

/**
 * This is the delete-action, it will delete a template
 *
 * @package		backend
 * @subpackage	extensions
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendExtensionsDeleteThemeTemplate extends BackendBaseActionDelete
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
		if($this->id !== null && BackendExtensionsModel::existsTemplate($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// init var
			$success = false;

			// get template (we need the title)
			$item = BackendExtensionsModel::getTemplate($this->id);

			// valid template?
			if(!empty($item))
			{
				// delete the page
				$success = BackendExtensionsModel::deleteTemplate($this->id);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_delete_template', array('id' => $this->id));
			}

			// page is deleted, so redirect to the overview
			if($success) $this->redirect(BackendModel::createURLForAction('theme_templates') . '&theme=' . $item['theme'] . '&report=deleted-template&var=' . urlencode($item['label']));
			else $this->redirect(BackendModel::createURLForAction('theme_templates') . '&error=non-existing');
		}

		// something went wrong
		else $this->redirect(BackendModel::createURLForAction('theme_templates') . '&error=non-existing');
	}
}

?>