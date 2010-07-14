<?php

/**
 * SettingsInstall
 * Installer for the settings module
 *
 * @package		installer
 * @subpackage	contact
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
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
		// load install.sql
		$this->importSQL(PATH_WWW .'/backend/modules/settings/installer/install.sql');

		// add 'settings' as a module
		$this->addModule('settings', 'The module to manage your settings.');

		// general settings
		$this->setSetting('settings', 'requires_akismet', false);
		$this->setSetting('settings', 'requires_google_maps', false);

		// module rights
		$this->setModuleRights(1, 'settings');

		// action rights
		$this->setActionRights(1, 'settings', 'index');

		// insert locale
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'ConfigurationError', 'Sommige instellingen zijn nog niet geconfigureerd:');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpAPIKeys', 'Toegangscodes voor gebruikte webservices:');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpDateFormatLong', 'Formaat dat bij de overzichtspagina\'s en detailweergaves wordt gebruikt.');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpDateFormatShort', 'Dit formaat wordt voornamelijk gebruikt bij tabelweergaves.');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpDomains', 'Vul de domeinen in waarop de website te bereiken is (1 domein per regel)');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpEmailWebmaster', 'Stuur notificaties van het CMS naar dit e-mailadres.');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpLanguages', 'Duid aan welke talen toegankelijk zijn voor bezoekers');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpRedirectLanguages', 'Duid aan in welke talen mensen op basis van hun browser mogen terechtkomen.');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpScripts', 'Plaats hier code die op elke pagina geladen moet worden. (bvb. Google Analytics).');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpThemes', 'Duid aan welk thema je wil gebruiken.');
		$this->insertLocale('nl', 'backend', 'settings', 'msg', 'HelpTimeFormat', 'Dit formaat wordt gehanteerd bij het weergeven van datums in de frontend.');
	}
}

?>