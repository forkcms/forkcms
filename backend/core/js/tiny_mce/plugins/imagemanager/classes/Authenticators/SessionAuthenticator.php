<?php
/**
 * $Id: SessionAuthenticator.php 642 2009-01-19 13:49:06Z spocke $
 *
 * @package SessionAuthenticator
 * @author Moxiecode
 * @copyright Copyright © 2007, Moxiecode Systems AB, All rights reserved.
 */

@session_start();

/**
 * This class handles MCImageManager SessionAuthenticator stuff.
 *
 * @package SessionAuthenticator
 */
class Moxiecode_SessionAuthenticator extends Moxiecode_ManagerPlugin {
	/**#@+
	 * @access public
	 */

	/**
	 * SessionAuthenciator contructor.
	 */
	function SessionAuthenticator() {
	}

	/**
	 * Gets called on a authenication request. This method should check sessions or simmilar to
	 * verify that the user has access to the backend.
	 *
	 * This method should return true if the current request is authenicated or false if it's not.
	 *
	 * @param ManagerEngine $man ManagerEngine reference that the plugin is assigned to.
	 * @return bool true/false if the user is authenticated.
	 */
	function onAuthenticate(&$man) {
		$config =& $man->getConfig();

		// Support both old and new format
		$loggedInKey = isset($config['SessionAuthenticator.logged_in_key']) ? $config['SessionAuthenticator.logged_in_key'] : $config["authenticator.session.logged_in_key"];
		$userKey = isset($config['SessionAuthenticator.user_key']) ? $config['SessionAuthenticator.user_key'] : $config["authenticator.session.user_key"];
		$pathKey = isset($config['SessionAuthenticator.path_key']) ? $config['SessionAuthenticator.path_key'] : $config["authenticator.session.path_key"];
		$rootPathKey = isset($config['SessionAuthenticator.rootpath_key']) ? $config['SessionAuthenticator.rootpath_key'] : $config["authenticator.session.rootpath_key"];
		$configPrefix = (isset($config['SessionAuthenticator.config_prefix']) ? $config['SessionAuthenticator.config_prefix'] : "mcmanager") . ".";

		// Switch path
		if (isset($_SESSION[$pathKey]))
			$config['filesystem.path'] = $_SESSION[$pathKey];

		// Switch root
		if (isset($_SESSION[$rootPathKey]))
			$config['filesystem.rootpath'] = $_SESSION[$rootPathKey];

		$user = isset($_SESSION[$userKey]) ? $_SESSION[$userKey] : "";
		$user = preg_replace('/[\\\\\\/:]/i', '', $user);

		// Override by prefix
		foreach ($_SESSION as $key => $value) {
			if (strpos($key, $configPrefix) === 0)
				$config[substr($key, strlen($configPrefix))] = $value;
		}

		foreach ($config as $key => $value) {
			// Skip replaceing {$user} in true/false stuff
			if ($value === true || $value === false)
				continue;

			$value = str_replace('${user}', $user, $value);
			$config[$key] = $value;
		}

		// Force update of internal state
		$man->setConfig($config);

		return isset($_SESSION[$loggedInKey]) && checkBool($_SESSION[$loggedInKey]);
	}
}

// Add plugin to MCManager
$man->registerPlugin("SessionAuthenticator", new Moxiecode_SessionAuthenticator());
?>