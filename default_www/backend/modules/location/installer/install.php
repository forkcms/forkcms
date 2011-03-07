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
		$this->importSQL(dirname(__FILE__) . '/install.sql');

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

		// insert locale (nl)
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Address', 'adres');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'City', 'stad');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Country', 'land');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'GroupMap', 'algemene kaart: alle locaties');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Height', 'hoogte');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'IndividualMap', 'widget: individuele kaart');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Location', 'locatie');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Number', 'nummer');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Street', 'straat');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Width', 'breedte');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Zip', 'postcode');
		$this->insertLocale('nl', 'backend', 'location', 'lbl', 'Auto', 'automatisch');
		$this->insertLocale('nl', 'backend', 'location', 'lbl', 'Hybrid', 'hybride');
		$this->insertLocale('nl', 'backend', 'location', 'lbl', 'Map', 'kaart');
		$this->insertLocale('nl', 'backend', 'location', 'lbl', 'MapType', 'kaarttype');
		$this->insertLocale('nl', 'backend', 'location', 'lbl', 'Roadmap', 'wegenkaart');
		$this->insertLocale('nl', 'backend', 'location', 'lbl', 'Satellite', 'satelliet');
		$this->insertLocale('nl', 'backend', 'location', 'lbl', 'Terrain', 'terrein');
		$this->insertLocale('nl', 'backend', 'location', 'lbl', 'ZoomLevel', 'zoom niveau');
		$this->insertLocale('nl', 'backend', 'location', 'err', 'AddressCouldNotBeGeocoded', 'Dit adres kon niet worden omgezet naar coördinaten.');

		// insert locale (en)
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Address', 'address');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'City', 'city');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Country', 'country');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'GroupMap', 'general map: all locations');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Height', 'height');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'IndividualMap', 'widget: individual map');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Location', 'location');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Number', 'number');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Street', 'street');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Width', 'width');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Zip', 'zip code');
		$this->insertLocale('en', 'backend', 'location', 'lbl', 'Auto', 'automatic');
		$this->insertLocale('en', 'backend', 'location', 'lbl', 'Hybrid', 'hybrid');
		$this->insertLocale('en', 'backend', 'location', 'lbl', 'Map', 'map');
		$this->insertLocale('en', 'backend', 'location', 'lbl', 'MapType', 'map type');
		$this->insertLocale('en', 'backend', 'location', 'lbl', 'Roadmap', 'road map');
		$this->insertLocale('en', 'backend', 'location', 'lbl', 'Satellite', 'satellite');
		$this->insertLocale('en', 'backend', 'location', 'lbl', 'Terrain', 'terrain');
		$this->insertLocale('en', 'backend', 'location', 'lbl', 'ZoomLevel', 'zoom level');
		$this->insertLocale('en', 'backend', 'location', 'err', 'AddressCouldNotBeGeocoded', 'Address couldn\'t be converted into coordinates.');
	}
}

?>