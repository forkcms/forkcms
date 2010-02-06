<?php
/**
 * JoomlaAuthenticatorImpl.php
 *
 * @package MCImageManager.authenicators
 * @author Moxiecode
 * @copyright Copyright © 2005-2006, Moxiecode Systems AB, All rights reserved.
 */

class Moxiecode_FixGlobals {
	static public $_globals;

	function store() {
		Moxiecode_FixGlobals::$_globals = array();

		foreach ($GLOBALS as $key => $value) {
			if ($key != 'GLOBALS')
				Moxiecode_FixGlobals::$_globals[$key] = $value;
		}
	}

	function restore() {
		foreach (Moxiecode_FixGlobals::$_globals as $key => $value)
			$GLOBALS[$key] = $value;
	}
}

Moxiecode_FixGlobals::store();

// Include Joomla bootstrap logic
@session_destroy();
define('mcOldCWD', getcwd());
chdir(MCMANAGER_ABSPATH . "../../../../../../../../administrator");

define('_JEXEC', 1);
define('JPATH_BASE', getcwd());
define('DS', DIRECTORY_SEPARATOR);

require_once(JPATH_BASE .DS.'includes'.DS.'defines.php');
require_once(JPATH_BASE .DS.'includes'.DS.'framework.php');

$mainframe =& JFactory::getApplication('administrator');
$mainframe->initialise(array(
	'language' => $mainframe->getUserState( "application.lang", 'lang' )
));

$mamboUser = $mainframe->getUser();

chdir(mcOldCWD);

Moxiecode_FixGlobals::restore();

/**
 * This class is a Drupal CMS authenticator implementation.
 *
 * @package MCImageManager.Authenticators
 */
class Moxiecode_JoomlaAuthenticator extends Moxiecode_ManagerPlugin {
    /**#@+
	 * @access public
	 */

	/**
	 * Main constructor.
	 */
	function Moxiecode_JoomlaAuthenticator() {
	}

	function onAuthenticate(&$man) {
		global $mamboUser;

		$config =& $man->getConfig();

		// Not logged in
		if ($mamboUser->id == 0)
			return false;

		// Replace ${user} in all config values
		foreach ($config as $key => $value) {
			// Skip replaceing {$user} in true/false stuff
			if ($value === true || $value === false)
				continue;

			$value = str_replace('${user}', $mamboUser->username, $value);
			$config[$key] = $value;
		}

		// Try create rootpath
		$rootPath = $man->toAbsPath($config['filesystem.rootpath']);
		$rootPathItems = explode(';', $rootPath);
		$rootPathItems = explode('=', $rootPathItems[0]);

		if (count($rootPathItems) > 1)
			$rootPath = $rootPathItems[1];
		else
			$rootPath = $rootPathItems[0];

		if (!file_exists($rootPath))
			@mkdir($rootPath);

		if (!isset($config['JoomlaAuthenticator.valid_types']) && !$config['JoomlaAuthenticator.valid_types'])
			$config['JoomlaAuthenticator.valid_types'] = "/administrator/i";

		// Is one of the valid user names
		return preg_match($config['JoomlaAuthenticator.valid_types'], $mamboUser->usertype);
	}

	/**#@-*/
}

// Add plugin to MCManager
$man->registerPlugin("JoomlaAuthenticator", new Moxiecode_JoomlaAuthenticator());

?>