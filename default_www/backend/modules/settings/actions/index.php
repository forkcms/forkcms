<?php

/**
 * SettingsIndex
 *
 * This is the index-action (default), it will display the setting-overview
 *
 * @package		backend
 * @subpackage	settings
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
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
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// loads the requirements
		$this->loadRequirements();

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
		// list of default domains
		$defaultDomains = array(str_replace(array('http://', 'www.', 'https://'), '', SITE_URL));

		// create form
		$this->frm = new BackendForm('settings');

		// create elements
		$this->frm->addTextField('site_title', BackendModel::getSetting('core', 'site_title_'. BL::getWorkingLanguage(), SITE_DEFAULT_TITLE));
		$this->frm->addTextField('email', BackendModel::getSetting('core', 'email_'. BL::getWorkingLanguage(), null));
		$this->frm->addTextArea('site_wide_html', BackendModel::getSetting('core', 'site_wide_html', null), 'inputTextarea', 'inputTextareaError', true);
		$this->frm->addButton('save', ucfirst(BL::getLabel('Save')), 'submit', 'inputButton button mainButton');
		$this->frm->addTextArea('site_domains', implode("\n", (array) BackendModel::getSetting('core', 'site_domains', $defaultDomains)));
		$this->frm->addTextField('fork_api_public_key', BackendModel::getSetting('core', 'fork_api_public_key', null));
		$this->frm->addTextField('fork_api_private_key', BackendModel::getSetting('core', 'fork_api_private_key', null));

		/*
		 * We need to create a list of the languages
		 * @todo davy - correcte vertaling op basis van de lijsten in SpoonLocale
		 */
		foreach(BackendModel::getSetting('core', 'languages', array('nl')) as $abbreviation)
		{
			// is this the default language
			$defaultLanguage = ($abbreviation == BackendModel::getSetting('core', 'default_language', 'nl')) ? true : false;

			// attributes
			$attributes = array();
			$attributes['id'] = 'language_'. $abbreviation;

			// default may not be unselected
			if($defaultLanguage)
			{
				// add to attributes
				$attributes['disabled'] = 'disabled';

				// overrule in $_POST
				if(!isset($_POST['languages']) || !is_array($_POST['languages'])) $_POST['languages'] = array('nl');
				elseif(!in_array($abbreviation, $_POST['languages'])) $_POST['languages'][] = $abbreviation;
			}

			// add to the list
			$languages[] = array('label' => $abbreviation, 'value' => $abbreviation, 'attributes' => $attributes, 'variables' => array('default' => $defaultLanguage));
		}

		// create multilanguage checkbox
		$this->frm->addMultiCheckBox('languages', $languages, BackendModel::getSetting('core', 'active_languages', array('nl')));

		// api keys are not required for every module
		if($this->needsAkismet) $this->frm->addTextField('akismet_key', BackendModel::getSetting('core', 'akismet_key', null));
		if($this->needsGoogleMaps) $this->frm->addTextField('google_maps_key', BackendModel::getSetting('core', 'google_maps_key', null));
	}


	/**
	 * Loads the requirements
	 *
	 * @return	void
	 */
	private function loadRequirements()
	{
		// init vars
		$activeModules = BackendModel::getModules(true);
		$modulesThatRequireAkismet = BackendSettingsModel::getModulesThatRequireAkismet();
		$modulesThatRequireGoogleMaps = BackendSettingsModel::getModulesThatRequireGoogleMaps();
		$this->needsAkismet = false;
		$this->needsGoogleMaps = false;

		// loop active modules
		foreach($activeModules as $module)
		{
			if(in_array($module, $modulesThatRequireAkismet)) $this->needsAkismet = true;
			if(in_array($module, $modulesThatRequireGoogleMaps)) $this->needsGoogleMaps = true;
		}
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

		// parse the warnings
		$this->parseWarnings();
	}


	/**
	 * Show the warnings based on the active modules & configured settings
	 *
	 * @return	void
	 */
	private function parseWarnings()
	{
		// init vars
		$warnings = array();
		$activeModules = BackendModel::getModules(true);

		// add warnings
		$warnings = array_merge($warnings, BackendModel::checkSettings());

		// loop active modules
		foreach($activeModules as $module)
		{
			// model class
			$class = 'Backend'. ucfirst($module) .'Model';

			// model file exists
			if(SpoonFile::exists(BACKEND_MODULES_PATH .'/'. $module .'/engine/model.php'))
			{
				// require class
				require_once BACKEND_MODULES_PATH .'/'. $module .'/engine/model.php';
			}

			// method exists
			if(method_exists($class, 'checkSettings'))
			{
				// add possible warnings
				$warnings = array_merge($warnings, call_user_func(array($class, 'checkSettings')));
			}
		}

		// assign warnings
		$this->tpl->assign('warnings', $warnings);
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
			$this->frm->getField('site_title')->isFilled(BL::getError('FieldIsRequired'));
			$this->frm->getField('email')->isEmail(BL::getError('EmailIsInvalid'));

			// akismet key may be filled in
			if($this->needsAkismet && $this->frm->getField('akismet_key')->isFilled())
			{
				// key has changed
				if($this->frm->getField('akismet_key')->getValue() != BackendModel::getSetting('core', 'akismet_key', null))
				{
					 // load akismet
					 require_once PATH_LIBRARY .'/external/akismet.php';

					 // create instance
					$akismet = new Akismet($this->frm->getField('akismet_key')->getValue(), SITE_URL);

					// invalid key
					if(!$akismet->verifyKey()) $this->frm->getField('akismet_key')->setError(BL::getError('InvalidAPIKey'));
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
					if(!SpoonFilter::isURL($domain))
					{
						// set error
						$this->frm->getField('site_domains')->setError(BL::getError('InvalidDomain'));

						// stop looping domains
						break;
					}
				}
			}

			// no errors ?
			if($this->frm->isCorrect())
			{
				// save general settings
				BackendModel::setSetting('core', 'site_title_'. BL::getWorkingLanguage(), $this->frm->getField('site_title')->getValue());
				BackendModel::setSetting('core', 'email_'. BL::getWorkingLanguage(), $this->frm->getField('email')->getValue());
				BackendModel::setSetting('core', 'site_wide_html', $this->frm->getField('site_wide_html')->getValue());
				BackendModel::setSetting('core', 'fork_api_public_key', $this->frm->getField('fork_api_public_key')->getValue());
				BackendModel::setSetting('core', 'fork_api_private_key', $this->frm->getField('fork_api_private_key')->getValue());
				if($this->needsAkismet) BackendModel::setSetting('core', 'akismet_key', $this->frm->getField('akismet_key')->getValue());
				if($this->needsGoogleMaps) BackendModel::setSetting('core', 'google_maps_key', $this->frm->getField('google_maps_key')->getValue());

				/*
				 * Before we save the languages, we need to ensure that each language actually
				 * exists and may be chosen.
				 */
				$languages = array(BackendModel::getSetting('core', 'default_language', null));

				// save active languages
				BackendModel::setSetting('core', 'active_languages', array_unique(array_merge($languages, $this->frm->getField('languages')->getValue())));

				/*
				 * Domains may not contain www, http or https. Therefor we must loop
				 * and create the list of domains.
				 */
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
				BackendModel::setSetting('core', 'site_domains', $siteDomains);

				// assign report
				$this->tpl->assign('formSucces', true);
			}
		}
	}
}

?>