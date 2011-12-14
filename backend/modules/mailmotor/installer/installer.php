<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the mailmotor module
 *
 * @author Dave Lens <dave.lens@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class MailmotorInstaller extends ModuleInstaller
{
	/**
	 * Insert an empty admin dashboard sequence
	 */
	private function insertWidget()
	{
		// build widget
		$statistics = array(
			'column' => 'right',
			'position' => 2,
			'hidden' => false,
			'present' => true
		);

		// insert widget
		$this->insertDashboardWidget('mailmotor', 'statistics', $statistics);
	}

	/**
	 * Install the module
	 */
	public function install()
	{
		// install settings
		$this->installSettings();

		// install the DB
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// install locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		// install the mailmotor module
		$this->installModule();

		// install the pages for the module
		$this->installPages();

		// insert dashboard widget
		$this->insertWidget();

		// set navigation
		$navigationMailmotorId = $this->setNavigation(null, 'Mailmotor', null, null, 5);
		$this->setNavigation($navigationMailmotorId, 'Newsletters', 'mailmotor/index', array(
			'mailmotor/add',
			'mailmotor/edit',
			'mailmotor/edit_mailing_campaign',
			'mailmotor/statistics',
			'mailmotor/statistics_link',
			'mailmotor/statistics_bounces',
			'mailmotor/statistics_campaign',
			'mailmotor/statistics_opens'
		));
		$this->setNavigation($navigationMailmotorId, 'Campaigns', 'mailmotor/campaigns', array(
			'mailmotor/add_campaign',
			'mailmotor/edit_campaign',
			'mailmotor/statistics_campaigns'
		));
		$this->setNavigation($navigationMailmotorId, 'MailmotorGroups', 'mailmotor/groups', array(
			'mailmotor/add_group',
			'mailmotor/edit_group',
			'mailmotor/custom_fields',
			'mailmotor/add_custom_field',
			'mailmotor/import_groups'
		));
		$this->setNavigation($navigationMailmotorId, 'Addresses', 'mailmotor/addresses', array(
			'mailmotor/add_address',
			'mailmotor/edit_address',
			'mailmotor/import_addresses'
		));

		// settings navigation
		$navigationSettingsId = $this->setNavigation(null, 'Settings');
		$navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
		$this->setNavigation($navigationModulesId, 'Mailmotor', 'mailmotor/settings');
	}

	/**
	 * Install the module and it's actions
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
	 */
	private function installPages()
	{
		// add extra's
		$sentMailingsID = $this->insertExtra('mailmotor', 'block', 'SentMailings', null, null, 'N', 3000);
		$subscribeFormID = $this->insertExtra('mailmotor', 'block', 'SubscribeForm', 'subscribe', null, 'N', 3001);
		$unsubscribeFormID = $this->insertExtra('mailmotor', 'block', 'UnsubscribeForm', 'unsubscribe', null, 'N', 3002);
		$widgetSubscribeFormID = $this->insertExtra('mailmotor', 'widget', 'SubscribeForm', 'subscribe', null, 'N', 3003);

		// get search extra id
		$searchId = (int) $this->getDB()->getVar('SELECT id FROM modules_extras WHERE module = ? AND type = ? AND action = ?', array('search', 'widget', 'form'));

		// loop languages
		foreach($this->getLanguages() as $language)
		{
			$parentID = $this->insertPage(
				array('title' => SpoonFilter::ucfirst($this->getLocale('SentMailings', 'core', $language, 'lbl', 'frontend')),
				'type' => 'root',
				'language' => $language),
				null,
				array('extra_id' => $sentMailingsID, 'position' => 'main'),
				array('extra_id' => $searchId, 'position' => 'top')
			);

			$this->insertPage(
				array(
					'parent_id' => $parentID,
					'title' => SpoonFilter::ucfirst($this->getLocale('Subscribe', 'core', $language, 'lbl', 'frontend')
				),
				'language' => $language),
				null,
				array('extra_id' => $subscribeFormID, 'position' => 'main'),
				array('extra_id' => $searchId, 'position' => 'top')
			);

			$this->insertPage(
				array(
					'parent_id' => $parentID,
					'title' => SpoonFilter::ucfirst($this->getLocale('Unsubscribe', 'core', $language, 'lbl', 'frontend')
				),
				'language' => $language),
				null,
				array('extra_id' => $unsubscribeFormID, 'position' => 'main'),
				array('extra_id' => $searchId, 'position' => 'top')
			);
		}
	}

	/**
	 * Install settings
	 */
	private function installSettings()
	{
		// add 'blog' as a module
		$this->addModule('mailmotor');

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
		$this->setSetting('mailmotor', 'price_per_campaign', 0);

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
