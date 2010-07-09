<?php

class AuthenticationInstall extends ModuleInstaller
{
	public function __construct(SpoonDatabase $db, array $languages)
	{
		// set database instance
		$this->db = $db;

		// add 'authentication' as a module
		$this->addModule('authentication', 'The module to manage authentication');
	}
}

?>