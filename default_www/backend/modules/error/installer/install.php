<?php

/**
 * ErrorInstall
 * Installer for the error module
 *
 * @package		installer
 * @subpackage	error
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class ErrorInstall extends ModuleInstaller
{
	/**
	 * Install the module
	 *
	 * @return	void
	 */
	protected function execute()
	{
		// add 'error' as a module
		$this->addModule('error', 'The error module, used for displaying errors.');
	}
}

?>