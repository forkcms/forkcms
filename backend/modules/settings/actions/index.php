<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the index-action (default), it will display the setting-overview
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class BackendSettingsIndex extends BackendBaseActionIndex
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
	private $needsAkismet, $needsGoogleMaps;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// get some data
		$modulesThatRequireAkismet = BackendExtensionsModel::getModulesThatRequireAkismet();
		$modulesThatRequireGoogleMaps = BackendExtensionsModel::getModulesThatRequireGoogleMaps();

		// set properties
		$this->needsAkismet = (!empty($modulesThatRequireAkismet));
		$this->needsGoogleMaps = (!empty($modulesThatRequireGoogleMaps));

		$this->loadForm();
		$this->validateForm();
		$this->parse();
		$this->display();
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// list of default domains
		$defaultDomains = array(str_replace(array('http://', 'www.', 'https://'), '', SITE_URL));

		// create form
		$this->frm = new BackendForm('settingsIndex');

		// general settings
		$this->frm->addText('site_title', BackendModel::getModuleSetting('core', 'site_title_' . BL::getWorkingLanguage(), SITE_DEFAULT_TITLE));
		$this->frm->addTextarea('site_html_header', BackendModel::getModuleSetting('core', 'site_html_header', null), 'textarea code', 'textareaError code', true);
		$this->frm->addTextarea('site_html_footer', BackendModel::getModuleSetting('core', 'site_html_footer', null), 'textarea code', 'textareaError code', true);
		$this->frm->addTextarea('site_domains', implode("\n", (array) BackendModel::getModuleSetting('core', 'site_domains', $defaultDomains)), 'textarea code', 'textareaError code');

		// facebook settings
		$this->frm->addText('facebook_admin_ids', BackendModel::getModuleSetting('core', 'facebook_admin_ids', null));
		$this->frm->addText('facebook_application_id', BackendModel::getModuleSetting('core', 'facebook_app_id', null));
		$this->frm->addText('facebook_application_secret', BackendModel::getModuleSetting('core', 'facebook_app_secret', null));

		// ckfinder
		$this->frm->addText('ckfinder_license_name', BackendModel::getModuleSetting('core', 'ckfinder_license_name', null));
		$this->frm->addText('ckfinder_license_key', BackendModel::getModuleSetting('core', 'ckfinder_license_key', null));
		$this->frm->addText('ckfinder_image_max_width', BackendModel::getModuleSetting('core', 'ckfinder_image_max_width', 1600));
		$this->frm->addText('ckfinder_image_max_height', BackendModel::getModuleSetting('core', 'ckfinder_image_max_height', 1200));

		// api keys
		$this->frm->addText('fork_api_public_key', BackendModel::getModuleSetting('core', 'fork_api_public_key', null));
		$this->frm->addText('fork_api_private_key', BackendModel::getModuleSetting('core', 'fork_api_private_key', null));

		// date & time formats
		$this->frm->addDropdown('time_format', BackendModel::getTimeFormats(), BackendModel::getModuleSetting('core', 'time_format'));
		$this->frm->addDropdown('date_format_short', BackendModel::getDateFormatsShort(), BackendModel::getModuleSetting('core', 'date_format_short'));
		$this->frm->addDropdown('date_format_long', BackendModel::getDateFormatsLong(), BackendModel::getModuleSetting('core', 'date_format_long'));

		// number formats
		$this->frm->addDropdown('number_format', BackendModel::getNumberFormats(), BackendModel::getModuleSetting('core', 'number_format'));

		// create a list of the languages
		foreach(BackendModel::getModuleSetting('core', 'languages', array('en')) as $abbreviation)
		{
			// is this the default language
			$defaultLanguage = ($abbreviation == SITE_DEFAULT_LANGUAGE) ? true : false;

			// attributes
			$activeAttributes = array();
			$activeAttributes['id'] = 'active_language_' . $abbreviation;
			$redirectAttributes = array();
			$redirectAttributes['id'] = 'redirect_language_' . $abbreviation;

			// fetch label
			$label = BL::msg(mb_strtoupper($abbreviation), 'core');

			// default may not be unselected
			if($defaultLanguage)
			{
				// add to attributes
				$activeAttributes['disabled'] = 'disabled';
				$redirectAttributes['disabled'] = 'disabled';

				// overrule in $_POST
				if(!isset($_POST['active_languages']) || !is_array($_POST['active_languages'])) $_POST['active_languages'] = array(SITE_DEFAULT_LANGUAGE);
				elseif(!in_array($abbreviation, $_POST['active_languages'])) $_POST['active_languages'][] = $abbreviation;
				if(!isset($_POST['redirect_languages']) || !is_array($_POST['redirect_languages'])) $_POST['redirect_languages'] = array(SITE_DEFAULT_LANGUAGE);
				elseif(!in_array($abbreviation, $_POST['redirect_languages'])) $_POST['redirect_languages'][] = $abbreviation;
			}

			// add to the list
			$activeLanguages[] = array('label' => $label, 'value' => $abbreviation, 'attributes' => $activeAttributes, 'variables' => array('default' => $defaultLanguage));
			$redirectLanguages[] = array('label' => $label, 'value' => $abbreviation, 'attributes' => $redirectAttributes, 'variables' => array('default' => $defaultLanguage));
		}

		// create multilanguage checkbox
		$this->frm->addMultiCheckbox('active_languages', $activeLanguages, BackendModel::getModuleSetting('core', 'active_languages', array(SITE_MULTILANGUAGE)));
		$this->frm->addMultiCheckbox('redirect_languages', $redirectLanguages, BackendModel::getModuleSetting('core', 'redirect_languages', array(SITE_MULTILANGUAGE)));

		// api keys are not required for every module
		if($this->needsAkismet) $this->frm->addText('akismet_key', BackendModel::getModuleSetting('core', 'akismet_key', null));
		if($this->needsGoogleMaps) $this->frm->addText('google_maps_key', BackendModel::getModuleSetting('core', 'google_maps_key', null));
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		parent::parse();

		// show options
		if($this->needsAkismet) $this->tpl->assign('needsAkismet', true);
		if($this->needsGoogleMaps) $this->tpl->assign('needsGoogleMaps', true);

		// parse the form
		$this->frm->parse($this->tpl);

		// parse the warnings
		$this->parseWarnings();
	}

	/**
	 * Show the warnings based on the active modules & configured settings
	 */
	private function parseWarnings()
	{
		// get warnings
		$warnings = BackendSettingsModel::getWarnings();

		// assign warnings
		$this->tpl->assign('warnings', $warnings);
	}

	/**
	 * Validates the form
	 */
	private function validateForm()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// validate required fields
			$this->frm->getField('site_title')->isFilled(BL::err('FieldIsRequired'));

			// date & time
			$this->frm->getField('time_format')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('date_format_short')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('date_format_long')->isFilled(BL::err('FieldIsRequired'));

			// number
			$this->frm->getField('number_format')->isFilled(BL::err('FieldIsRequired'));

			// akismet key may be filled in
			if($this->needsAkismet && $this->frm->getField('akismet_key')->isFilled())
			{
				// key has changed
				if($this->frm->getField('akismet_key')->getValue() != BackendModel::getModuleSetting('core', 'akismet_key', null))
				{
					 // load akismet
					 require_once PATH_LIBRARY . '/external/akismet.php';

					 // create instance
					$akismet = new Akismet($this->frm->getField('akismet_key')->getValue(), SITE_URL);

					// invalid key
					if(!$akismet->verifyKey()) $this->frm->getField('akismet_key')->setError(BL::err('InvalidAPIKey'));
				}
			}

			// domains filled in
			if($this->frm->getField('site_domains')->isFilled())
			{
				// split on newlines
				$domains = explode("\n", trim($this->frm->getField('site_domains')->getValue()));

				// loop domains
				foreach($domains as $domain)
				{
					// strip funky stuff
					$domain = trim(str_replace(array('www.', 'http://', 'https://'), '', $domain));

					// invalid URL
					if(!SpoonFilter::isURL('http://' . $domain))
					{
						// set error
						$this->frm->getField('site_domains')->setError(BL::err('InvalidDomain'));

						// stop looping domains
						break;
					}
				}
			}

			if($this->frm->getField('ckfinder_image_max_width')->isFilled()) $this->frm->getField('ckfinder_image_max_width')->isInteger(BL::err('InvalidInteger'));
			if($this->frm->getField('ckfinder_image_max_height')->isFilled()) $this->frm->getField('ckfinder_image_max_height')->isInteger(BL::err('InvalidInteger'));

			// no errors ?
			if($this->frm->isCorrect())
			{
				// general settings
				BackendModel::setModuleSetting('core', 'site_title_' . BL::getWorkingLanguage(), $this->frm->getField('site_title')->getValue());
				BackendModel::setModuleSetting('core', 'site_html_header', $this->frm->getField('site_html_header')->getValue());
				BackendModel::setModuleSetting('core', 'site_html_footer', $this->frm->getField('site_html_footer')->getValue());

				// facebook settings
				BackendModel::setModuleSetting('core', 'facebook_admin_ids', ($this->frm->getField('facebook_admin_ids')->isFilled()) ? $this->frm->getField('facebook_admin_ids')->getValue() : null);
				BackendModel::setModuleSetting('core', 'facebook_app_id', ($this->frm->getField('facebook_application_id')->isFilled()) ? $this->frm->getField('facebook_application_id')->getValue() : null);
				BackendModel::setModuleSetting('core', 'facebook_app_secret', ($this->frm->getField('facebook_application_secret')->isFilled()) ? $this->frm->getField('facebook_application_secret')->getValue() : null);

				// ckfinder settings
				BackendModel::setModuleSetting('core', 'ckfinder_license_name', ($this->frm->getField('ckfinder_license_name')->isFilled()) ? $this->frm->getField('ckfinder_license_name')->getValue() : null);
				BackendModel::setModuleSetting('core', 'ckfinder_license_key', ($this->frm->getField('ckfinder_license_key')->isFilled()) ? $this->frm->getField('ckfinder_license_key')->getValue() : null);
				BackendModel::setModuleSetting('core', 'ckfinder_image_max_width', ($this->frm->getField('ckfinder_image_max_width')->isFilled()) ? $this->frm->getField('ckfinder_image_max_width')->getValue() : 1600);
				BackendModel::setModuleSetting('core', 'ckfinder_image_max_height', ($this->frm->getField('ckfinder_image_max_height')->isFilled()) ? $this->frm->getField('ckfinder_image_max_height')->getValue() : 1200);

				// api keys
				BackendModel::setModuleSetting('core', 'fork_api_public_key', $this->frm->getField('fork_api_public_key')->getValue());
				BackendModel::setModuleSetting('core', 'fork_api_private_key', $this->frm->getField('fork_api_private_key')->getValue());
				if($this->needsAkismet) BackendModel::setModuleSetting('core', 'akismet_key', $this->frm->getField('akismet_key')->getValue());
				if($this->needsGoogleMaps) BackendModel::setModuleSetting('core', 'google_maps_key', $this->frm->getField('google_maps_key')->getValue());

				// date & time formats
				BackendModel::setModuleSetting('core', 'time_format', $this->frm->getField('time_format')->getValue());
				BackendModel::setModuleSetting('core', 'date_format_short', $this->frm->getField('date_format_short')->getValue());
				BackendModel::setModuleSetting('core', 'date_format_long', $this->frm->getField('date_format_long')->getValue());

				// date & time formats
				BackendModel::setModuleSetting('core', 'number_format', $this->frm->getField('number_format')->getValue());

				// before we save the languages, we need to ensure that each language actually exists and may be chosen.
				$languages = array(SITE_DEFAULT_LANGUAGE);
				$activeLanguages = array_unique(array_merge($languages, $this->frm->getField('active_languages')->getValue()));
				$redirectLanguages = array_unique(array_merge($languages, $this->frm->getField('redirect_languages')->getValue()));

				// cleanup redirect-languages, by removing the values that aren't present in the active languages
				$redirectLanguages = array_intersect($redirectLanguages, $activeLanguages);

				// save active languages
				BackendModel::setModuleSetting('core', 'active_languages', $activeLanguages);
				BackendModel::setModuleSetting('core', 'redirect_languages', $redirectLanguages);

				// domains may not contain www, http or https. Therefor we must loop and create the list of domains.
				$siteDomains = array();

				// domains filled in
				if($this->frm->getField('site_domains')->isFilled())
				{
					// split on newlines
					$domains = explode("\n", trim($this->frm->getField('site_domains')->getValue()));

					// loop domains
					foreach($domains as $domain)
					{
						// strip funky stuff
						$siteDomains[] = trim(str_replace(array('www.', 'http://', 'https://'), '', $domain));
					}
				}

				// save domains
				BackendModel::setModuleSetting('core', 'site_domains', $siteDomains);

				// assign report
				$this->tpl->assign('report', true);
				$this->tpl->assign('reportMessage', BL::msg('Saved'));
			}
		}
	}
}
