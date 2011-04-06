<?php

/**
 * Installer for the location module
 *
 * @package		installer
 * @subpackage	location
 *
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.1
 */
class LocationInstall extends ModuleInstaller
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

		// add 'location' as a module
		$this->addModule('location', 'The location module.');

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
		$this->setActionRights(1, 'location', 'settings');

		// add extra's
		$this->insertExtra('location', 'block', 'Location', null, 'a:1:{s:3:"url";s:37:"/private/nl/location/index?token=true";}', 'N');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');
	}
}

?>