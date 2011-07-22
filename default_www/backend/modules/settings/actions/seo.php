<?php

/**
 * This is the SEO-action, it will display a form to set SEO settings
 *
 * @package		backend
 * @subpackage	settings
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class BackendSettingsSeo extends BackendBaseActionIndex
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

		// parse
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
		$this->frm = new BackendForm('settingsSeo');

		$this->frm->addCheckbox('seo_noodp', BackendModel::getModuleSetting('core', 'seo_noodp', false));
		$this->frm->addCheckbox('seo_noydir', BackendModel::getModuleSetting('core', 'seo_noydir', false));
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
				// smtp settings
				BackendModel::setModuleSetting('core', 'seo_noodp', $this->frm->getField('seo_noodp')->getValue());
				BackendModel::setModuleSetting('core', 'seo_noydir', $this->frm->getField('seo_noydir')->getValue());

				// assign report
				$this->tpl->assign('report', true);
				$this->tpl->assign('reportMessage', BL::msg('Saved'));
			}
		}
	}
}

?>