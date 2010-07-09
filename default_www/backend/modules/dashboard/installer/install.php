<?php

class DashboardInstall extends ModuleInstaller
{
	public function __construct(SpoonDatabase $db, array $languages)
	{
		// set database instance
		$this->db = $db;

		// add 'dashboard' as a module
		$this->addModule('dashboard', 'The dashboard containing module specific widgets.');

		// general settings
		$this->setSetting('dashboard', 'requires_akismet', false);
		$this->setSetting('dashboard', 'requires_google_maps', false);

		// module rights
		$this->setModuleRights(1, 'dashboard');

		// action rights
		$this->setActionRights(1, 'dashboard', 'index');
	}
}

?>