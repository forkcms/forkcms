<?php

/**
 * AuthenticationInstall
 * Installer for the authentication module
 *
 * @package		installer
 * @subpackage	authentication
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class AuthenticationInstall extends ModuleInstaller
{
	/**
	 * Install the module
	 *
	 * @return	void
	 */
	protected function execute()
	{
		// add 'authentication' as a module
		$this->addModule('authentication', 'The module to manage authentication');
	}
}

?>