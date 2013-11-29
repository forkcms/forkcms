<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the sitemap module
 *
 * @author Jonas Goderis <jonas.goderis@wijs.be>
 */
class SitemapInstaller extends ModuleInstaller
{
	/**
	 * Install the module
	 */
	public function install()
	{
		// $this->importSQL(dirname(__FILE__) . '/data/install.sql');

		$this->addModule('sitemap');

		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		$this->setModuleRights(1, 'sitemap');

		$this->setActionRights(1, 'sitemap', 'settings');

		// set navigation
		$navigationSettingsId = $this->setNavigation(null, 'Settings');
		$navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
		$this->setNavigation($navigationModulesId, 'Sitemap', 'sitemap/settings');
	}
}