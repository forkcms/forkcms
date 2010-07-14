<?php

/**
 * ContentBlocksInstall
 * Installer for the content blocks module
 *
 * @package		installer
 * @subpackage	content_blocks
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
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
		$this->importSQL(PATH_WWW .'/backend/modules/content_blocks/installer/install.sql');

		// add 'content_blocks' as a module
		$this->addModule('content_blocks', 'The content blocks module.');

		// general settings
		$this->setSetting('content_blocks', 'requires_akismet', false);
		$this->setSetting('content_blocks', 'requires_google_maps', false);
		$this->setSetting('content_blocks', 'max_num_revisions', 20);

		// module rights
		$this->setModuleRights(1, 'content_blocks');

		// action rights
		$this->setActionRights(1, 'content_blocks', 'add');
		$this->setActionRights(1, 'content_blocks', 'delete');
		$this->setActionRights(1, 'content_blocks', 'edit');
		$this->setActionRights(1, 'content_blocks', 'index');

		// insert locale
		$this->insertLocale('nl', 'backend', 'content_blocks', 'lbl', 'Add', 'Inhoudsblok toevoegen');
		$this->insertLocale('nl', 'backend', 'content_blocks', 'msg', 'EditContentBlock', 'bewerk inhoudsblok "%1$s"');
		$this->insertLocale('nl', 'backend', 'content_blocks', 'msg', 'ConfirmDelete', 'Ben je zeker dat je de inhoudsblok "%1$s" wil verwijderen?');
		$this->insertLocale('nl', 'backend', 'content_blocks', 'msg', 'Added', 'Het inhoudsblok "%1$s" werd toegevoegd.');
		$this->insertLocale('nl', 'backend', 'content_blocks', 'msg', 'Edited', 'Het inhoudsblok "%1$s" werd opgeslagen.');
		$this->insertLocale('nl', 'backend', 'content_blocks', 'msg', 'Deleted', 'Het inhoudsblok "%1$s" werd verwijderd.');
	}
}

?>