<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the location module
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class LocationInstaller extends ModuleInstaller
{
	/**
	 * Install the module
	 */
	public function install()
	{
		// load install.sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// add 'location' as a module
		$this->addModule('location');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		// general settings
		$this->setSetting('location', 'zoom_level', 'auto');
		$this->setSetting('location', 'width', 400);
		$this->setSetting('location', 'height', 300);
		$this->setSetting('location', 'map_type', 'ROADMAP');
		$this->setSetting('location', 'zoom_level_widget', 13);
		$this->setSetting('location', 'width_widget', 400);
		$this->setSetting('location', 'height_widget', 300);
		$this->setSetting('location', 'map_type_widget', 'ROADMAP');

		// make module searchable
		$this->makeSearchable('location');

		// module rights
		$this->setModuleRights(1, 'location');

		// action rights
		$this->setActionRights(1, 'location', 'index');
		$this->setActionRights(1, 'location', 'add');
		$this->setActionRights(1, 'location', 'edit');
		$this->setActionRights(1, 'location', 'delete');
		$this->setActionRights(1, 'location', 'save_live_location');
		$this->setActionRights(1, 'location', 'update_marker');

		// set navigation
		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$this->setNavigation($navigationModulesId, 'Location', 'location/index', array('location/add', 'location/edit'));

		// settings navigation
		$navigationSettingsId = $this->setNavigation(null, 'Settings');
		$navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
		$this->setNavigation($navigationModulesId, 'Location', 'location/settings');

		// add extra's
		$this->insertExtra('location', 'block', 'Location', null, 'a:1:{s:3:"url";s:37:"/private/nl/location/index?token=true";}', 'N');
	}
}
