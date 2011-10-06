<?php

/**
 * Installer for the analytics module
 *
 * @package		installer
 * @subpackage	analytics
 *
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.0
 */
class AnalyticsInstall extends ModuleInstaller
{
	/**
	 * Install the module
	 *
	 * @return	void
	 */
	protected function execute()
	{
		// load install.sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// add 'analytics' as a module
		$this->addModule('analytics', 'The analytics module.');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		// module rights
		$this->setModuleRights(1, 'analytics');

		// action rights
		$this->setActionRights(1, 'analytics', 'add_landing_page');
		$this->setActionRights(1, 'analytics', 'all_pages');
		$this->setActionRights(1, 'analytics', 'check_status');
		$this->setActionRights(1, 'analytics', 'content');
		$this->setActionRights(1, 'analytics', 'delete_landing_page');
		$this->setActionRights(1, 'analytics', 'detail_page');
		$this->setActionRights(1, 'analytics', 'exit_pages');
		$this->setActionRights(1, 'analytics', 'get_traffic_sources');
		$this->setActionRights(1, 'analytics', 'index');
		$this->setActionRights(1, 'analytics', 'landing_pages');
		$this->setActionRights(1, 'analytics', 'loading');
		$this->setActionRights(1, 'analytics', 'mass_landing_page_action');
		$this->setActionRights(1, 'analytics', 'refresh_traffic_sources');
		$this->setActionRights(1, 'analytics', 'settings');

		// set navigation
		$navigationMarketingId = $this->setNavigation(null, 'Marketing', 'analytics/index', null, 4);
		$navigationAnalyticsId = $this->setNavigation($navigationMarketingId, 'Analytics', 'analytics/index', array('analytics/loading'));
		$this->setNavigation($navigationAnalyticsId, 'Content', 'analytics/content');
		$this->setNavigation($navigationAnalyticsId, 'AllPages', 'analytics/all_pages');
		$this->setNavigation($navigationAnalyticsId, 'ExitPages', 'analytics/exit_pages');
		$this->setNavigation($navigationAnalyticsId, 'LandingPages', 'analytics/landing_pages', array(
			'analytics/add_landing_page',
			'analytics/edit_landing_page',
			'analytics/detail_page'
		));

		// settings navigation
		$navigationSettingsId = $this->setNavigation(null, 'Settings');
		$navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
		$this->setNavigation($navigationModulesId, 'Analytics', 'analytics/settings');
	}
}

?>