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
class FormBuilderInstall extends ModuleInstaller
{
	/**
	 * Install the module
	 *
	 * @return	void
	 */
	protected function execute()
	{
		// load install.sql
		$this->importSQL(PATH_WWW . '/backend/modules/form_builder/installer/data/install.sql');

		// add as a module
		$this->addModule('form_builder', 'The module to create and manage forms.');

		// module rights
		$this->setModuleRights(1, 'form_builder');

		// action rights
		$this->setActionRights(1, 'form_builder', 'add');
		$this->setActionRights(1, 'form_builder', 'edit');
		$this->setActionRights(1, 'form_builder', 'delete');
		$this->setActionRights(1, 'form_builder', 'index');
		$this->setActionRights(1, 'form_builder', 'data');
		$this->setActionRights(1, 'form_builder', 'data_details');
		$this->setActionRights(1, 'form_builder', 'mass_data_action');
		$this->setActionRights(1, 'form_builder', 'get_field');
		$this->setActionRights(1, 'form_builder', 'delete_field');
		$this->setActionRights(1, 'form_builder', 'save_field');
		$this->setActionRights(1, 'form_builder', 'sequence');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');
	}
}

?>