<?php

/**
 * BlogIndex
 *
 * This is the index-action (default), it will display the login screen
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendBlogSettings extends BackendBaseActionEdit
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


	private function loadForm()
	{
		$this->frm = new SpoonForm('settings');
		$this->frm->addCheckBox('spamfilter', BackendModel::getSetting('blog', 'spamfilter', false));
		$this->frm->addCheckBox('ping_services', BackendModel::getSetting('blog', 'ping_services', false));
		$this->frm->addTextField('rss_title', BackendModel::getSetting('blog', 'rss_title_'. BL::getWorkingLanguage()));
		$this->frm->addTextArea('rss_description', BackendModel::getSetting('blog', 'rss_description_'. BL::getWorkingLanguage()));
		$this->frm->addTextField('feedburner_url', BackendModel::getSetting('blog', 'feedburner_url_'. BL::getWorkingLanguage()));
		$this->frm->addButton('save', ucfirst(BL::getLabel('Save')), 'submit', 'inputButton button mainButton');
	}

	private function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			$this->frm->getField('rss_title')->isFilled(BL::getError('FieldIsRequired'));

			if($this->frm->getField('feedburner_url')->isFilled())
			{
				// @todo davy - valideren dat http er op zijn minst voorstaat...
				$this->frm->getField('feedburner_url')->isURL(BL::getError('InvalidURL'));
			}

			if($this->frm->isCorrect())
			{
				BackendModel::setSetting('blog', 'spamfilter', (bool) $this->frm->getField('spamfilter')->getValue());
				BackendModel::setSetting('blog', 'ping_services', (bool) $this->frm->getField('ping_services')->getValue());
				BackendModel::setSetting('blog', 'rss_title_'. BL::getWorkingLanguage(), $this->frm->getField('rss_title')->getValue());
				BackendModel::setSetting('blog', 'rss_description_'. BL::getWorkingLanguage(), $this->frm->getField('rss_description')->getValue());
				BackendModel::setSetting('blog', 'feedburner_url_'. BL::getWorkingLanguage(), $this->frm->getField('feedburner_url')->getValue());

				$this->redirect(BackendModel::createURLForAction('settings'));
			}
		}
	}
}

?>