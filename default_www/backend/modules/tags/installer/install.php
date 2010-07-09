<?php

class TagsInstall extends ModuleInstaller
{
	public function __construct(SpoonDatabase $db, array $languages)
	{
		// set database instance
		$this->db = $db;

		// load install.sql
		$this->importSQL(PATH_WWW .'/backend/modules/tags/installer/install.sql');

		// add 'blog' as a module
		$this->addModule('tags', 'The tags module.');

		// general settings
		$this->setSetting('blog', 'requires_akismet', false);
		$this->setSetting('blog', 'requires_google_maps', false);

		// module rights
		$this->setModuleRights(1, 'tags');

		// action rights
		$this->setActionRights(1, 'tags', 'autocomplete');
		$this->setActionRights(1, 'tags', 'edit');
		$this->setActionRights(1, 'tags', 'index');
		$this->setActionRights(1, 'tags', 'mass_action');
	}
}

?>