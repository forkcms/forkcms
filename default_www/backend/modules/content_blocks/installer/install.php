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
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

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

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');
	}
}

?>