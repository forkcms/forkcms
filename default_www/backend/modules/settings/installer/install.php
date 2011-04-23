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
		$this->setActionRights(1, 'settings', 'test_email_connection');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');
	}
}

?>