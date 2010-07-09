<?php

class ContentBlocksInstall extends ModuleInstaller
{
	public function __construct(SpoonDatabase $db, array $languages)
	{
		// set database instance
		$this->db = $db;

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
	}
}

?>