<?php

/**
 * Installer for the mailmotor module
 *
 * @package		installer
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class MailmotorInstall extends ModuleInstaller
{
	/**
	 * Install the module
	 *
	 * @return	void
	 */
	protected function execute()
	{
		// install settings
		$this->installSettings();

		// install the DB
		$this->installDatabase();

		// install locale
		$this->installLocale();

		// install the mailmotor module
		$this->installModule();

		// install the pages for the module
		$this->installPages();
	}


	/**
	 * Install the database
	 *
	 * @return	void
	 */
	private function installDatabase()
	{
		// load install.sql and labels.sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');
	}


	/**
	 * Install locale
	 *
	 * @return	void
	 */
	private function installLocale()
	{
		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');
	}


	/**
	 * Iinstall the module and it's actions
	 *
	 * @return	void
	 */
	private function installModule()
	{
		// module rights
		$this->setModuleRights(1, 'mailmotor');

		// action rights
		$this->setActionRights(1, 'mailmotor', 'add');
		$this->setActionRights(1, 'mailmotor', 'add_address');
		$this->setActionRights(1, 'mailmotor', 'add_campaign');
		$this->setActionRights(1, 'mailmotor', 'add_custom_field');
		$this->setActionRights(1, 'mailmotor', 'add_group');
		$this->setActionRights(1, 'mailmotor', 'addresses');
		$this->setActionRights(1, 'mailmotor', 'campaigns');
		$this->setActionRights(1, 'mailmotor', 'copy');
		$this->setActionRights(1, 'mailmotor', 'custom_fields');
		$this->setActionRights(1, 'mailmotor', 'delete_bounces');
		$this->setActionRights(1, 'mailmotor', 'delete_custom_field');
		$this->setActionRights(1, 'mailmotor', 'edit');
		$this->setActionRights(1, 'mailmotor', 'edit_address');
		$this->setActionRights(1, 'mailmotor', 'edit_campaign');
		$this->setActionRights(1, 'mailmotor', 'edit_custom_field');
		$this->setActionRights(1, 'mailmotor', 'edit_group');
		$this->setActionRights(1, 'mailmotor', 'edit_mailing_campaign');
		$this->setActionRights(1, 'mailmotor', 'edit_mailing_iframe');
		$this->setActionRights(1, 'mailmotor', 'export_addresses');
		$this->setActionRights(1, 'mailmotor', 'export_statistics');
		$this->setActionRights(1, 'mailmotor', 'export_statistics_campaign');
		$this->setActionRights(1, 'mailmotor', 'groups');
		$this->setActionRights(1, 'mailmotor', 'import_addresses');
		$this->setActionRights(1, 'mailmotor', 'import_groups');
		$this->setActionRights(1, 'mailmotor', 'index');
		$this->setActionRights(1, 'mailmotor', 'link_account');
		$this->setActionRights(1, 'mailmotor', 'load_client_info');
		$this->setActionRights(1, 'mailmotor', 'mass_address_action');
		$this->setActionRights(1, 'mailmotor', 'mass_campaign_action');
		$this->setActionRights(1, 'mailmotor', 'mass_custom_field_action');
		$this->setActionRights(1, 'mailmotor', 'mass_group_action');
		$this->setActionRights(1, 'mailmotor', 'mass_mailing_action');
		$this->setActionRights(1, 'mailmotor', 'save_content');
		$this->setActionRights(1, 'mailmotor', 'save_send_date');
		$this->setActionRights(1, 'mailmotor', 'send_mailing');
		$this->setActionRights(1, 'mailmotor', 'settings');
		$this->setActionRights(1, 'mailmotor', 'statistics');
		$this->setActionRights(1, 'mailmotor', 'statistics_bounces');
		$this->setActionRights(1, 'mailmotor', 'statistics_campaign');
		$this->setActionRights(1, 'mailmotor', 'statistics_link');
	}


	/**
	 * Install the pages for this module
	 *
	 * @return	void
	 */
	private function installPages()
	{
		// add extra's
		$sentMailingsID = $this->insertExtra('mailmotor', 'block', 'SentMailings', null, null, 'N', 3000);
		$subscribeFormID = $this->insertExtra('mailmotor', 'block', 'SubscribeForm', 'subscribe', null, 'N', 3001);
		$unsubscribeFormID = $this->insertExtra('mailmotor', 'block', 'UnsubscribeForm', 'unsubscribe', null, 'N', 3002);
		$widgetSubscribeFormID = $this->insertExtra('mailmotor', 'widget', 'SubscribeForm', 'subscribe', null, 'N', 3003);

		// get the default templates
		$templateID = (int) $this->getDB()->getVar('SELECT id FROM pages_templates WHERE label = ?', array('Triton - Default'));

		// loop languages
		foreach($this->getLanguages() as $language)
		{
			$parentID = (int) $this->insertPage(array('title' => 'Sent mailings',
														'template_id' => $templateID,
														'type' => 'root',
														'language' => $language),
												null,
												array('extra_id' => $sentMailingsID));

			$this->insertPage(array('parent_id' => $parentID,
									'template_id' => $templateID,
									'title' => 'Subscribe',
									'language' => $language),
								null,
								array('extra_id' => $subscribeFormID));

			$this->insertPage(array('parent_id' => $parentID,
									'template_id' => $templateID,
									'title' => 'Unsubscribe',
									'language' => $language),
								null,
								array('extra_id' => $unsubscribeFormID));
		}
	}


	/**
	 * Install settings
	 *
	 * @return	void
	 */
	private function installSettings()
	{
		// add 'blog' as a module
		$this->addModule('mailmotor', 'The module to manage and send mailings.');

		// get email from the session
		$email = SpoonSession::exists('email') ? SpoonSession::get('email') : null;

		// get from/replyTo core settings
		$from = $this->getSetting('core', 'mailer_from');
		$replyTo = $this->getSetting('core', 'mailer_reply_to');

		// general settings
		$this->setSetting('mailmotor', 'from_email', $from['email']);
		$this->setSetting('mailmotor', 'from_name', $from['name']);
		$this->setSetting('mailmotor', 'plain_text_editable', true);
		$this->setSetting('mailmotor', 'reply_to_email', $replyTo['email']);
		$this->setSetting('mailmotor', 'price_per_email', 0);

		// pre-load these CM settings - these are used to obtain a client ID after the CampaignMonitor account is linked.
		$this->setSetting('mailmotor', 'cm_url', '');
		$this->setSetting('mailmotor', 'cm_username', '');
		$this->setSetting('mailmotor', 'cm_password', '');
		$this->setSetting('mailmotor', 'cm_client_company_name', $from['name']);
		$this->setSetting('mailmotor', 'cm_client_contact_email', $from['email']);
		$this->setSetting('mailmotor', 'cm_client_contact_name', $from['name']);
		$this->setSetting('mailmotor', 'cm_client_country', 'Belgium');
		$this->setSetting('mailmotor', 'cm_client_timezone', '');

		// by default no account is linked yet
		$this->setSetting('mailmotor', 'cm_account', false);
	}
}

?>