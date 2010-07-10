<?php

/**
 * LocaleInstall
 * Installer for the locale module
 *
 * @package		installer
 * @subpackage	locale
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class LocaleInstall extends ModuleInstaller
{
	/**
	 * Install the module
	 *
	 * @return	void
	 */
	protected function execute()
	{
		// load install.sql
		$this->importSQL(PATH_WWW .'/backend/modules/locale/installer/install.sql');

		// add 'locale' as a module
		$this->addModule('locale', 'The module to manage your website/cms locale.');

		// general settings
		$this->setSetting('locale', 'languages', array('de', 'en', 'es', 'fr', 'nl'));
		$this->setSetting('locale', 'requires_akismet', false);
		$this->setSetting('locale', 'requires_google_maps', false);

		// module rights
		$this->setModuleRights(1, 'locale');

		// action rights
		$this->setActionRights(1, 'locale', 'add');
		$this->setActionRights(1, 'locale', 'analyse');
		$this->setActionRights(1, 'locale', 'edit');
		$this->setActionRights(1, 'locale', 'index');
		$this->setActionRights(1, 'locale', 'mass_action');
	}
}

?>