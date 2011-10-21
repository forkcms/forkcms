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
		// add 'extensions' as a module
		$this->addModule('extensions');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		// module rights
		$this->setModuleRights(1, 'extensions');

		// action rights
		$this->setActionRights(1, 'content_blocks', 'modules');

		// settings navigation
		$navigationSettingsId = $this->setNavigation(null, 'Settings');
		$navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
		$this->setNavigation($navigationModulesId, 'Overview', 'extensions/modules', array(
			'extensions/module_detail',
			'extensions/module_upload'
		));
	}
}

?>