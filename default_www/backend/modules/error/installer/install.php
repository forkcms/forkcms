<?php

class ErrorInstall extends ModuleInstaller
{
	public function __construct(SpoonDatabase $db, array $languages)
	{
		// set database instance
		$this->db = $db;

		// add 'error' as a module
		$this->addModule('error', 'The error module, used for displaying errors.');
	}
}

?>