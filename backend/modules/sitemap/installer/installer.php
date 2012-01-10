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
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class SitemapInstaller extends ModuleInstaller
{
	public function install()
	{
		// import the sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// install the module in the database
		$this->addModule('sitemap', 'The sitemap module.');

		// install the locale, this is set here beceause we need the module for this
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		$this->setModuleRights(1, 'sitemap');

		$this->setActionRights(1, 'sitemap', 'settings');

		// settings navigation
		$navigationSettingsId = $this->setNavigation(null, 'Settings');
		$navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
		$this->setNavigation($navigationModulesId, 'Sitemap', 'sitemap/settings');
	}
}
