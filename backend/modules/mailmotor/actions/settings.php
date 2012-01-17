<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 *
 * @author Dave Lens <dave.lens@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendMailmotorSettings extends BackendBaseActionEdit
{
	/**
	 * Holds true if the CM account exists
	 *
	 * @var	bool
	 */
	private $accountLinked = false;

	/**
	 * The client ID
	 *
	 * @var	string
	 */
	private $clientID;

	/**
	 * The forms used on this page
	 *
	 * @var	BackendForm
	 */
	private $frmAccount, $frmClient, $frmGeneral;

	/**
	 * Mailmotor settings
	 *
	 * @var	array
	 */
	private $settings = array();

	/**
	 * Attempts to create a client
	 *
	 * @param array $record The client record to create.
	 * @return mixed
	 */
	private function createClient($record)
	{
		// get the account settings
		$url = BackendModel::getModuleSetting($this->getModule(), 'cm_url');
		$username = BackendModel::getModuleSetting($this->getModule(), 'cm_username');
		$password = BackendModel::getModuleSetting($this->getModule(), 'cm_password');

		// create a client
		try
		{
			// fetch complete list of timezones as pairs
			$timezones = BackendMailmotorCMHelper::getTimezonesAsPairs();

			// init CampaignMonitor object
			$cm = new CampaignMonitor($url, $username, $password, 10);

			// create client
			$clientID = $cm->createClient($record['company_name'], $record['contact_name'], $record['contact_email'], $record['country'], $timezones[$record['timezone']]);

			// store ID in a setting
			if(!empty($clientID)) BackendModel::setModuleSetting($this->getModule(), 'cm_client_id', $clientID);
		}
		catch(Exception $e)
		{
			// add an error to the email field
			$this->redirect(BackendModel::createURLForAction('settings') . '&error=campaign-monitor-error&var=' . $e->getMessage() . '#tabSettingsClient');
		}
	}

	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		$this->getData();
		$this->loadAccountForm();
		$this->loadClientForm();
		$this->loadGeneralForm();
		$this->validateAccountForm();
		$this->validateClientForm();
		$this->validateGeneralForm();
		$this->parse();
		$this->display();
	}

	/**
	 * Get all necessary data
	 */
	private function getData()
	{
		// fetch the mailmotor settings
		$settings = BackendModel::getModuleSettings();

		// store mailmotor settings
		$this->settings = $settings['mailmotor'];

		// check if an account was linked already and/or client ID was set
		$this->accountLinked = BackendMailmotorCMHelper::checkAccount();
		$this->clientID = BackendMailmotorCMHelper::getClientID();
	}

	/**
	 * Loads the account settings form
	 */
	private function loadAccountForm()
	{
		// init account settings form
		$this->frmAccount = new BackendForm('settingsAccount');

		// add fields for campaignmonitor API
		$this->frmAccount->addText('url', $this->settings['cm_url']);
		$this->frmAccount->addText('username', $this->settings['cm_username']);
		$this->frmAccount->addPassword('password', $this->settings['cm_password']);

		if($this->accountLinked)
		{
			$this->frmAccount->getField('url')->setAttributes(array('disabled' => 'disabled'));
			$this->frmAccount->getField('username')->setAttributes(array('disabled' => 'disabled'));
			$this->frmAccount->getField('password')->setAttributes(array('disabled' => 'disabled'));
		}
	}

	/**
	 * Loads the client settings form
	 */
	private function loadClientForm()
	{
		// init account settings form
		$this->frmClient = new BackendForm('settingsClient');

		// an account was succesfully made
		if($this->accountLinked)
		{
			// get all clients linked to the active account
			$clients = BackendMailmotorCMHelper::getClientsAsPairs();

			// add field for client ID
			$this->frmClient->addDropdown('client_id', $clients, $this->clientID);
		}

		// account was made
		if($this->accountLinked)
		{
			// fetch CM countries
			$countries = BackendMailmotorCMHelper::getCountriesAsPairs();

			// fetch CM timezones
			$timezones = BackendMailmotorCMHelper::getTimezonesAsPairs();

			// add fields for campaignmonitor client ID
			$this->frmClient->addText('company_name', $this->settings['cm_client_company_name']);
			$this->frmClient->addText('contact_name', $this->settings['cm_client_contact_name']);
			$this->frmClient->addText('contact_email', $this->settings['cm_client_contact_email']);
			$this->frmClient->addDropdown('countries', $countries, $this->settings['cm_client_country']);
			$this->frmClient->addDropdown('timezones', $timezones, $this->settings['cm_client_timezone']);
		}

		// sender info
		$this->frmClient->addText('from_name', $this->settings['from_name']);
		$this->frmClient->addText('from_email', $this->settings['from_email']);

		// reply to address
		$this->frmClient->addText('reply_to_email', $this->settings['reply_to_email']);

		// add fields for comments
		$this->frmClient->addCheckbox('plain_text_editable', $this->settings['plain_text_editable']);
	}

	/**
	 * Loads the general settings form
	 */
	private function loadGeneralForm()
	{
		// init account settings form
		$this->frmGeneral = new BackendForm('settingsGeneral');

		// sender info
		$this->frmGeneral->addText('from_name', $this->settings['from_name']);
		$this->frmGeneral->addText('from_email', $this->settings['from_email']);

		// reply to address
		$this->frmGeneral->addText('reply_to_email', $this->settings['reply_to_email']);

		// add fields for comments
		$this->frmGeneral->addCheckbox('plain_text_editable', $this->settings['plain_text_editable']);

		// user is god
		if(BackendAuthentication::getUser()->isGod())
		{
			// price per email
			$this->frmGeneral->addText('price_per_email', $this->settings['price_per_email']);

			// price per campaign
			$this->frmGeneral->addText('price_per_campaign', $this->settings['price_per_campaign']);
		}
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		parent::parse();

		// parse settings in template
		$this->tpl->assign('account', $this->accountLinked);

		// parse client ID
		if($this->accountLinked && !empty($this->settings['cm_client_id'])) $this->tpl->assign('clientId', $this->settings['cm_client_id']);

		// parse god status
		$this->tpl->assign('userIsGod', BackendAuthentication::getUser()->isGod());

		// add all forms to template
		$this->frmAccount->parse($this->tpl);
		$this->frmClient->parse($this->tpl);
		$this->frmGeneral->parse($this->tpl);
	}

	/**
	 * Updates a client record.
	 *
	 * @param array $record The client record to update.
	 * @return mixed
	 */
	private function updateClient($record)
	{
		// get the account settings
		$url = BackendModel::getModuleSetting($this->getModule(), 'cm_url');
		$username = BackendModel::getModuleSetting($this->getModule(), 'cm_username');
		$password = BackendModel::getModuleSetting($this->getModule(), 'cm_password');

		// try and update the client info
		try
		{
			// fetch complete list of timezones as pairs
			$timezones = BackendMailmotorCMHelper::getTimezonesAsPairs();

			// init CampaignMonitor object
			$cm = new CampaignMonitor($url, $username, $password, 10, $this->clientID);

			// update the client
			$cm->updateClientBasics($record['company_name'], $record['contact_name'], $record['contact_email'], $record['country'], $timezones[$record['timezone']]);
		}
		catch(Exception $e)
		{
			// add an error to the email field
			$this->redirect(BackendModel::createURLForAction('settings') . '&error=campaign-monitor-error&var=' . $e->getMessage() . '#tabSettingsClient');
		}
	}

	/**
	 * Validates the account tab. On successful validation it will unlink an existing campaignmonitor account.
	 */
	private function validateAccountForm()
	{
		// form is submitted
		if($this->frmAccount->isSubmitted())
		{
			// form is validated
			if($this->frmAccount->isCorrect())
			{
				// unlink the account and client ID
				BackendModel::setModuleSetting($this->getModule(), 'cm_account', false);
				BackendModel::setModuleSetting($this->getModule(), 'cm_url', null);
				BackendModel::setModuleSetting($this->getModule(), 'cm_username', null);
				BackendModel::setModuleSetting($this->getModule(), 'cm_password', null);
				BackendModel::setModuleSetting($this->getModule(), 'cm_client_id', null);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_saved_account_settings');

				// redirect to the settings page
				$this->redirect(BackendModel::createURLForAction('settings') . '&report=unlinked#tabSettingsAccount');
			}
		}
	}

	/**
	 * Validates the client tab
	 */
	private function validateClientForm()
	{
		// form is submitted
		if($this->frmClient->isSubmitted())
		{
			$this->frmClient->getField('company_name')->isFilled(BL::err('FieldIsRequired'));
			$this->frmClient->getField('contact_email')->isFilled(BL::err('FieldIsRequired'));
			$this->frmClient->getField('contact_name')->isFilled(BL::err('FieldIsRequired'));
			$this->frmClient->getField('countries')->isFilled(BL::err('FieldIsRequired'));
			$this->frmClient->getField('timezones')->isFilled(BL::err('FieldIsRequired'));

			// form is validated
			if($this->frmClient->isCorrect())
			{
				// get the client settings from the install
				$client = array();
				$client['company_name'] = $this->frmClient->getField('company_name')->getValue();
				$client['contact_name'] = $this->frmClient->getField('contact_name')->getValue();
				$client['contact_email'] = $this->frmClient->getField('contact_email')->getValue();
				$client['country'] = $this->frmClient->getField('countries')->getValue();
				$client['timezone'] = $this->frmClient->getField('timezones')->getValue();

				// client ID was not yet set OR the user wants a new client created
				if($this->frmClient->getField('client_id')->getValue() == '0')
				{
					// attempt to create the client
					$this->createClient($client);

					// store the client info in our database
					BackendModel::setModuleSetting($this->getModule(), 'cm_client_company_name', $client['company_name']);
					BackendModel::setModuleSetting($this->getModule(), 'cm_client_contact_name', $client['contact_name']);
					BackendModel::setModuleSetting($this->getModule(), 'cm_client_contact_email', $client['contact_email']);
					BackendModel::setModuleSetting($this->getModule(), 'cm_client_country', $client['country']);
					BackendModel::setModuleSetting($this->getModule(), 'cm_client_timezone', $client['timezone']);

					// trigger event
					BackendModel::triggerEvent($this->getModule(), 'after_saved_client_settings');

					// redirect to a custom success message
					$this->redirect(BackendModel::createURLForAction('settings') . '&report=client-linked&var=' . $this->frmClient->getField('company_name')->getValue());
				}

				// client ID was already set
				else
				{
					// overwrite the client ID
					$this->clientID = $this->frmClient->getField('client_id')->getValue();

					// update the client record
					$this->updateClient($client);

					// store the client info in our database
					BackendModel::setModuleSetting($this->getModule(), 'cm_client_company_name', $client['company_name']);
					BackendModel::setModuleSetting($this->getModule(), 'cm_client_contact_name', $client['contact_name']);
					BackendModel::setModuleSetting($this->getModule(), 'cm_client_contact_email', $client['contact_email']);
					BackendModel::setModuleSetting($this->getModule(), 'cm_client_country', $client['country']);
					BackendModel::setModuleSetting($this->getModule(), 'cm_client_timezone', $client['timezone']);

					// update the client ID in settings
					BackendModel::setModuleSetting($this->getModule(), 'cm_client_id', $this->clientID);

					// trigger event
					BackendModel::triggerEvent($this->getModule(), 'after_saved_client_settings');

					// redirect to the settings page
					$this->redirect(BackendModel::createURLForAction('settings') . '&report=saved#tabSettingsClient');
				}
			}
		}
	}

	/**
	 * Validates the general tab
	 */
	private function validateGeneralForm()
	{
		// form is submitted
		if($this->frmGeneral->isSubmitted())
		{
			// validate required fields
			$this->frmGeneral->getField('from_name')->isFilled(BL::getError('FieldIsRequired'));
			$this->frmGeneral->getField('from_email')->isEmail(BL::getError('EmailIsInvalid'));
			$this->frmGeneral->getField('reply_to_email')->isEmail(BL::getError('EmailIsInvalid'));

			// user is god
			if(BackendAuthentication::getUser()->isGod())
			{
				if($this->frmGeneral->getField('price_per_email')->isFilled(BL::err('FieldIsRequired')))
				{
					$this->frmGeneral->getField('price_per_email')->isFloat(BL::err('InvalidPrice'));
				}

				if($this->frmGeneral->getField('price_per_campaign')->isFilled(BL::err('FieldIsRequired')))
				{
					$this->frmGeneral->getField('price_per_campaign')->isFloat(BL::err('InvalidPrice'));
				}
			}

			// form is validated
			if($this->frmGeneral->isCorrect())
			{
				// set sender info
				BackendModel::setModuleSetting($this->getModule(), 'from_name', $this->frmGeneral->getField('from_name')->getValue());
				BackendModel::setModuleSetting($this->getModule(), 'from_email', $this->frmGeneral->getField('from_email')->getValue());
				BackendModel::setModuleSetting($this->getModule(), 'reply_to_email', $this->frmGeneral->getField('reply_to_email')->getValue());
				BackendModel::setModuleSetting($this->getModule(), 'plain_text_editable', $this->frmGeneral->getField('plain_text_editable')->getValue());

				// user is god?
				if(BackendAuthentication::getUser()->isGod())
				{
					// set price per email
					BackendModel::setModuleSetting(
						$this->getModule(),
						'price_per_email',
						$this->frmGeneral->getField('price_per_email')->getValue()
					);

					// set price per campaign
					BackendModel::setModuleSetting(
						$this->getModule(),
						'price_per_campaign',
						$this->frmGeneral->getField('price_per_campaign')->getValue()
					);
				}

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_saved_general_settings');

				// redirect to the settings page
				$this->redirect(BackendModel::createURLForAction('settings') . '&report=saved#tabGeneral');
			}
		}
	}
}
