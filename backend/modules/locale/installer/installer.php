<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the locale module
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter@dieterve.be>
 */
class LocaleInstaller extends ModuleInstaller
{
	/**
	 * Install the module
	 */
	public function install()
	{
		// load install.sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// add 'locale' as a module
		$this->addModule('locale');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		// import core locale
		$this->importLocale(dirname(dirname(dirname(dirname(__FILE__)))) . '/core/installer/data/locale.xml');

		// import dashboard locale
		$this->importLocale(dirname(dirname(dirname(__FILE__))) . '/dashboard/installer/data/locale.xml');

		// module rights
		$this->setModuleRights(1, 'locale');

		// action rights
		$this->setActionRights(1, 'locale', 'add');
		$this->setActionRights(1, 'locale', 'analyse');
		$this->setActionRights(1, 'locale', 'edit');
		$this->setActionRights(1, 'locale', 'export_analyse');
		$this->setActionRights(1, 'locale', 'index');
		$this->setActionRights(1, 'locale', 'mass_action');
		$this->setActionRights(1, 'locale', 'save_translation');
		$this->setActionRights(1, 'locale', 'export');
		$this->setActionRights(1, 'locale', 'import');
		$this->setActionRights(1, 'locale', 'delete');

		// set navigation
		$navigationSettingsId = $this->setNavigation(null, 'Settings', null, null, 999);
		$this->setNavigation($navigationSettingsId, 'Translations', 'locale/index', array(
			'locale/add',
			'locale/edit',
			'locale/import',
			'locale/analyse'
		), 4);
	}
}
