<?php

class SettingsInstall extends ModuleInstaller
{
	public function __construct(SpoonDatabase $db, array $languages)
	{
		// set database instance
		$this->db = $db;

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