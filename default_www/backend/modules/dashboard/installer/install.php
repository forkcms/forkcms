<?php

/**
 * DashboardInstall
 * Installer for the dashboard module
 *
 * @package		installer
 * @subpackage	dashboard
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class DashboardInstall extends ModuleInstaller
{
	/**
	 * Install the module
	 *
	 * @return	void
	 */
	protected function execute()
	{
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