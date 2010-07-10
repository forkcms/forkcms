<?php

/**
 * ExampleInstall
 * Installer for the example module
 *
 * @package		installer
 * @subpackage	example
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class ExampleInstall extends ModuleInstaller
{
	/**
	 * Install the module
	 *
	 * @return	void
	 */
	protected function execute()
	{
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