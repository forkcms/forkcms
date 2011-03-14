<?php

/**
 * MailmotorInstall
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
		$this->installLocaleNL();
		$this->installLocaleEN();
	}


	/**
	 * Install english locale
	 *
	 * @return	void
	 */
	private function installLocaleEN()
	{
		$this->insertLocale('en', 'frontend', 'core', 'act', 'Preview', 'preview');
		$this->insertLocale('en', 'frontend', 'core', 'act', 'Subscribe', 'subscribe');
		$this->insertLocale('en', 'frontend', 'core', 'act', 'Unsubscribe', 'unsubscribe');
		$this->insertLocale('en', 'frontend', 'core', 'err', 'AlreadySubscribed', 'This e-mail address is already subscribed to the newsletter.');
		$this->insertLocale('en', 'frontend', 'core', 'err', 'AlreadyUnsubscribed', 'This e-mail address is already unsubscribed from the newsletter');
		$this->insertLocale('en', 'frontend', 'core', 'err', 'EmailNotInDatabase', 'This e-mail address does not exist in the database.');
		$this->insertLocale('en', 'frontend', 'core', 'err', 'SubscribeFailed', 'Subscribing failed, try again by refreshing the page.');
		$this->insertLocale('en', 'frontend', 'core', 'err', 'UnsubscribeFailed', 'Unsubscribing failed, please try again later.');
		$this->insertLocale('en', 'frontend', 'core', 'lbl', 'Sent', 'sent');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'NoSentMailings', 'So far, no mailings have been sent.');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'SubscribeSuccess', 'You have successfully subscribed to the newsletter.');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'UnsubscribeSuccess', 'You have successfully unsubscribed from the newsletter.');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'AccountSettings', 'account settings');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Addresses', 'e-mail addresses');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'AllAddresses', 'all e-mail addresses');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Bounces', 'bounces');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'BounceType', 'bounce type');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Campaigns', 'campaigns');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'ClientSettings', 'client settings');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Copy', 'copy');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Country', 'country');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Created', 'created');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'CreatedOn', 'created on');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'EN', 'english');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Export', 'export');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'For', 'for');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'FR', 'french');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'MailmotorGroups', 'groups');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Import', 'import');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'ImportNoun', 'import');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'In', 'in');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Mailmotor', 'mailmotor');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'MailmotorClicks', 'clicks');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'MailmotorLatestMailing', 'last sent mailing');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'MailmotorOpened', 'opened');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'MailmotorSendDate', 'send date');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'MailmotorSent', 'sent');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'MailmotorStatistics', 'statistics');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'MailmotorSubscriptions', 'subscriptions');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'MailmotorUnsubscriptions', 'unsubscriptions');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Newsletters', 'mailings');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'NL', 'dutch');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Person', 'person');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Persons', 'people');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Price', 'price');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'QuantityNo', 'no');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'SentMailings', 'sent mailings');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'SentOn', 'sent on');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Source', 'source');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Subscriptions', 'subscriptions');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'SubscribeForm', 'subscribe form');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Timezone', 'timezone');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'ToStep', 'to step');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Unsubscriptions', 'unsubscriptions');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'UnsubscribeForm', 'unsubscribe form');
		$this->insertLocale('en', 'backend', 'core', 'msg', 'AllAddresses', 'All addresses sorted by subscription date.');
		$this->insertLocale('en', 'backend', 'core', 'msg', 'NoSentMailings', 'No mailings have been sent yet.');
		$this->insertLocale('en', 'backend', 'core', 'msg', 'NoSubscriptions', 'No one subscribed to the mailinglist yet.');
		$this->insertLocale('en', 'backend', 'core', 'msg', 'NoUnsubscriptions', 'No one unsubscribed from from the mailinglist yet.');

		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'AnalysisNoCMAccount', 'There is no link with a CampaignMonitor account yet. <a href="%1$s">Configure</a>');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'AnalysisNoCMClientID', 'There is no client linked to the CampaignMonitor account yet. <a href="%1$s">Configure</a>');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'AddressDoesNotExist', 'The given e-mail address does not exist.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'AlreadySubscribed', 'This e-mail address is already subscribed to the mailinglist.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'AddMailingNoGroups', 'There are no groups to put subscribers in yet. Create a group first.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'CampaignDoesNotExist', 'The given campaign does not exist.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'CampaignExists', 'This campaign name already exists.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'CampaignNotEdited', 'The campaign wasn\'t edited.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'CampaignMonitorError', 'CampaignMonitor error: %1$s');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'ChooseAtLeastOneGroup', 'You need to choose at least one group.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'ChooseTemplateLanguage', 'Choose the language of the template to use.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'ClassDoesNotExist', 'The CampaignMonitor wrapper class is not found. Please locate and place it in /library/external');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'CmTimeout', 'Could not make a connection with CampaignMonitor,m try again.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'CompleteStep2', 'Complete step 2 first');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'CompleteStep3', 'Complete step 3 first');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'CSVIsRequired', 'Choose a .csv file to upload.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'CouldNotConnect', 'Could not connect to CampaignMonitor, please try again.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'CustomFieldExists', 'This field name is already in use.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'DuplicateCampaignName', 'The name of this mailing already exists in the archives of CampaignMonitor. Change the name before sending.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'GroupAlreadyExists', 'This group already exists, choose another name.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'GroupsNoRecipients', 'The selected group(s) don\'t contain any addresses.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'HTMLContentURLRequired', 'CampaignMonitor could not find an URL to the HTML content. (The URL needs to be accessible)');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'ImportedAddresses', '%1$s addresses are imported in %2$s group(s), %3$s were not imported.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'InvalidAccountCredentials', 'The CampaignMonitor account credentials are invalid.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'InvalidCSV', 'The CSV file is empty, or not valid.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'LinkDoesNotExist', 'This link doesn\'t exists.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'LinkDoesNotExists', 'This link doesn\'t exists.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'MailingAlreadyExists', 'This mailing already exists, choose another name.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'MailingAlreadySent', 'The given mailing has already been sent!');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'MailingDoesNotExist', 'The given mailing does not exist.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'NoActionSelected', 'No action selected.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'NoBounces', 'There are no bounces for this mailing.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'NoCMAccount', 'There is no link with a CampaignMonitor account yet.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'NoCMClientID', 'There is no client linked to the CampaignMonitor account yet.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'NoCMAccountCredentials', 'Please enter your CampaignMonitor credentials.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'NoGroups', 'Select a group.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'NoSubject', 'Enter a subject for this mailing.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'NoSubscribers', 'None of your groups have subscribers yet! You can import your current subscriber list by uploading a .csv-file.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'NoTemplates', 'No templates are available for this language, select another one.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'NoPreviewSent', 'The preview-mail to %1$s was not sent.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'NoPricePerEmail', 'No price per sent mail has been set yet.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'NoStatisticsLoaded', 'There are no statistics available yet for mailing &ldquo;%1$s&rdquo;.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'PaymentDetailsRequired', 'The payment details of the active user (%1$s) are not yet set in the CampaignMonitor backend.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'TemplateIsRequired', 'Choose a template first before proceeding to the next step.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'err', 'TemplateDoesNotExist', 'This template does not exist, you scallywag!');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'AddCampaign', 'add campaign');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'AddCustomField', 'add custom field');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'AddEmail', 'add e-mail address');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'AddGroup', 'add group');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'AddNewMailing', 'create new mailing');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'AddMailing', 'add mailing');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'AddressList', 'mailinglist');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'BounceRate', 'bounce-rate');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'Campaign', 'campaign');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'CampaignName', 'campaign');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'ChooseTemplate', 'choose a template');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'CompanyName', 'company name');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'ContactName', 'contact');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'ClickRate', 'click-rate');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'Clicks', 'clicks');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'Client', 'client');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'ClientID', 'client ID');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'CreateNewClient', 'create a new client');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'CustomFields', 'custom fields');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'EditCampaign', 'edit campaign');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'EditMailingCampaign', 'edit campaign');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'EditCustomField', 'edit custom field');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'EditEmail', 'edit e-mail address');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'EditGroup', 'edit group');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'EmailAddress', 'e-mail address');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'EmailAddresses', 'e-mail addresses');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'ExampleFile', 'an example file');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'ExportAddresses', 'export addresses');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'ExportStatistics', 'export statistics');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'Group', 'group');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'Groups', 'groups');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'ImportAddresses', 'import addresses');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'IpAddress', 'IP address');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'Manual', 'manual');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'MailingsWithoutCampaign', 'mailings without campaign');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'NoCampaign', 'no campaign');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'OpenedMailings', 'opened mailings');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'PlainTextVersion', 'plain text version');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'PricePerSentMailing', 'price per sent mailing');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'QueuedMailings', 'queued mailings');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'Recipients', 'groups');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'ReplyTo', 'reply-to address');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'Reset', 'reset');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'SendDate', 'send date');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'Sender', 'sender');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'SendMailing', 'send mailing');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'SendOn', 'send on');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'SendPreview', 'send preview');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'Sent', 'sent');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'SentMailings', 'sent mailings');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'SettingsAccount', 'account settings');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'SettingsClient', 'client settings');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'Subject', 'subject');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'TemplateDefault', 'default template');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'TemplateEmpty', 'empty template');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'TemplateFork', 'Fork CMS template');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'TemplateLanguage', 'template language');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'TotalSentMailings', 'sent mailings');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'UnopenedMailings', 'unopened mailings');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'UnsentMailings', 'concepts');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'Who', 'who?');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'WillBeSentOn', 'will be sent on');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'WizardInformation', 'configuration');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'WizardTemplate', 'template');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'WizardContent', 'content');
		$this->insertLocale('en', 'backend', 'mailmotor', 'lbl', 'WizardSend', 'send');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'AccountLinked', 'Your CampaignMonitor account is now linked to Fork.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'AddMultipleEmails', 'add multiple email addresses by using a comma (,) as a delimiter');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'BackToCampaigns', 'Back to campaign overview');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'BackToMailings', 'Back to mailings overview');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'BackToStatistics', 'Back to statistics for &ldquo;%1$s&rdquo;');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'CampaignAdded', 'The campaign has been added successfully.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'CampaignEdited', 'The campaign has been edited successfully.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'CampaignMailings', 'mailings in this campaign');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'ClickedLinks', 'ontvangers hebben op links geklikt.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'ClicksAmount', 'number of clicks');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'ClicksBreakdown', '%1$s of opened, %2$s of sent');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'ClicksOpened', 'times opened');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'ClientLinked', 'The client &ldquo;%1$s&rdquo; is now linked to Fork.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'CreateGroupByAddresses', 'Create a new group with the addresses below.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'DefaultGroup', 'Selecting a language here will mark this as the default group for that language. This means visitors who subscribe to your mailinglist in this language version of your website will end up in this group. Only one default group can be set for each language.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'DeleteAddresses', 'The address(es) have been deleted successfully.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'DeleteBounces', 'Delete all hard bounces');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'DeletedBounces', 'The hard bounces for this mailing have been deleted.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'DeletedCustomFields', 'The custom fields for group &ldquo;%1$s&rdquo; have been deleted successfully.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'DeleteCampaigns', 'The campaigns have been deleted successfully.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'DeleteGroups', 'The groups have been deleted successfully.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'DeleteMailings', 'The mailings have been deleted successfully.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'EditMailingCampaign', 'Edit campaign');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'ExportFailed', 'Export failed.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'GroupAdded', 'The group has been added successfully.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'GroupsImported', '%1$s group(s) and %2$s email-addresses were imported from CampaignMonitor. Don\'t forget to select a default group for each language!');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'GroupsNumberOfRecipients', 'This group contains %1$s address(es).');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'HelpCMURL', 'The URL of the CampaignMonitor API for your account. (ex. *.createsend.com)');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'HelpCustomFields', 'Custom fields are variables that hold a unique value for each e-mail address in a group. This way you can send personalized mailings.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'ImportedAddresses', '%1$s addresses are imported in %2$s group(s).');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'ImportFailedDownloadCSV', 'Download a CSV with the failed addresses <a href="%1$s">here</a>.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'ImportGroupsTitle', 'Import groups from CampaignMonitor');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'ImportGroups', 'Fork has found the following groups in Campaignmonitor');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'ImportRecentlyFailed', 'Not all addresses were imported.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'LinkCMAccount', 'Link CampaignMonitor account');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'MailingAdded', 'The mailing has been added successfully.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'MailingConfirmSend', 'Are you sure you want to send the mailing?');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'MailingConfirmTitle', 'Send this mailing.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'MailingCopied', 'The mailing &ldquo;%1$s&rdquo; has been copied successfully.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'MailingCSVBounces', 'bounces');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'MailingCSVBouncesPercentage', '% bounces');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'MailingCSVOpens', 'opened mailings');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'MailingCSVRecipients', 'total sent mailings');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'MailingCSVUniqueOpens', 'uniquely opened mailings');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'MailingCSVUniqueOpensPercentage', '% opened mailings');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'MailingCSVUnopens', 'opened mailings');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'MailingCSVUnopensPercentage', '% unopened mailings');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'MailingCSVUnsubscribes', 'unsubscribes');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'MailingEdited', 'The mailing has been edited successfully.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'MailingLinks', 'links in this mailing');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'MailingSent', 'The mailing has been sent successfully.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'NameInternalUseOnly', 'This name is for internal use only.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'NoClientID', 'No CampaignMonitor client has been linked to Fork yet. Choose an existing client from the dropdown, or enter the following fields to link a client.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'NoDefault', 'This is not a default group.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'NoDefaultsSet', 'A default group for a language is the group where visitors end up in when they subscribe through the subscribe-forms on your Fork website. Edit a group and select a language to set it as the default group for that language.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'NoDefaultsSetTitle', 'Not all default groups were set.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'NoResultsForFilter', 'No results for search term &ldquo;%1$s&rdquo;.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'NoUnsentMailings', 'There are no concepts available.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'PeopleGroups', 'These people come from the following groups:');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'PlainTextEditable', 'Make the textual version of each individual mail adjustable.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'PreviewSent', 'The preview-mail has been sent to %1$s.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'Reason', 'reason');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'RecipientStatisticsCampaign', 'You are about to send the mailing "%1$s" from campaign "%2$s" to %3$s %4$s.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'RecipientStatisticsNoCampaign', 'You are about to send the mailing "%1$s" to %2$s %3$s.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'ResetCampaigns', 'campaigns');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'ResetDone', 'Don\t forget to remove the client in the CampaignMonitor backend if it\'s no longer to be used.<br />This is done because there is a limit to the number of clients you can add through the API.<br /><br />Use the following client ID if you don\'t wish to remove your client: <strong>%1$s</strong>');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'ResetGroups', 'groups (with addresses)');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'ResetLabels', 'labels');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'ResetMailings', 'mailings');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'ResetSettings', 'module settings');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'SendOn', 'The mailing will be sent on %1$s at %2$s.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'TemplateLanguage', 'language of the template');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'UnlinkCMAccount', 'Unlink CampaignMonitor account');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'Unlinked', 'The CampaignMonitor account has been unlinked.');
		$this->insertLocale('en', 'backend', 'mailmotor', 'msg', 'ViewMailings', 'Go to your mailings overview');

		$this->insertLocale('en', 'backend', 'pages', 'lbl', 'SentMailings', 'sent mailings');
		$this->insertLocale('en', 'backend', 'pages', 'lbl', 'SubscribeForm', 'subscribe form');
		$this->insertLocale('en', 'backend', 'pages', 'lbl', 'UnsubscribeForm', 'unsubscribe form');
	}


	/**
	 * Install dutch locale
	 *
	 * @return	void
	 */
	private function installLocaleNL()
	{
		$this->insertLocale('nl', 'frontend', 'core', 'act', 'Preview', 'preview');
		$this->insertLocale('nl', 'frontend', 'core', 'act', 'Subscribe', 'inschrijven');
		$this->insertLocale('nl', 'frontend', 'core', 'act', 'Unsubscribe', 'uitschrijven');
		$this->insertLocale('nl', 'frontend', 'core', 'err', 'AlreadySubscribed', 'Dit e-mailadres is reeds ingeschreven op de nieuwsbrief.');
		$this->insertLocale('nl', 'frontend', 'core', 'err', 'AlreadyUnsubscribed', 'Dit e-mailadres is reeds uitschreven uit de nieuwsbrief.');
		$this->insertLocale('nl', 'frontend', 'core', 'err', 'EmailNotInDatabase', 'Dit e-mailadres bestaat niet in onze database.');
		$this->insertLocale('nl', 'frontend', 'core', 'err', 'SubscribeFailed', 'Het inschrijven is niet gelukt, probeer het later opnieuw.');
		$this->insertLocale('nl', 'frontend', 'core', 'err', 'UnsubscribeFailed', 'Het uitschrijven is niet gelukt, probeer het later opnieuw.');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'Sent', 'verzonden');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'NoSentMailings', 'Er zijn tot dusver nog geen nieuwsbrieven verzonden.');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'SubscribeSuccess', 'Je bent met success ingeschreven op de nieuwsbrief.');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'UnsubscribeSuccess', 'Je bent met success uitgeschreven uit de nieuwsbrief.');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'AccountSettings', 'account instellingen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Addresses', 'e-mailadressen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'AllAddresses', 'alle e-mailadressen,');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Bounces', 'bounces');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'BounceType', 'bounce type');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Campaigns', 'campagnes');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'ClientSettings', 'client instellingen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Copy', 'kopieer');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Country', 'land');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Created', 'aangemaakt');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'CreatedOn', 'aangemaakt op');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'EN', 'engels');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Export', 'exporteren');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'For', 'voor');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'FR', 'frans');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'MailmotorGroups', 'doelgroepen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Import', 'importeren');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'ImportNoun', 'import');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'In', 'in');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Mailmotor', 'mailmotor');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'MailmotorClicks', 'kliks');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'MailmotorLatestMailing', 'laatst verzonden mailing');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'MailmotorOpened', 'geopend');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'MailmotorSendDate', 'verzenddatum');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'MailmotorSent', 'verzonden');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'MailmotorStatistics', 'statistieken');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'MailmotorSubscriptions', 'inschrijvingen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'MailmotorUnsubscriptions', 'uitschrijvingen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Newsletters', 'mailings');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'NL', 'nederlands');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Person', 'persoon');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Persons', 'personen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Price', 'prijs');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'QuantityNo', 'geen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'SentMailings', 'verzonden mailings');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'SentOn', 'verzonden op');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Source', 'bron');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Subscriptions', 'inschrijvingen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'SubscribeForm', 'inschrijfformulier');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Timezone', 'tijdzone');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'ToStep', 'naar stap');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Unsubscriptions', 'uitschrijvingen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'UnsubscribeForm', 'uitschrijfformulier');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'AllAddresses', 'alle adressen gesorteerd op inschrijfdatum');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'NoSentMailings', 'Er zijn nog geen mailings verzonden.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'NoSubscriptions', 'Er is nog niemand ingeschreven.');
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'NoUnsubscriptions', 'Er is nog niemand uitgeschreven.');

		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'AnalysisNoCMAccount', 'Er is nog geen CampaignMonitor account gekoppeld. <a href="%1$s">Configureer</a>');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'AnalysisNoCMClientID', 'Er is nog geen client aan de CampaignMonitor account gekoppeld. <a href="%1$s">Configureer</a>');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'AddressDoesNotExist', 'Het opgegeven e-mailadres bestaat niet.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'AlreadySubscribed', 'Dit e-mailadres is reeds ingeschreven op de mailing.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'AddMailingNoGroups', 'Er zijn nog geen doelgroepen aangemaakt om subscribers in te plaatsen. Maak eerst een doelgroep aan.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'CampaignDoesNotExist', 'De opgegeven campagne bestaat niet.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'CampaignExists', 'Deze campagnenaam is reeds in gebruik.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'CampaignNotEdited', 'De campagne werd niet gewijzigd.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'CampaignMonitorError', 'CampaignMonitor fout: %1$s');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'ChooseAtLeastOneGroup', 'Je dient minstens 1 doelgroep te kiezen.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'ChooseTemplateLanguage', 'Kies eerst de taal van het te gebruiken template.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'ClassDoesNotExist', 'De CampaignMonitor wrapper class is niet gevonden. Localiseer en plaats hem in /library/external');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'CmTimeout', 'Kon geen connectie leggen naar CampaignMonitor, probeer het opnieuw.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'CompleteStep2', 'Vervolledig eerst stap 2');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'CompleteStep3', 'Vervolledig eerst stap 3');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'CouldNotConnect', 'Fork kan momenteel niet verbinden met CampaignMonitor, probeer het opnieuw door de pagina opnieuw te laden.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'CSVIsRequired', 'Kies een .csv bestand om te uploaden.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'CustomFieldExists', 'Deze veldnaam is reeds in gebruik.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'DuplicateCampaignName', 'De naam van deze mailing bestaat reeds in het archief van CampaignMonitor. Verander de naam alvorens te verzenden.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'GroupAlreadyExists', 'Deze groep bestaat reeds, kies een andere naam.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'GroupsNoRecipients', 'De geselecteerde doelgroep(en) bevat(ten) geen e-mailadressen.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'HTMLContentURLRequired', 'CampaignMonitor kon geen URL naar de HTML content vinden. (Als je lokaal werkt is dit normaal, probeer eens op SVN.)');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'ImportedAddresses', '%1$s adressen zijn ge&iuml;mporteerd in %2$s doelgroep(en), %3$s zijn niet ge&iuml;mporteerd.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'InvalidAccountCredentials', 'De CampaignMonitor accountgegevens zijn niet geldig.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'InvalidCSV', 'Het CSV bestand is leeg, of ongeldig.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'LinkDoesNotExist', 'Deze link bestaat niet.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'LinkDoesNotExists', 'Deze link bestaat niet.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'MailingAlreadyExists', 'Deze mailing bestaat al, kies een andere naam.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'MailingAlreadySent', 'De opgegeven mailing is reeds verzonden!');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'MailingDoesNotExist', 'De opgegeven mailing bestaat niet.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'NoActionSelected', 'Geen actie geselecteerd.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'NoBounces', 'Er zijn geen bounces voor deze mailing.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'NoCMAccount', 'Er is nog geen CampaignMonitor account gekoppeld.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'NoCMClientID', 'Er is nog geen client aan de CampaignMonitor account gekoppeld.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'NoCMAccountCredentials', 'Geef je CampaignMonitor gegevens in.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'NoGroups', 'Gelieve een groep te selecteren.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'NoSubject', 'Geef een onderwerp op voor deze mailing.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'NoSubscribers', 'Je hebt nog geen e-mailadressen in je groepen! Je kan er importeren met behulp van een .csv-bestand.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'NoTemplates', 'Voor die taal zijn geen templates beschikbaar, kies een andere taal.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'NoPreviewSent', 'De preview-mail naar %1$s werd niet verzonden.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'NoPricePerEmail', 'Er is nog geen prijs per verzonden e-mail ingesteld.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'NoStatisticsLoaded', 'Er zijn nog geen statistieken beschikbaar voor mailing &ldquo;%1$s&rdquo;.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'PaymentDetailsRequired', 'De betalingsgegevens van de actieve gebruiker (%1$s) zijn nog niet ingegeven in CampaignMonitor.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'TemplateIsRequired', 'Kies eerst een template alvorens door te gaan naar de volgende stap.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'err', 'TemplateDoesNotExist', 'Deze template bestaat niet, jij prutser!');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'AddCampaign', 'campagne toevoegen');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'AddCustomField', 'variabel veld toevoegen');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'AddEmail', 'e-mailadres toevoegen');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'AddGroup', 'doelgroep toevoegen');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'AddNewMailing', 'nieuwe mailing maken');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'AddMailing', 'mailing toevoegen');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'AddressList', 'e-mailadressenlijst');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'BounceRate', 'bounce-rate');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'Campaign', 'campagne');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'CampaignName', 'campagne');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'ChooseTemplate', 'kies een template');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'CompanyName', 'bedrijfsnaam');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'ContactName', 'contactpersoon');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'ClickRate', 'click-rate');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'Clicks', 'kliks');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'Client', 'client');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'ClientID', 'client ID');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'CreateNewClient', 'maak een nieuwe client aan');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'CustomFields', 'variabele velden');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'EditCampaign', 'campagne bewerken');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'EditMailingCampaign', 'wijzig campagne');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'EditCustomField', 'variabel veld bewerken');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'EditEmail', 'e-mailadres bewerken');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'EditGroup', 'doelgroep bewerken');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'EmailAddress', 'e-mailadres');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'EmailAddresses', 'e-mailadressen');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'ExampleFile', 'een voorbeeldbestand');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'ExportAddresses', 'e-mailadressen exporteren');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'ExportStatistics', 'statistieken exporteren');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'Group', 'doelgroep');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'Groups', 'doelgroepen');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'ImportAddresses', 'e-mailadressen importeren');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'IpAddress', 'IP adres');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'Manual', 'manueel');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'MailingsWithoutCampaign', 'mailings zonder campagne');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'NoCampaign', 'geen campagne');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'OpenedMailings', 'geopende mailings');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'PlainTextVersion', 'plain text versie');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'PricePerSentMailing', 'prijs per verzonden e-mail');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'QueuedMailings', 'mailings in de wachtrij');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'Recipients', 'doelgroepen');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'ReplyTo', 'reply-to adres');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'Reset', 'resetten');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'SendDate', 'verzenddatum');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'Sender', 'afzender');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'SendMailing', 'verzend mailing');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'SendOn', 'verzenden op');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'SendPreview', 'verzend preview');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'Sent', 'verzonden');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'SentMailings', 'verzonden mailings');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'SettingsAccount', 'account instellingen');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'SettingsClient', 'client instellingen');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'Subject', 'onderwerp');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'TemplateDefault', 'default template');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'TemplateEmpty', 'leeg template');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'TemplateFork', 'Fork CMS template');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'TemplateLanguage', 'taal template');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'TotalSentMailings', 'verzonden mailings');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'UnopenedMailings', 'ongeopende mailings');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'UnsentMailings', 'concepten');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'Who', 'wie?');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'WillBeSentOn', 'wordt verzonden op');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'WizardInformation', 'configuratie');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'WizardTemplate', 'template');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'WizardContent', 'inhoud');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'lbl', 'WizardSend', 'verzenden');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'AccountLinked', 'Uw CampaignMonitor account is met succes gekoppeld aan Fork.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'AddMultipleEmails', 'voeg meerdere email adressen toe door deze te scheiden met een komma (,)');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'BackToCampaigns', 'Terug naar het campagne-overzicht');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'BackToMailings', 'Terug naar het mailings-overzicht');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'BackToStatistics', 'Terug naar de statistieken voor &ldquo;%1$s&rdquo;');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'CampaignAdded', 'De campagne werd met succes toegevoegd.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'CampaignEdited', 'De campagne werd met succes gewijzigd.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'CampaignMailings', 'mailings in deze campagne');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'ClickedLinks', 'ontvangers hebben op links geklikt.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'ClicksAmount', 'aantal keer aangeklikt');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'ClicksBreakdown', '%1$s van geopend, %2$s van verzonden');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'ClicksOpened', 'aantal keer geopend');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'ClientLinked', 'De client &ldquo;%1$s&rdquo; is met succes gekoppeld aan Fork.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'CreateGroupByAddresses', 'Maak een nieuwe doelgroep aan met onderstaande e-mailadressen');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'DefaultGroup', 'Een taal selecteren markeert deze doelgroep als de standaard groep voor die taal. Dit betekent dat bezoekers die zich inschrijven voor de mailinglist in deze taal-versie van je website terechtkomen in deze groep. Er kan maar &eacute;&eacute; doelgroep gekozen worden per taal.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'DeleteAddresses', 'De e-mail(s) werd(en) met succes verwijderd.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'DeleteBounces', 'Verwijder alle hard bounces');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'DeletedBounces', 'De hard bounces voor deze mailing zijn verwijderd.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'DeletedCustomFields', 'De variabele velden voor doelgroep &ldquo;%1$s&rdquo; zijn verwijderd.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'DeleteCampaigns', 'De campagne(s) werd(en) met succes verwijderd.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'DeleteGroups', 'De doelgroep(en) werd(en) met succes verwijderd.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'DeleteMailings', 'De mailing(s) werd(en) met succes verwijderd.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'EditMailingCampaign', 'Wijzig campagne');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'ExportFailed', 'Er ging iets mis bij het exporteren.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'GroupAdded', 'De doelgroep werd met succes toegevoegd.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'GroupsNumberOfRecipients', 'Deze doelgroep bevat %1$s adres(sen).');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'GroupsImported', 'Er werden %1$s groep(en) met %2$s adressen geïmporteerd uit CampaignMonitor. Vergeet niet de standaard-doelgroep per taal in te stellen!');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'HelpCMURL', 'De URL van de CampaignMonitor API voor jouw account. (vb. *.createsend.com)');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'HelpCustomFields', 'Variabele velden zijn velden die per e-mailadres in een doelgroep een unieke waarde kunnen hebben, met als doel mailings te personaliseren.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'ImportedAddresses', '%1$s adressen zijn ge&iuml;mporteerd in %2$s doelgroep(en).');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'ImportFailedDownloadCSV', '<a href="%1$s">Download hier</a> een CSV-bestand met de foute adressen.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'ImportGroups', 'Fork heeft de volgende doelgroepen gevonden in Campaignmonitor');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'ImportGroupsTitle', 'Importeer doelgroepen uit CampaignMonitor');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'ImportRecentlyFailed', 'Niet alle adressen werden geïmporteerd.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'LinkCMAccount', 'Koppel uw CampaignMonitor account');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'MailingAdded', 'De mailing werd met succes toegevoegd.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'MailingConfirmSend', 'Bent u zeker dat u deze mailing wil versturen?');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'MailingConfirmTitle', 'Verstuur deze mailing.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'MailingCopied', 'De mailing &ldquo;%1$s&rdquo; werd met succes gekopi&euml;erd.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'MailingCSVBounces', 'bounces');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'MailingCSVBouncesPercentage', '% bounces');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'MailingCSVOpens', 'geopende mailings');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'MailingCSVRecipients', 'totaal verzonden mailings');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'MailingCSVUniqueOpens', 'uniek geopende mailings');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'MailingCSVUniqueOpensPercentage', '% geopende mailings');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'MailingCSVUnopens', 'ongeopende mailings');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'MailingCSVUnopensPercentage', '% ongeopende mailings');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'MailingCSVUnsubscribes', 'unsubscribes');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'MailingEdited', 'De mailing werd met succes gewijzigd.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'MailingLinks', 'links in deze mailing');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'MailingSent', 'De mailing werd met succes verstuurd!');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'NameInternalUseOnly', 'Deze naam is enkel voor intern gebruik.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'NoClientID', 'Er is nog geen CampaignMonitor client gekoppeld aan Fork. Kies een bestaande client uit de lijst, of vul de onderstaande velden in om een client te koppelen.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'NoDefault', 'Dit is geen standaard doelgroep.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'NoDefaultsSet', 'Een standaard-doelgroep voor een taal is de groep waar bezoekers in terechtkomen wanneer ze zich inschrijven via de inschrijfformulieren op je Fork-website. Editeer een groep en kies een taal om hem in te stellen als standaard-groep voor die taal.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'NoDefaultsSetTitle', 'niet alle standaard-doelgroepen zijn ingesteld.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'NoResultsForFilter', 'Geen resultaten voor de zoekopdracht &ldquo;%1$s&rdquo;.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'NoUnsentMailings', 'Er zijn geen concepten.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'PeopleGroups', 'Deze personen komen uit volgende doelgroepen:');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'PlainTextEditable', 'De tekstuele versie van iedere, individuele mailing aanpasbaar maken.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'PreviewSent', 'De preview-mail werd verzonden naar %1$s.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'Reason', 'reden');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'RecipientStatisticsCampaign', 'U staat op het punt om de mailing "%1$s" uit de campagne "%2$s" naar %3$s %4$s te sturen.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'RecipientStatisticsNoCampaign', 'U staat op het punt om de mailing "%1$s" naar %2$s %3$s te sturen.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'ResetCampaigns', 'campagnes');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'ResetDone', 'Vergeet niet om de client te verwijderen in CM via (http://nieuwsbrieven.netlash.com)<br />Dit moet zo omdat er een limiet is hoeveel keer een IP clients kan toevoegen/verwijderen via de API.<br /><br />Gebruik de volgende client ID als je de huidige wenst te blijven gebruiken: <strong>%1$s</strong>');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'ResetGroups', 'doelgroepen (m&eacute;t adressen)');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'ResetLabels', 'labels');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'ResetMailings', 'mailings');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'ResetSettings', 'module-settings');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'SendOn', 'De mailing wordt verzonden op %1$s om %2$s.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'TemplateLanguage', 'taal van de template');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'UnlinkCMAccount', 'Ontkoppel CampaignMonitor account');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'Unlinked', 'De CampaignMonitor account is ontkoppeld.');
		$this->insertLocale('nl', 'backend', 'mailmotor', 'msg', 'ViewMailings', 'Bekijk je mailings');

		$this->insertLocale('nl', 'backend', 'pages', 'lbl', 'SentMailings', 'verzonden nieuwsbrieven');
		$this->insertLocale('nl', 'backend', 'pages', 'lbl', 'SubscribeForm', 'inschrijvingsformulier');
		$this->insertLocale('nl', 'backend', 'pages', 'lbl', 'UnsubscribeForm', 'uitschrijvingsformulier');
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
		$templateID = (int) $this->getDB()->getVar('SELECT id FROM pages_templates WHERE label = ?', 'Triton - Default');

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