<?php

/**
 * Installer for the locale module
 *
 * @package		installer
 * @subpackage	locale
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @author		Dieter Vanden Eynde <dieter@dieterve.be>
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
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// add 'locale' as a module
		$this->addModule('locale', 'The module to manage your website/cms locale.');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		// general settings
		$this->setSetting('locale', 'languages', array('de', 'en', 'es', 'fr', 'nl'));

		// module rights
		$this->setModuleRights(1, 'locale');

		// action rights
		$this->setActionRights(1, 'locale', 'add');
		$this->setActionRights(1, 'locale', 'analyse');
		$this->setActionRights(1, 'locale', 'edit');
		$this->setActionRights(1, 'locale', 'export');
		$this->setActionRights(1, 'locale', 'export_analyse');
		$this->setActionRights(1, 'locale', 'import');
		$this->setActionRights(1, 'locale', 'index');
		$this->setActionRights(1, 'locale', 'mass_action');
		$this->setActionRights(1, 'locale', 'save_translation');
		$this->setActionRights(1, 'locale', 'export');
		$this->setActionRights(1, 'locale', 'import');
		$this->setActionRights(1, 'locale', 'delete');
	}
}

?>