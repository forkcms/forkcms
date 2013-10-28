<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the settings-action, it will display a form to set general analytics settings
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@wijs.be>
 */
class BackendAnalyticsSettings extends BackendBaseActionEdit
{
	/**
	 * The account name
	 *
	 * @var	string
	 */
	private $accountName;

	/**
	 * API key needed by the API.
	 *
	 * @var string
	 */
	private $apiKey;

	/**
	 * The forms used on this page
	 *
	 * @var BackendForm
	 */
	private $frmApiKey, $frmLinkProfile, $frmTrackingType;

	/**
	 * All website profiles
	 *
	 * @var	array
	 */
	private $profiles = array();

	/**
	 * The title of the selected profile
	 *
	 * @var	string
	 */
	private $profileTitle;

	/**
	 * The session token
	 *
	 * @var	string
	 */
	private $sessionToken;

	/**
	 * The table id
	 *
	 * @var	int
	 */
	private $tableId;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->loadTrackingTypeForm();
		$this->validateTrackingTypeForm();
		$this->getAnalyticsParameters();
		$this->parse();
		$this->display();
	}

	/**
	 * Gets all the needed parameters to link a google analytics account to fork
	 */
	private function getAnalyticsParameters()
	{
		$remove = SpoonFilter::getGetValue('remove', array('session_token', 'table_id'), null);

		// something has to be removed before proceeding
		if(!empty($remove))
		{
			// the session token has te be removed
			if($remove == 'session_token')
			{
				// remove all parameters from the module settings
				BackendModel::setModuleSetting($this->getModule(), 'session_token', null);
			}

			// remove all profile parameters from the module settings
			BackendModel::setModuleSetting($this->getModule(), 'account_name', null);
			BackendModel::setModuleSetting($this->getModule(), 'table_id', null);
			BackendModel::setModuleSetting($this->getModule(), 'profile_title', null);
			BackendModel::setModuleSetting($this->getModule(), 'web_property_id', null);

			// remove cache files
			BackendAnalyticsModel::removeCacheFiles();

			// clear tables
			BackendAnalyticsModel::clearTables();
		}

		// get session token, account name, the profile's table id, the profile's title
		$this->sessionToken = BackendModel::getModuleSetting($this->getModule(), 'session_token', null);
		$this->accountName = BackendModel::getModuleSetting($this->getModule(), 'account_name', null);
		$this->tableId = BackendModel::getModuleSetting($this->getModule(), 'table_id', null);
		$this->profileTitle = BackendModel::getModuleSetting($this->getModule(), 'profile_title', null);
		$this->apiKey = BackendModel::getModuleSetting($this->getModule(), 'api_key', null);

		// no session token
		if(!isset($this->sessionToken))
		{
			$token = SpoonFilter::getGetValue('token', null, null);

			// a one time token is given in the get parameters
			if(!empty($token) && $token !== 'true')
			{
				// get google analytics instance
				$ga = BackendAnalyticsHelper::getGoogleAnalyticsInstance();

				// get a session token
				$this->sessionToken = $ga->getSessionToken($token);

				// store the session token in the settings
				BackendModel::setModuleSetting($this->getModule(), 'session_token', $this->sessionToken);
			}
		}

		// session id is present but there is no table_id
		if(isset($this->sessionToken) && !isset($this->tableId))
		{
			// get google analytics instance
			$ga = BackendAnalyticsHelper::getGoogleAnalyticsInstance();

			try
			{
				// get all possible profiles in this account
				$this->profiles = $ga->getAnalyticsAccountList($this->sessionToken);
			}
			catch(GoogleAnalyticsException $e)
			{
				// bad request, probably means the API key is wrong
				if($e->getCode() == '400')
				{
					// reset token so we can alter the API key
					BackendModel::setModuleSetting($this->getModule(), 'session_token', null);

					$this->redirect(BackendModel::createURLForAction('settings') . '&error=invalid-api-key');
				}
			}

			// not authorized
			if($this->profiles == 'UNAUTHORIZED')
			{
				// remove invalid session token
				BackendModel::setModuleSetting($this->getModule(), 'session_token', null);

				// redirect to the settings page without parameters
				$this->redirect(BackendModel::createURLForAction('settings'));
			}

			// everything went fine
			elseif(is_array($this->profiles))
			{
				$tableId = SpoonFilter::getGetValue('table_id', null, null);

				// a table id is given in the get parameters
				if(!empty($tableId))
				{
					$profiles = array();

					// set the table ids as keys
					foreach($this->profiles as $profile) $profiles[$profile['tableId']] = $profile;

					// correct table id
					if(isset($profiles[$tableId]))
					{
						// save table id and account title
						$this->tableId = $tableId;
						$this->accountName = $profiles[$this->tableId]['profileName'];
						$this->profileTitle = $profiles[$this->tableId]['title'];
						$webPropertyId = $profiles[$this->tableId]['webPropertyId'];

						// store the table id and account title in the settings
						BackendModel::setModuleSetting($this->getModule(), 'account_name', $this->accountName);
						BackendModel::setModuleSetting($this->getModule(), 'table_id', $this->tableId);
						BackendModel::setModuleSetting($this->getModule(), 'profile_title', $this->profileTitle);
						BackendModel::setModuleSetting($this->getModule(), 'web_property_id', $webPropertyId);
					}
				}
			}
		}
	}

	/**
	 * Load settings form
	 */
	private function loadTrackingTypeForm()
	{
		$this->frmTrackingType = new BackendForm('trackingType');

		$types = array();
		$types[] = array('label' => 'Universal Analytics', 'value' => 'universal_analytics');
		$types[] = array('label' => 'Classic Google Analytics', 'value' => 'classic_analytics');
		$types[] = array('label' => 'Display Advertising (stats.g.doubleclick.net/dc.js)', 'value' => 'display_advertising');

		$this->frmTrackingType->addRadiobutton(
			'type',
			$types,
			BackendModel::getModuleSetting($this->URL->getModule(), 'tracking_type', 'universal_analytics')
		);
	}

	/**
	 * Validates the tracking url form.
	 */
	private function validateTrackingTypeForm()
	{
		// form is submitted
		if($this->frmTrackingType->isSubmitted())
		{
			// form is validated
			if($this->frmTrackingType->isCorrect())
			{
				BackendModel::setModuleSetting(
					$this->getModule(),
					'tracking_type',
					$this->frmTrackingType->getField('type')->getValue()
				);
				BackendModel::triggerEvent($this->getModule(), 'after_saved_tracking_type_settings');
				$this->redirect(BackendModel::createURLForAction('settings') . '&report=saved');
			}
		}
	}

	/**
	 * Parse
	 */
	protected function parse()
	{
		parent::parse();

		if(!isset($this->sessionToken))
		{
			// show the link to the google account authentication form
			$this->tpl->assign('NoSessionToken', true);
			$this->tpl->assign('Wizard', true);

			// build the link to the google account authentication form
			$redirectUrl = SITE_URL . '/' . (strpos($this->URL->getQueryString(), '?') === false ? $this->URL->getQueryString() : substr($this->URL->getQueryString(), 0, strpos($this->URL->getQueryString(), '?')));
			$googleAccountAuthenticationForm = sprintf(BackendAnalyticsModel::GOOGLE_ACCOUNT_AUTHENTICATION_URL, urlencode($redirectUrl), urlencode(BackendAnalyticsModel::GOOGLE_ACCOUNT_AUTHENTICATION_SCOPE));

			// create form
			$this->frmApiKey = new BackendForm('apiKey');
			$this->frmApiKey->addText('key', $this->apiKey);

			if($this->frmApiKey->isSubmitted())
			{
				$this->frmApiKey->getField('key')->isFilled(BL::err('FieldIsRequired'));

				if($this->frmApiKey->isCorrect())
				{
					BackendModel::setModuleSetting($this->getModule(), 'api_key', $this->frmApiKey->getField('key')->getValue());
					$this->redirect($googleAccountAuthenticationForm);
				}
			}

			$this->frmApiKey->parse($this->tpl);
		}

		// session token is present but no table id
		elseif(isset($this->sessionToken) && isset($this->profiles) && !isset($this->tableId))
		{
			// show all possible accounts with their profiles
			$this->tpl->assign('NoTableId', true);
			$this->tpl->assign('Wizard', true);

			$accounts = array();

			// no profiles or not authorized
			if(!empty($this->profiles) && $this->profiles !== 'UNAUTHORIZED')
			{
				$accounts[''][0] = BL::msg('ChooseWebsiteProfile');

				// prepare accounts array
				foreach((array) $this->profiles as $profile)
				{
					$accounts[$profile['accountName']][$profile['tableId']] = $profile['profileName'] . ' (' . $profile['webPropertyId'] . ')';
				}

				// there are accounts
				if(!empty($accounts))
				{
					// sort accounts
					uksort($accounts, array('BackendAnalyticsSettings', 'sortAccounts'));

					// create form
					$this->frmLinkProfile = new BackendForm('linkProfile', BackendModel::createURLForAction(), 'get');
					$this->frmLinkProfile->addDropdown('table_id', $accounts);
					$this->frmLinkProfile->parse($this->tpl);

					if($this->frmLinkProfile->isSubmitted())
					{
						if($this->frmLinkProfile->getField('table_id')->getValue() == '0') $this->tpl->assign('ddmTableIdError', BL::err('FieldIsRequired'));
					}

					// parse accounts
					$this->tpl->assign('accounts', true);
				}
			}
		}

		// everything is fine
		elseif(isset($this->sessionToken) && isset($this->tableId) && isset($this->accountName))
		{
			// show the linked account
			$this->tpl->assign('EverythingIsPresent', true);

			// show the title of the linked account and profile
			$this->tpl->assign('accountName', $this->accountName);
			$this->tpl->assign('profileTitle', $this->profileTitle);
		}

		// Parse tracking url form
		$this->frmTrackingType->parse($this->tpl);
	}

	/**
	 * Helper function to sort accounts
	 *
	 * @param array $account1 First account for comparison.
	 * @param array $account2 Second account for comparison.
	 * @return int
	 */
	public static function sortAccounts($account1, $account2)
	{
		if(strtolower($account1) > strtolower($account2)) return 1;
		if(strtolower($account1) < strtolower($account2)) return -1;
		return 0;
	}
}
