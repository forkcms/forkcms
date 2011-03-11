<?php

/**
 * Installer for the settings module
 *
 * @package		installer
 * @subpackage	settings
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class SettingsInstall extends ModuleInstaller
{
	/**
	 * Install the module
	 *
	 * @return	void
	 */
	protected function execute()
	{
		// add 'settings' as a module
		$this->addModule('settings', 'The module to manage your settings.');

		// module rights
		$this->setModuleRights(1, 'settings');

		// action rights
		$this->setActionRights(1, 'settings', 'index');
		$this->setActionRights(1, 'settings', 'themes');
		$this->setActionRights(1, 'settings', 'email');

		// insert locale (nl)
		$this->insertLocale('nl', 'backend', 'settings', 'err', 'PortIsRequired', 'Poort is verplicht.');
		$this->insertLocale('nl', 'backend', 'settings', 'err', 'ServerIsRequired', 'Server is verplicht.');
		$this->insertLocale('nl', 'backend', 'settings', 'lbl', 'AdminIds', 'admin-ids');
		$this->insertLocale('nl', 'backend', 'settings', 'lbl', 'ApplicationId', 'toepassings-id');
		$this->insertLocale('nl', 'backend', 'settings', 'lbl', 'ApplicationSecret', 'app secret');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'ConfigurationError', 'Sommige instellingen zijn nog niet geconfigureerd:');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpAPIKeys', 'Toegangscodes voor gebruikte webservices:');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpDateFormatLong', 'Formaat dat bij de overzichtspagina\'s en detailweergaves wordt gebruikt.');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpDateFormatShort', 'Dit formaat wordt voornamelijk gebruikt bij tabelweergaves.');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpDomains', 'Vul de domeinen in waarop de website te bereiken is (1 domein per regel)');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpEmailWebmaster', 'Stuur notificaties van het CMS naar dit e-mailadres.');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpFacebookAdminIds', 'Een lijst met de Facebook-gebruikers hun ID en/of het id van de Facebook-applicatie die de paginas mogen beheren.');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpFacebookApiKey', 'De API-sleutel van je Facebook-toepassing, je kan er een aanmaken via <a href="http://facebook.com/developers" class="targetBlank">http://facebook.com/developers</a>.');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpFacebookApplicationId', 'Het toepassings-id van je Facebook-toepassing.');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpFacebookApplicationSecret', 'De App Secret van je Facebook-toepassing.');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpLanguages', 'Duid aan welke talen toegankelijk zijn voor bezoekers');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpNumberFormat', 'Dit formaat wordt gehanteerd bij het weergeven van getallen in de frontend.');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpRedirectLanguages', 'Duid aan in welke talen mensen op basis van hun browser mogen terechtkomen.');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpSendingEmails', 'Je kan e-mails versturen op 2 manieren. Door de ingebouwd mail functie van PHP of via SMTP. We raden je aan om SMTP te gebruiken, aangezien e-mails hierdoor minder snel in de spamfilter zullen terechtkomen.');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpScriptsFoot', 'Plaats hier code die onderaan elke pagina geladen moet worden. (bvb. Google Analytics)');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpScriptsFootLabel', 'Einde van <code>&lt;body&gt;</code> script(s)');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpScriptsHead', 'Plaats hier code die op elke pagina geladen moet worden in de <code>&lt;head&gt;</code>-tag.');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpScriptsHeadLabel', '<code>&lt;head&gt;</code> script(s)');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpThemes', 'Duid aan welk thema je wil gebruiken.');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpTimeFormat', 'Dit formaat wordt gehanteerd bij het weergeven van datums in de frontend.');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'NoAdminIds', 'Nog geen admin-ids');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'SendTestMail', 'verstuur testmail');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'TestMessage', 'dit is een test');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'TestWasSent', 'De testmail is verzonden.');

		// insert locale (en)
		$this->insertLocale('en', 'backend', 'settings', 'err', 'PortIsRequired', 'Port is required..');
		$this->insertLocale('en', 'backend', 'settings', 'err', 'ServerIsRequired', 'Server is required.');
		$this->insertLocale('en', 'backend', 'settings', 'lbl', 'AdminIds', 'admin ids');
		$this->insertLocale('en', 'backend', 'settings', 'lbl', 'ApplicationId', 'application id');
		$this->insertLocale('en', 'backend', 'settings', 'lbl', 'ApplicationSecret', 'app secret');
		$this->insertLocale('en', 'backend', 'settings', 'msg', 'ConfigurationError', 'Some settings are not yet configured.');
		$this->insertLocale('en', 'backend', 'settings', 'msg', 'HelpAPIKeys', 'Access codes for webservices.');
		$this->insertLocale('en', 'backend', 'settings', 'msg', 'HelpDateFormatLong', 'Format that\'s used on overview and detail pages.');
		$this->insertLocale('en', 'backend', 'settings', 'msg', 'HelpDateFormatShort', 'This format is mostly used in table overviews.');
		$this->insertLocale('en', 'backend', 'settings', 'msg', 'HelpDomains', 'Enter the domains on which this website can be reached. (Split domains with linebreaks.)');
		$this->insertLocale('en', 'backend', 'settings', 'msg', 'HelpEmailWebmaster', 'Send CMS notifications to this e-mailaddress.');
		$this->insertLocale('en', 'backend', 'settings', 'msg', 'HelpFacebookAdminIds', 'Either Facebook user IDs or a Facebook Platform application ID that administers this website.');
		$this->insertLocale('en', 'backend', 'settings', 'msg', 'HelpFacebookApiKey', 'The API key of your Facebook application.');
		$this->insertLocale('en', 'backend', 'settings', 'msg', 'HelpFacebookApplicationId', 'The id of your Facebook application, you can create one at <a href="http://facebook.com/developers" class="targetBlank">http://facebook.com/developers</a>.');
		$this->insertLocale('en', 'backend', 'settings', 'msg', 'HelpFacebookApplicationSecret', 'The secret of your Facebook application.');
		$this->insertLocale('en', 'backend', 'settings', 'msg', 'HelpLanguages', 'Select the languages that are accessible for visitors.');
		$this->insertLocale('en', 'backend', 'settings', 'msg', 'HelpNumberFormat', 'This format is used to display numbers on the website.');
		$this->insertLocale('en', 'backend', 'settings', 'msg', 'HelpRedirectLanguages', 'Select the languages that people may automatically be redirect to by their browser.');
		$this->insertLocale('en', 'backend', 'settings', 'msg', 'HelpSendingEmails', 'You can send emails in 2 ways. By using PHP\'s built-in mail method or via SMTP. We advice you to use SMTP, since this ensures that e-mails are less frequently marked as spam.');
		$this->insertLocale('en', 'backend', 'settings', 'msg', 'HelpScriptsFoot', 'Paste code that needs to be loaded at the end of the <code>&lt;body&gt;</code> tag here (e.g. Google Analytics).');
		$this->insertLocale('en', 'backend', 'settings', 'msg', 'HelpScriptsFootLabel', 'End of <code>&lt;body&gt;</code> script(s)');
		$this->insertLocale('en', 'backend', 'settings', 'msg', 'HelpScriptsHead', 'Paste code that needs to be loaded in the <code>&lt;head&gt;</code> section here.');
		$this->insertLocale('en', 'backend', 'settings', 'msg', 'HelpScriptsHeadLabel', '<code>&lt;head&gt;</code> script(s)');
		$this->insertLocale('en', 'backend', 'settings', 'msg', 'HelpThemes', 'Select the theme you wish to use.');
		$this->insertLocale('en', 'backend', 'settings', 'msg', 'HelpTimeFormat', 'This format is used to display dates on the website.');
		$this->insertLocale('en', 'backend', 'settings', 'msg', 'NoAdminIds', 'No admin ids yet.');
		$this->insertLocale('en', 'backend', 'settings', 'msg', 'SendTestMail', 'send test email');
		$this->insertLocale('en', 'backend', 'settings', 'msg', 'TestMessage', 'this is just a test');
		$this->insertLocale('en', 'backend', 'settings', 'msg', 'TestWasSent', 'The test email was sent.');
	}
}

?>