<?php

/**
 * Installer for the form_builder module
 *
 * @package		installer
 * @subpackage	form_builder
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class TestInstaller extends ModuleInstaller
{
	/**
	 * Install the module
	 *
	 * @return	void
	 */
	public function install()
	{
		// add as a module
		$this->addModule('test');

		// module rights
		$this->setModuleRights(1, 'test');

		// set navigation
		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$this->setNavigation($navigationModulesId, 'Test', 'test/index');
	}
}

?>