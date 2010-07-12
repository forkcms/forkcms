<?php

/**
 * ContactInstall
 * Installer for the contact module
 *
 * @package		installer
 * @subpackage	contact
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class ContactInstall extends ModuleInstaller
{
	/**
	 * Install the module
	 *
	 * @return	void
	 */
	protected function execute()
	{
		// load install.sql
		$this->importSQL(PATH_WWW .'/backend/modules/contact/installer/install.sql');

		// add 'contact' as a module
		$this->addModule('contact', 'The contact module.');

		// general settings
		$this->setSetting('contact', 'requires_akismet', false);
		$this->setSetting('contact', 'requires_google_maps', false);
	}
}

?>