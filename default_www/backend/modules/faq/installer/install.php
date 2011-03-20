<?php

/**
 * Installer for the faq module
 *
 * @package		installer
 * @subpackage	faq
 *
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.1
 */
class FaqInstall extends ModuleInstaller
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

		// add 'search' as a module
		$this->addModule('faq', 'The faq module.');

		// module rights
		$this->setModuleRights(1, 'faq');

		// action rights
		$this->setActionRights(1, 'faq', 'index');
		$this->setActionRights(1, 'faq', 'add');
		$this->setActionRights(1, 'faq', 'edit');
		$this->setActionRights(1, 'faq', 'delete');
		$this->setActionRights(1, 'faq', 'sequence');
		$this->setActionRights(1, 'faq', 'categories');
		$this->setActionRights(1, 'faq', 'add_category');
		$this->setActionRights(1, 'faq', 'edit_category');
		$this->setActionRights(1, 'faq', 'delete_category');
		$this->setActionRights(1, 'faq', 'sequence_questions');

		// extras
		$this->insertExtra('faq', 'block', 'Faq', 'index', null, 'N', 9001);
		$this->insertExtra('faq', 'block', 'Category', 'category', null, 'N', 9002);

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');
	}
}

?>