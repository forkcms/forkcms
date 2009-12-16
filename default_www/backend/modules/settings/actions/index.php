<?php

/**
 * UsersIndex
 *
 * This is the index-action (default), it will display the users-overview
 *
 * @package		backend
 * @subpackage	users
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class SettingsIndex extends BackendBaseActionIndex
{
	/**
	 * The form instance
	 *
	 * @var	BackendForm
	 */
	private $frm;


	/**
	 * Should we show boxes for their API keys
	 *
	 * @var	bool
	 */
	private $needsAkismet,
			$needsGoogleMaps;


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
		// init vars
		$activeModules = BackendModel::getModules(true);
		$modulesThatRequireAkismet = array('blog'); // @todo	prefill in a decent way
		$modulesThatRequireGoogleMaps = array('blog'); // @todo	prefill in a decent way
		$this->needsAkismet = false;
		$this->needsGoogleMaps = false;

		// loop active modules
		foreach($activeModules as $module)
		{
			if(in_array($module, $modulesThatRequireAkismet)) $this->needsAkismet = true;
			if(in_array($module, $modulesThatRequireGoogleMaps)) $this->needsGoogleMaps = true;
		}

		// create form
		$this->frm = new BackendForm('settings');

		// create elements
		$this->frm->addTextField('core_website_title', BackendModel::getSetting('core', 'website_title_'. BL::getWorkingLanguage(), SITE_DEFAULT_TITLE));
		$this->frm->addTextField('core_email', BackendModel::getSetting('core', 'email_'. BL::getWorkingLanguage(), null));
		// @todo	languages
		$this->frm->addTextArea('core_site_wide_html', BackendModel::getSetting('core', 'site_wide_html', null));
		$this->frm->addTextArea('core_site_domains', BackendModel::getSetting('core', 'site_domains', null));
		$this->frm->addTextField('core_fork_api_public_key', BackendModel::getSetting('core', 'fork_api_public_key', null));
		$this->frm->addTextField('core_fork_api_private_key', BackendModel::getSetting('core', 'fork_api_private_key', null));
		if($this->needsAkismet) $this->frm->addTextField('core_akismet_key', BackendModel::getSetting('core', 'core_akismet_key', null));
		if($this->needsGoogleMaps) $this->frm->addTextField('core_google_maps_key', BackendModel::getSetting('core', 'core_google_maps_key', null));

		$this->frm->addButton('save', ucfirst(BL::getLabel('Save')));
	}


	/**
	 * Parse the form
	 *
	 * @return	void
	 */
	private function parse()
	{
		// show options
		if($this->needsAkismet) $this->tpl->assign('needsAkismet', true);
		if($this->needsGoogleMaps) $this->tpl->assign('needsGoogleMaps', true);

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
			// validate required fields
			$this->frm->getField('core_website_title')->isFilled(BL::getError('FieldIsRequired'));
			$this->frm->getField('core_email')->isEmail(BL::getError('EmailIsInvalid'));
			// @todo	languages

			// no errors ?
			if($this->frm->isCorrect())
			{
				// cleanup domains
				$siteDomains = (array) explode("\n", $this->frm->getField('core_site_domains')->getValue());
				foreach($siteDomains as $key => $value) $siteDomains[$key] = trim($value);

				// store settings
				BackendModel::setSetting('core', 'website_title_'. BL::getWorkingLanguage(), $this->frm->getField('core_website_title')->getValue());
				BackendModel::setSetting('core', 'email_'. BL::getWorkingLanguage(), $this->frm->getField('core_email')->getValue());
				BackendModel::setSetting('core', 'site_wide_html', $this->frm->getField('core_site_wide_html')->getValue());
				BackendModel::setSetting('core', 'site_domains', $siteDomains);
				BackendModel::setSetting('core', 'fork_api_public_key', $this->frm->getField('core_fork_api_public_key')->getValue());
				BackendModel::setSetting('core', 'fork_api_private_key', $this->frm->getField('core_fork_api_private_key')->getValue());
				BackendModel::setSetting('core', 'core_akismet_key', $this->frm->getField('core_akismet_key')->getValue());
				BackendModel::setSetting('core', 'core_google_maps_key', $this->frm->getField('core_google_maps_key')->getValue());

				// assign report
				$this->tpl->assign('formSucces', true);
			}
		}
	}
}

?>