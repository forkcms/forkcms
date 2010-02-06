<?php
/**
 * WordpressAuthenticator.php
 *
 * @package WordpressAuthenticator
 * @author Moxiecode
 * @copyright Copyright © 2007, Moxiecode Systems AB, All rights reserved.
 */

$mcOldCWD = getcwd();
chdir(MCMANAGER_ABSPATH . "../../../../../wp-admin/");
require_once("admin.php");
chdir($mcOldCWD);

/**
 * This class is a Drupal CMS authenticator implementation.
 *
 * @package MCImageManager.Authenticators
 */
class Moxiecode_WordpressAuthenticator extends Moxiecode_ManagerPlugin {
    /**#@+
	 * @access public
	 */

	/**
	 * Main constructor.
	 */
	function Moxiecode_WordpressAuthenticator() {
	}

	function onAuthenticate(&$man) {
		return user_can_richedit();
	}

	/**#@-*/
}

// Add plugin to MCManager
$man->registerPlugin("WordpressAuthenticator", new Moxiecode_WordpressAuthenticator());

?>