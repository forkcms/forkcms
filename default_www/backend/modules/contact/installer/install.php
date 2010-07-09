<?php

class ContactInstall extends ModuleInstaller
{
	public function __construct(SpoonDatabase $db, array $languages)
	{
		// set database instance
		$this->db = $db;

		// add 'contact' as a module
		$this->addModule('contact', 'The contact module.');

		// general settings
		$this->setSetting('contact', 'requires_akismet', false);
		$this->setSetting('contact', 'requires_google_maps', false);
	}
}

?>