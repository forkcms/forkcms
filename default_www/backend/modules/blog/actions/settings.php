<?php

/**
 * BackendBlogSettings
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @author		Dave Lens <dave@netlash.com>
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


	/**
	 * Loads the settings form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// init settings form
		$this->frm = new BackendForm('settings');

		// add fields for spam
		$this->frm->addCheckBox('spamfilter', BackendModel::getSetting('blog', 'spamfilter', false));

		// add fields for comments
		$this->frm->addCheckBox('allow_comments', BackendModel::getSetting('blog', 'allow_comments', false));

		// add fields for comments
		$this->frm->addCheckBox('moderation', BackendModel::getSetting('blog', 'moderation', false));

		// add fields for SEO
		$this->frm->addCheckBox('ping_services', BackendModel::getSetting('blog', 'ping_services', false));

		// add fields for RSS
		$this->frm->addTextField('rss_title', BackendModel::getSetting('blog', 'rss_title_'. BL::getWorkingLanguage()));
		$this->frm->addTextArea('rss_description', BackendModel::getSetting('blog', 'rss_description_'. BL::getWorkingLanguage()));
		$this->frm->addTextField('feedburner_url', BackendModel::getSetting('blog', 'feedburner_url_'. BL::getWorkingLanguage()));
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
			// shorten fields
			$feedburnerURL = $this->frm->getField('feedburner_url');

			// validation
			$this->frm->getField('rss_title')->isFilled(BL::getError('FieldIsRequired'));

			// feedburner URL is set
			if($feedburnerURL->isFilled())
			{
				// check if http:// is set and add if necessary
				$feedburner = !strstr($feedburnerURL->getValue(), 'http://') ? 'http://'. $feedburnerURL->getValue() : $feedburnerURL->getValue();

				// check if feedburner URL is valid
				if(!SpoonFilter::isURL($feedburner)) $feedburnerURL->addError('InvalidURL');
			}

			// init variable
			else $feedburner = null;

			// form is validated
			if($this->frm->isCorrect())
			{
				// set our settings
				BackendModel::setSetting('blog', 'spamfilter', (bool) $this->frm->getField('spamfilter')->getValue());
				BackendModel::setSetting('blog', 'allow_comments', (bool) $this->frm->getField('allow_comments')->getValue());
				BackendModel::setSetting('blog', 'moderation', (bool) $this->frm->getField('moderation')->getValue());
				BackendModel::setSetting('blog', 'ping_services', (bool) $this->frm->getField('ping_services')->getValue());
				BackendModel::setSetting('blog', 'rss_title_'. BL::getWorkingLanguage(), $this->frm->getField('rss_title')->getValue());
				BackendModel::setSetting('blog', 'rss_description_'. BL::getWorkingLanguage(), $this->frm->getField('rss_description')->getValue());
				if($feedburner !== null) BackendModel::setSetting('blog', 'feedburner_url_'. BL::getWorkingLanguage(), $feedburner);

				// redirect to the settings page
				$this->redirect(BackendModel::createURLForAction('settings'));
			}
		}
	}
}

?>