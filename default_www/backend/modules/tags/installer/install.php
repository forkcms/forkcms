<?php

/**
 * TagsInstall
 * Installer for the tags module
 *
 * @package		installer
 * @subpackage	tags
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class TagsInstall extends ModuleInstaller
{
	/**
	 * Install the module
	 *
	 * @return	void
	 */
	protected function execute()
	{
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