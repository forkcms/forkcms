<?php

/**
 * ModuleManagerInstall
 * Installer for the module_manager module
 *
 * @package		installer
 * @subpackage	module_manager
 *
 * @author 		Frederik Heyninck <frederik@figure8.be>
 * @since		2.0
 */
class ModuleManagerInstall extends ModuleInstaller
{
	/**
	 * Install the module
	 *
	 * @return	void
	 */
	protected function execute()
	{
		// add 'blog' as a module
		$this->addModule('module_manager', 'The module manager.');

		// module rights
		$this->setModuleRights(1, 'module_manager');

		// action rights
		$this->setActionRights(1, 'module_manager', 'actions');
		$this->setActionRights(1, 'module_manager', 'add_action');
		$this->setActionRights(1, 'module_manager', 'delete');
		$this->setActionRights(1, 'module_manager', 'delete_action');
		$this->setActionRights(1, 'module_manager', 'edit');
		$this->setActionRights(1, 'module_manager', 'edit_action');
		$this->setActionRights(1, 'module_manager', 'index');
		$this->setActionRights(1, 'module_manager', 'install');

		// insert locale (nl)
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'ModuleManager', 'module beheer');
		$this->insertLocale('nl', 'backend', 'module_manager', 'lbl', 'ModuleManager', 'module beheer');
		$this->insertLocale('nl', 'backend', 'module_manager', 'lbl', 'Actions', 'acties');
		$this->insertLocale('nl', 'backend', 'module_manager', 'lbl', 'Reinstall', 'herinstalleer');
		$this->insertLocale('nl', 'backend', 'module_manager', 'lbl', 'AddAction', 'voeg actie toe');
		$this->insertLocale('nl', 'backend', 'module_manager', 'lbl', 'NoInstaller', 'geen installer');
		$this->insertLocale('nl', 'backend', 'module_manager', 'lbl', 'NonInstalledModules', 'niet geinstalleerde modules');
		$this->insertLocale('nl', 'backend', 'module_manager', 'lbl', 'InstalledActive', 'geinstalleerde modules');
		$this->insertLocale('nl', 'backend', 'module_manager', 'lbl', 'InstalledNonActive', 'geinstalleerde niet actieve modules');
		$this->insertLocale('nl', 'backend', 'module_manager', 'lbl', 'Action', 'actie');
		$this->insertLocale('nl', 'backend', 'module_manager', 'lbl', 'Level', 'level');
		$this->insertLocale('nl', 'backend', 'module_manager', 'lbl', 'ChooseALevel', 'kies een  level');
		$this->insertLocale('nl', 'backend', 'module_manager', 'lbl', 'ChooseAGroup', 'kies een groep');
		$this->insertLocale('nl', 'backend', 'module_manager', 'lbl', 'ActionsForModule', 'acties voor module "%1$s"');
		$this->insertLocale('nl', 'backend', 'module_manager', 'lbl', 'GroupName', 'groep');
		$this->insertLocale('en', 'backend', 'module_manager', 'lbl', 'Install', 'installeer');
		
		// insert locale (en)
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'ModuleManager', 'module manager');
		$this->insertLocale('en', 'backend', 'module_manager', 'lbl', 'ModuleManager', 'module manager');
		$this->insertLocale('en', 'backend', 'module_manager', 'lbl', 'Actions', 'actions');
		$this->insertLocale('en', 'backend', 'module_manager', 'lbl', 'Reinstall', 'reinstall');
		$this->insertLocale('en', 'backend', 'module_manager', 'lbl', 'AddAction', 'add action');
		$this->insertLocale('en', 'backend', 'module_manager', 'lbl', 'NoInstaller', 'no installer');
		$this->insertLocale('en', 'backend', 'module_manager', 'lbl', 'NonInstalledModules', 'non installed modules');
		$this->insertLocale('en', 'backend', 'module_manager', 'lbl', 'InstalledActive', 'installed modules');
		$this->insertLocale('en', 'backend', 'module_manager', 'lbl', 'InstalledNonActive', 'installed non active modules');
		$this->insertLocale('en', 'backend', 'module_manager', 'lbl', 'Action', 'action');
		$this->insertLocale('en', 'backend', 'module_manager', 'lbl', 'Level', 'level');
		$this->insertLocale('en', 'backend', 'module_manager', 'lbl', 'ChooseALevel', 'choose a level');
		$this->insertLocale('en', 'backend', 'module_manager', 'lbl', 'ChooseAGroup', 'choose a group');
		$this->insertLocale('en', 'backend', 'module_manager', 'lbl', 'ActionsForModule', 'actions for module "%1$s"');
		$this->insertLocale('en', 'backend', 'module_manager', 'lbl', 'GroupName', 'group');
		$this->insertLocale('en', 'backend', 'module_manager', 'lbl', 'Install', 'install');
		
	}
}

?>