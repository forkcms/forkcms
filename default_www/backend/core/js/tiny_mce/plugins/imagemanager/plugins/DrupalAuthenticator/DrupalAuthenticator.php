<?php
/**
 * DrupalAuthenticatorImpl.php
 *
 * @package MCImageManager.authenicators
 * @author Moxiecode
 * @copyright Copyright © 2005, Moxiecode Systems AB, All rights reserved.
 */

// Store away important references
$access_check = array(
	"manager" => $man,
	"basepath" => MCMANAGER_ABSPATH,
	"json" => $json,
	"input" => $input,
	"cmd" => $cmd,
	"cwd" => getcwd(),
	"MCErrorHandler" => $MCErrorHandler
);

define('MC_TYPE', $man->getType());

// Bootstap drupal
@session_destroy();
chdir(MCMANAGER_ABSPATH . "../../../../../");
require_once("includes/bootstrap.inc");

global $base_url, $base_root, $base_path;

// Setup base_root, base_url and base_path so the sessions will work correctly
$base_root = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
$base_url = $base_root .= '://'. preg_replace('/[^a-z0-9-:._]/i', '', $_SERVER['HTTP_HOST']);
$base_path = '/' . trim(dirname($_SERVER['SCRIPT_NAME']), '\,/');
$base_path = substr($base_path, 0, strpos($base_path, '/sites/all/modules/'));
$base_url .= $base_path;

drupal_bootstrap(DRUPAL_BOOTSTRAP_SESSION);
$isDrupalAuth = false;

if (!isset($_SESSION['mc_drupal_auth']) || !$_SESSION['mc_drupal_auth']) {
	// Not cached in session check agains API
	drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

	if (MC_TYPE == "fm")
		$type = "filemanager";
	else
		$type = "imagemanager";

	$isDrupalAuth = user_access('access tinymce ' . $type);
	$_SESSION['mc_drupal_auth'] = $isDrupalAuth;
} else
	$isDrupalAuth = $_SESSION['mc_drupal_auth'];

// Restore everything
chdir($access_check['cwd']);
$MCErrorHandler = $access_check['MCErrorHandler'];
$man = $access_check['manager'];
$json = $access_check['json'];
$cmd = $access_check['cmd'];
$input = $access_check['input'];

/**
 * This class is a Drupal CMS authenticator implementation.
 *
 * @package MCImageManager.Authenticators
 */
class Moxiecode_DrupalAuthenticator extends Moxiecode_ManagerPlugin {
    /**#@+
	 * @access public
	 */

	/**
	 * Main constructor.
	 */
	function Moxiecode_DrupalAuthenticator() {
	}

	function onAuthenticate(&$man) {
		global $isDrupalAuth;
		global $user;

        $config =& $man->getConfig(); 

		// If authenticated then
		if ($isDrupalAuth && isset($user)) {
			foreach ($config as $key => $value) {
				if (is_string($value)) {
					$value = str_replace('${user}', $user->uid, $value);
					$config[$key] = $value;
				}
			}

			// Get rootpath
			$rootPath = $man->toAbsPath($config['filesystem.rootpath']);

			// Create rootpath
			if (!file_exists($rootPath))
				mkdir($rootPath);
		}

        return $isDrupalAuth;
	}

	/**#@-*/
}

// Add plugin to MCManager
$man->registerPlugin("DrupalAuthenticator", new Moxiecode_DrupalAuthenticator());

?>