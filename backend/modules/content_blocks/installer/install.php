<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the content blocks module
 *
 * @author Davy Hellemans <davy@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class ContentBlocksInstall extends ModuleInstaller
{
	/**
	 * Install the module
	 */
	protected function execute()
	{
		// load install.sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// add 'content_blocks' as a module
		$this->addModule('content_blocks', 'The content blocks module.');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		// general settings
		$this->setSetting('content_blocks', 'max_num_revisions', 20);

		// module rights
		$this->setModuleRights(1, 'content_blocks');

		// action rights
		$this->setActionRights(1, 'content_blocks', 'add');
		$this->setActionRights(1, 'content_blocks', 'delete');
		$this->setActionRights(1, 'content_blocks', 'edit');
		$this->setActionRights(1, 'content_blocks', 'index');

		// set navigation
		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$this->setNavigation($navigationModulesId, 'ContentBlocks', 'content_blocks/index', array('content_blocks/add', 'content_blocks/edit'));
	}
}
