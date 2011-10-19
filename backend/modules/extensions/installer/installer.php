<?php

/**
 * Installer for the extensions module
 *
 * @package		installer
 * @subpackage	extensions
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.6.6
 */
class ExtensionsInstaller extends ModuleInstaller
{
	/**
	 * Install the module
	 *
	 * @return	void
	 */
	public function install()
	{
		// add 'content_blocks' as a module
		$this->addModule('extensions', 'The content blocks module.');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		// module rights
		$this->setModuleRights(1, 'extensions');

		// action rights
		$this->setActionRights(1, 'content_blocks', 'modules');

		// set navigation
		$navigationSettingsId = $this->setNavigation(null, 'Settings');
		$navigationExtensionsId = $this->setNavigation($navigationSettingsId, 'Extensions');
		$this->setNavigation($navigationExtensionsId, 'Modules', 'extensions/modules', array('extensions/module_detail'));
	}
}

?>