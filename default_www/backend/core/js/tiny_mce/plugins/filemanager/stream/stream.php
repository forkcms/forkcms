<?php
/**
 * stream.php
 *
 * @package MCManager.stream
 * @author Moxiecode
 * @copyright Copyright © 2007, Moxiecode Systems AB, All rights reserved.
 */

// Use install
if (file_exists("../install"))
	die('{"result":null,"id":null,"error":{"errstr":"You need to run the installer or rename/remove the \\"install\\" directory.","errfile":"","errline":null,"errcontext":"","level":"FATAL"}}');

error_reporting(E_ALL ^ E_NOTICE);

require_once("../classes/Utils/JSON.php");
require_once("../classes/Utils/Error.php");

@set_time_limit(5*60); // 5 minutes execution time

$MCErrorHandler = new Moxiecode_Error(false);
set_error_handler("StreamErrorHandler");

require_once("../includes/general.php");
require_once("../classes/ManagerEngine.php");

$cmd = getRequestParam("cmd", "");
$theme = getRequestParam("theme", "", true);
$package = getRequestParam("package", "", true);
$type = getRequestParam("type", "", true);
$file = getRequestParam("file", "", true);

if ($package) {
	require_once('../classes/Utils/ClientResources.php');

	$resources = new Moxiecode_ClientResources();
	$resources->load('../pages/' . $theme . '/resources.xml');

	if ($type) {
		$man = new Moxiecode_ManagerEngine($type);

		require_once(MCMANAGER_ABSPATH ."CorePlugin.php");
		require_once("../config.php");

		$man->dispatchEvent("onPreInit", array($type));
		$mcConfig = $man->getConfig();

		// Load plugin resources
		$plugins = explode(',', $mcConfig["general.plugins"]);
		foreach ($plugins as $plugin)
			$resources->load('../plugins/' . $plugin . '/resources.xml');
	}

	$file = $resources->getFile($package, $file);

	header('Content-type: ' . $file->getContentType());
	readfile($file->getPath());

	die();
}

if ($cmd == "")
	die("No command.");

$chunks = explode('.', $cmd);

$type = $chunks[0];
$method = $cmd = $chunks[1];

// Clean up type, only a-z stuff.
$type = preg_replace("/[^a-z]/i", "", $type);

if ($type == "")
	die("No type set.");

// Include Base and Core and Config.
$man = new Moxiecode_ManagerEngine($type);

require_once(MCMANAGER_ABSPATH ."CorePlugin.php");
require_once("../config.php");

$man->dispatchEvent("onPreInit", array($type));
$mcConfig = $man->getConfig();

// Include all plugins
$pluginPaths = $man->getPluginPaths();

foreach ($pluginPaths as $path)
	require_once("../". $path);

// Dispatch onAuthenticate event
if ($man->isAuthenticated()) {
	if ($_SERVER["REQUEST_METHOD"] == "GET") {
		$args = $_GET;

		// Dispatch event before starting to stream
		$man->dispatchEvent("onBeforeStream", array($cmd, &$args));

		// Check command, do command, stream file.
		$man->dispatchEvent("onStream", array($cmd, &$args));

		// Dispatch event after stream
		$man->dispatchEvent("onAfterStream", array($cmd, &$args));
	} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$args = array_merge($_POST, $_GET);
		$json = new Moxiecode_JSON();

		// Dispatch event before starting to stream
		$man->dispatchEvent("onBeforeUpload", array($cmd, &$args));

		// Check command, do command, stream file.
		$result = $man->executeEvent("onUpload", array($cmd, &$args));
		$data = $result->toArray();

		if (isset($args["chunk"])) {
			// Output JSON response to multiuploader
			die('{method:\'' . $method . '\',result:' . $json->encode($data) . ',error:null,id:\'m0\'}');
		} else {
			// Output JSON function
			echo '<html><body><script type="text/javascript">';

			if (isset($args["domain"]) && $args["domain"])
				echo 'document.domain="' . $args["domain"] . '";';

			echo 'parent.handleJSON({method:\'' . $method . '\',result:' . $json->encode($data) . ',error:null,id:\'m0\'});</script></body></html>';
		}

		// Dispatch event after stream
		$man->dispatchEvent("onAfterUpload", array($cmd, &$args));
	}
} else {
	if (isset($_GET["format"]) && ($_GET["format"] == "flash"))
		header("HTTP/1.1 405 Method Not Allowed");

	die('{"result":{login_url:"' . addslashes($mcConfig["authenticator.login_page"]) . '"},"id":null,"error":{"errstr":"Access denied by authenicator.","errfile":"","errline":null,"errcontext":"","level":"AUTH"}}');
}
?>