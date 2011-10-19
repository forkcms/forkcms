<?php

/**
 * Installer for the settings module
 *
 * @package		installer
 * @subpackage	settings
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class SettingsInstaller extends ModuleInstaller
{
	/**
	 * Install the module
	 *
	 * @return	void
	 */
	public function install()
	{
		// add 'settings' as a module
		$this->addModule('settings');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		// module rights
		$this->setModuleRights(1, 'settings');

		// action rights
		$this->setActionRights(1, 'settings', 'index');
		$this->setActionRights(1, 'settings', 'themes');
		$this->setActionRights(1, 'settings', 'email');
		$this->setActionRights(1, 'settings', 'seo');
		$this->setActionRights(1, 'settings', 'test_email_connection');

		// set navigation (settings should be last tab)
		$navigationSettingsId = $this->setNavigation(null, 'Settings', null, null, 999);

		// general navigation
		$this->setNavigation($navigationSettingsId, 'General', 'settings/index', null, 1);
		$navigationAdvancedId = $this->setNavigation($navigationSettingsId, 'Advanced', null, null, 2);
		$this->setNavigation($navigationAdvancedId, 'Email', 'settings/email');

		// theme navigation
		$navigationThemesId = $this->setNavigation($navigationSettingsId, 'Themes', null, null, 3);
		$this->setNavigation($navigationThemesId, 'ThemesSelection', 'settings/themes');
		$this->setNavigation($navigationThemesId, 'Templates', 'pages/templates', array(
			'pages/add_template',
			'pages/edit_template'
		));

		// modules settings navigation
		$this->setNavigation($navigationSettingsId, 'Modules', null, null, 6);
	}
}

?>