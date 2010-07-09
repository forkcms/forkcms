<?php

class PagesInstall extends ModuleInstaller
{
	public function __construct(SpoonDatabase $db, array $languages)
	{
		// set database instance
		$this->db = $db;

		// load install.sql
		$this->importSQL(PATH_WWW .'/backend/modules/pages/installer/install.sql');

		// add 'pages' as a module
		$this->addModule('pages', 'The module to manage your pages and website structure.');


		// @todo aanpassen in de code dat template_max_blocks setting uit de module pages komt en niet uit de core settings.
		// template_max_blocks => dit is een pages setting vanaf nu.
	}
}

?>