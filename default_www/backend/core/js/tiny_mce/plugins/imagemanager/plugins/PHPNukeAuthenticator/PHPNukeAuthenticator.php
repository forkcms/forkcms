<?php
/**
 * PHPNukeAuthenticatorImpl.php
 *
 * @package MCFileManager.authenicators
 * @author Moxiecode
 * @copyright Copyright © 2005, Moxiecode Systems AB, All rights reserved.
 */

// Include PHPNuke logic
@session_destroy();
$mcOldCWD = getcwd();
chdir(MCMANAGER_ABSPATH . "../../../../../");
require_once("mainfile.php");
chdir($mcOldCWD);

/**
 * This class is a Drupal CMS authenticator implementation.
 *
 * @package MCImageManager.Authenticators
 */
class Moxiecode_PHPNukeAuthenticator extends Moxiecode_ManagerPlugin {
    /**#@+
	 * @access public
	 */

	/**
	 * Main constructor.
	 */
	function Moxiecode_PHPNukeAuthenticator() {
	}

	function onAuthenticate(&$man) {
		global $user;

		return is_user($user) == 1;
	}

	/**#@-*/
}

// Add plugin to MCManager
$man->registerPlugin("PHPNukeAuthenticator", new Moxiecode_PHPNukeAuthenticator());

?>