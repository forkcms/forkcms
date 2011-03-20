<?php

/**
 * This is the themes-action, it will display a form to set theme settings
 *
 * @package		backend
 * @subpackage	settings
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendSettingsThemes extends BackendBaseActionIndex
{
	/**
	 * The form instance
	 *
	 * @var	BackendForm
	 */
	private $frm;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load the form
		$this->loadForm();

		// validate the form
		$this->validateForm();

		// parse the datagrid
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('settingsThemes');

		// theme
		$this->frm->addDropdown('theme', BackendModel::getThemes(), BackendModel::getModuleSetting('core', 'theme', null));
		$this->frm->getField('theme')->setDefaultElement(BL::lbl('NoTheme'));
	}


	/**
	 * Parse the form
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse the form
		$this->frm->parse($this->tpl);
	}


	/**
	 * Validates the form
	 *
	 * @return	void
	 */
	private function validateForm()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// no errors ?
			if($this->frm->isCorrect())
			{
				// theme
				BackendModel::setModuleSetting('core', 'theme', $this->frm->getField('theme')->getValue());

				// assign report
				$this->tpl->assign('report', true);
				$this->tpl->assign('reportMessage', BL::msg('Saved'));
			}
		}
	}
}

?>