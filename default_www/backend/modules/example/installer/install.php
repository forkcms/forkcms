<?php

class ExampleInstall extends ModuleInstaller
{
	public function __construct(SpoonDatabase $db, array $languages)
	{
		// set database instance
		$this->db = $db;

		// add 'example' as a module
		$this->addModule('example', 'The example module, used as a reference.');

		// general settings
		$this->setSetting('example', 'requires_akismet', false);
		$this->setSetting('example', 'requires_google_maps', false);

		// module rights
		$this->setModuleRights(1, 'example');

		// action rights
		$this->setActionRights(1, 'example', 'index');
		$this->setActionRights(1, 'example', 'layout');
	}
}

?>