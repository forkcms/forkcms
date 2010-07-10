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
	}
}

?>