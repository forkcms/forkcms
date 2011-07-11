<?php

/**
 * This is the settings-action, it will display a form to set general pages settings
 *
 * @package		backend
 * @subpackage	pages
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Dave Lens <dave@netlash.com>
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.0
 */
class BackendPagesSettings extends BackendBaseActionEdit
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

		// load form
		$this->loadForm();

		// validates the form
		$this->validateForm();

		// parse
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Loads the settings form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// init settings form
		$this->frm = new BackendForm('settings');

		// add fields
		$this->frm->addCheckbox('meta_navigation', BackendModel::getModuleSetting('pages', 'meta_navigation', false));
		$this->frm->addCheckbox('children_blocks', BackendModel::getModuleSetting('pages', 'children_blocks', false));
	}


	/**
	 * Validates the settings form
	 *
	 * @return	void
	 */
	private function validateForm()
	{
		// form is submitted
		if($this->frm->isSubmitted())
		{
			// form is validated
			if($this->frm->isCorrect())
			{
				// set our settings
				BackendModel::setModuleSetting($this->getModule(), 'meta_navigation', (bool) $this->frm->getField('meta_navigation')->getValue());
				BackendModel::setModuleSetting($this->getModule(), 'children_blocks', (bool) $this->frm->getField('children_blocks')->getValue());

				// show children blocks extra if needed
				BackendPagesModel::changeExtraVisibility($this->getModule(), 'widget', 'children', !BackendModel::getModuleSetting($this->getModule(), 'children_blocks', false));

				// redirect to the settings page
				$this->redirect(BackendModel::createURLForAction('settings') . '&report=saved');
			}
		}
	}
}

?>