<?php

/**
 * BackendMailmotorSettings
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
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
	 * Mailmotor settings
	 *
	 * @var	array
	 */
	private $settings = array();


	/**
	 * Attempts to create a client
	 *
	 * @return	mixed
	 */
	private function createClient()
	{
		// get the account settings
		$url = BackendModel::getModuleSetting('mailmotor', 'cm_url');
		$username = BackendModel::getModuleSetting('mailmotor', 'cm_username');
		$password = BackendModel::getModuleSetting('mailmotor', 'cm_password');

		// get the client gettings from the install
		$companyName = $this->frm->getField('company_name')->getValue();
		$contactEmail = $this->frm->getField('contact_email')->getValue();
		$contactName = $this->frm->getField('contact_name')->getValue();
		$country = $this->frm->getField('countries')->getValue();
		$timezone = $this->frm->getField('timezones')->getValue();

		// create a client
		try
		{
			// init CampaignMonitor object
			$cm = new CampaignMonitor($url, $username, $password, 5);

			// create client
			$clientID = $cm->createClient($companyName, $contactName, $contactEmail, $country, $timezone);

			// store ID in a setting
			if(!empty($clientID)) BackendModel::setModuleSetting('mailmotor', 'cm_client_id', $clientID);
		}
		catch(Exception $e)
		{
			// add an error to the email field
			$this->redirect(BackendModel::createURLForAction('settings') . '&error=campaign-monitor-error&var=' . $e->getMessage());
		}
	}


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get data
		$this->getData();

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
	 * Get all necessary data
	 *
	 * @return	void
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
	 * Loads the settings form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// init settings form
		$this->frm = new BackendForm('settings');

		// add fields for campaignmonitor API
		$this->frm->addText('url', BackendModel::getModuleSetting('mailmotor', 'cm_url'));
		$this->frm->addText('username', BackendModel::getModuleSetting('mailmotor', 'cm_username'));
		$this->frm->addPassword('password', BackendModel::getModuleSetting('mailmotor', 'cm_password'));

		// no client ID set
		if(empty($this->clientID))
		{
			// account was made
			if($this->accountLinked)
			{
				// fetch CM countries
				$countries = BackendMailmotorCMHelper::getCountriesAsPairs();

				// fetch CM timezones
				$timezones = BackendMailmotorCMHelper::getTimezonesAsPairs();

				// add fields for campaignmonitor client ID
				$this->frm->addText('company_name', BackendModel::getModuleSetting('mailmotor', 'cm_client_company_name'));
				$this->frm->addText('contact_name', BackendModel::getModuleSetting('mailmotor', 'cm_client_contact_name'));
				$this->frm->addText('contact_email', BackendModel::getModuleSetting('mailmotor', 'cm_client_contact_email'));
				$this->frm->addDropdown('countries', $countries, BackendModel::getModuleSetting('mailmotor', 'cm_client_country', 'Belgium'));
				$this->frm->addDropdown('timezones', $timezones, BackendModel::getModuleSetting('mailmotor', 'cm_client_timezone', '(GMT+01:00) Brussels, Copenhagen, Madrid, Paris'));
			}
		}

		// client ID was set
		else
		{
			// add field for client ID
			$this->frm->addText('client_id', $this->clientID);
			$this->frm->getField('client_id')->setAttribute('style', 'width: 250px');
		}

		// sender info
		$this->frm->addText('from_name', $this->settings['from_name']);
		$this->frm->addText('from_email', $this->settings['from_email']);

		// reply to address
		$this->frm->addText('reply_to_email', $this->settings['reply_to_email']);

		// add fields for comments
		$this->frm->addCheckbox('plain_text_editable', $this->settings['plain_text_editable']);

		// price per email
		if(BackendAuthentication::getUser()->isGod()) $this->frm->addText('price_per_email', $this->settings['price_per_email']);
	}


	/**
	 * Parse the form
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// call parent
		parent::parse();

		// parse settings in template
		$this->tpl->assign('account', $this->accountLinked);

		// parse client ID
		if($this->accountLinked && !empty($this->settings['cm_client_id'])) $this->tpl->assign('clientId', $this->settings['cm_client_id']);

		// parse god status
		$this->tpl->assign('userIsGod', BackendAuthentication::getUser()->isGod());
	}


	/**
	 * Validates the settings form
	 *
	 * @return	void
	 */
	private function validateForm()
	{
		// set settings URL
		$settingsURL = BackendModel::createURLForAction('settings');

		// form is submitted
		if($this->frm->isSubmitted())
		{
			// validation

			// account is linked but no client ID is set yet
			if($this->accountLinked && empty($this->clientID))
			{
				$this->frm->getField('company_name')->isFilled(BL::err('FieldIsRequired'));
				$this->frm->getField('contact_email')->isFilled(BL::err('FieldIsRequired'));
				$this->frm->getField('contact_name')->isFilled(BL::err('FieldIsRequired'));
				$this->frm->getField('countries')->isFilled(BL::err('FieldIsRequired'));
				$this->frm->getField('timezones')->isFilled(BL::err('FieldIsRequired'));
			}

			// user is god
			elseif(BackendAuthentication::getUser()->isGod())
			{
				$this->frm->getField('price_per_email')->isFilled(BL::err('FieldIsRequired'));
			}

			// form is validated
			if($this->frm->isCorrect())
			{
				// client ID was not yet set
				if($this->accountLinked && empty($this->clientID))
				{
					// attempt to create the client
					$this->createClient();

					// redirect to a custom success message
					$this->redirect($settingsURL . '&report=client-linked&var=' . $this->frm->getField('company_name')->getValue());
				}

				// set sender info
				BackendModel::setModuleSetting('mailmotor', 'from_name', $this->frm->getField('from_name')->getValue());
				BackendModel::setModuleSetting('mailmotor', 'from_email', $this->frm->getField('from_email')->getValue());
				BackendModel::setModuleSetting('mailmotor', 'reply_to_email', $this->frm->getField('reply_to_email')->getValue());
				BackendModel::setModuleSetting('mailmotor', 'plain_text_editable', $this->frm->getField('plain_text_editable')->getValue());

				// set price per email
				if(BackendAuthentication::getUser()->isGod()) BackendModel::setModuleSetting('mailmotor', 'price_per_email', $this->frm->getField('price_per_email')->getValue());

				// redirect to the settings page
				$this->redirect($settingsURL . '&report=saved');
			}
		}
	}
}

?>