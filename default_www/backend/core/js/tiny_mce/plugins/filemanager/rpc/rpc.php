<?php
/**
 * rpc.php
 *
 * @package MCManager.stream
 * @author Moxiecode
 * @copyright Copyright © 2007, Moxiecode Systems AB, All rights reserved.
 */

// Set RPC response headers
header('Content-Type: text/plain; charset=UTF-8');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Use installer
if (file_exists("../install"))
	die('{"result":null,"id":null,"error":{"errstr":"You need to run the installer or rename/remove the \\"install\\" directory.","errfile":"","errline":null,"errcontext":"","level":"FATAL"}}');

error_reporting(E_ALL ^ E_NOTICE);

@set_time_limit(5*60); // 5 minutes execution time

require_once("../classes/Utils/JSON.php");
require_once("../classes/Utils/Error.php");

$MCErrorHandler = new Moxiecode_Error(false);
set_error_handler("JSONErrorHandler");

require_once("../includes/general.php");
require_once("../classes/ManagerEngine.php");

$raw = "";

// Try param
if (isset($_POST["json_data"]))
	$raw = getRequestParam("json_data");

// Try globals array
if (!$raw && isset($_GLOBALS) && isset($_GLOBALS["HTTP_RAW_POST_DATA"]))
	$raw = $_GLOBALS["HTTP_RAW_POST_DATA"];

// Try globals variable
if (!$raw && isset($HTTP_RAW_POST_DATA))
	$raw = $HTTP_RAW_POST_DATA;

// Try stream
if (!$raw) {
	// OLD PHP
	if (!function_exists('file_get_contents')) {
		$fp = fopen("php://input", "r");
		if ($fp) {
			$raw = "";

			while (!feof($fp))
				$raw = fread($fp, 1024);

			fclose($fp);
		}
	} else
		$raw = "" . file_get_contents("php://input");
}

if ($raw == "")
	die('{"result":null,"id":null,"error":{"errstr":"Could not get raw post data.","errfile":"","errline":null,"errcontext":"","level":"FATAL"}}');

// Get JSON data
$json = new Moxiecode_JSON();
$input = $json->decode($raw);

// Parse prefix and method
$chunks = explode('.', $input['method']);

$type = $chunks[0];
$input['method'] = $chunks[1];

// Clean up type, only a-z stuff.
$type = preg_replace("/[^a-z]/i", "", $type);

if ($type == "")
	die('{"result":null,"id":null,"error":{"errstr":"No type set.","errfile":"","errline":null,"errcontext":"","level":"FATAL"}}');

// Include Base and Core and Config.
$man = new Moxiecode_ManagerEngine($type);

require_once(MCMANAGER_ABSPATH ."CorePlugin.php");
require_once("../config.php");

$man->dispatchEvent("onPreInit", array($type));
$mcConfig =& $man->getConfig();

// Include all plugins
$pluginPaths = $man->getPluginPaths();

foreach ($pluginPaths as $path)
	require_once("../". $path);

// Dispatch onInit event
if ($man->isAuthenticated()) {
	$man->dispatchEvent("onBeforeRPC", array($input['method'], $input['params'][0]));

	$result = new stdClass();
	$result->result = $man->executeEvent("onRPC", array($input['method'], $input['params'][0]));
	$result->id = $input['id'];
	$result->error = null;

	$data = $json->encode($result);

	//header('Content-Length: ' . strlen($data));
	die($data);
} else
	die('{"result":{"login_url":"' . addslashes($mcConfig["authenticator.login_page"]) . '"},"id":null,"error":{"errstr":"Access denied by authenicator.","errfile":"","errline":null,"errcontext":"","level":"AUTH"}}');
?>