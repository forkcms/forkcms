<?php

/**
 * Installer for the content blocks module
 *
 * @package		installer
 * @subpackage	content_blocks
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class ContentBlocksInstall extends ModuleInstaller
{
	/**
	 * Install the module
	 *
	 * @return	void
	 */
	protected function execute()
	{
		// load install.sql
		$this->importSQL(dirname(__FILE__) . '/install.sql');

		// add 'content_blocks' as a module
		$this->addModule('content_blocks', 'The content blocks module.');

		// general settings
		$this->setSetting('content_blocks', 'max_num_revisions', 20);

		// module rights
		$this->setModuleRights(1, 'content_blocks');

		// action rights
		$this->setActionRights(1, 'content_blocks', 'add');
		$this->setActionRights(1, 'content_blocks', 'delete');
		$this->setActionRights(1, 'content_blocks', 'edit');
		$this->setActionRights(1, 'content_blocks', 'index');

		// insert locale (nl)
		$this->insertLocale('nl', 'backend', 'content_blocks', 'lbl', 'Add', 'inhoudsblok toevoegen');
		$this->insertLocale('nl', 'backend', 'content_blocks', 'msg', 'EditContentBlock', 'bewerk inhoudsblok "%1$s"');
		$this->insertLocale('nl', 'backend', 'content_blocks', 'msg', 'ConfirmDelete', 'Ben je zeker dat je de inhoudsblok "%1$s" wil verwijderen?');
		$this->insertLocale('nl', 'backend', 'content_blocks', 'msg', 'Added', 'Het inhoudsblok "%1$s" werd toegevoegd.');
		$this->insertLocale('nl', 'backend', 'content_blocks', 'msg', 'Edited', 'Het inhoudsblok "%1$s" werd opgeslagen.');
		$this->insertLocale('nl', 'backend', 'content_blocks', 'msg', 'Deleted', 'Het inhoudsblok "%1$s" werd verwijderd.');

		// insert locale (en)
		$this->insertLocale('en', 'backend', 'content_blocks', 'lbl', 'Add', 'add content block');
		$this->insertLocale('en', 'backend', 'content_blocks', 'msg', 'EditContentBlock', 'edit content block "%1$s"');
		$this->insertLocale('en', 'backend', 'content_blocks', 'msg', 'ConfirmDelete', 'Are your sure you want to delete the content block "%1$s"?');
		$this->insertLocale('en', 'backend', 'content_blocks', 'msg', 'Added', 'The content block "%1$s" was added.');
		$this->insertLocale('en', 'backend', 'content_blocks', 'msg', 'Edited', 'The content block "%1$s" was saved.');
		$this->insertLocale('en', 'backend', 'content_blocks', 'msg', 'Deleted', 'The content block "%1$s" was deleted.');
	}
}

?>