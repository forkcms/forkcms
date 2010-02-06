<?php
/**
 * language.php
 *
 * @package MCManager.stream
 * @author Moxiecode
 * @copyright Copyright © 2007, Moxiecode Systems AB, All rights reserved.
 */

error_reporting(E_ALL ^ E_NOTICE);

header("Content-type: text/javascript");

// Load MCManager core
require_once("../includes/general.php");
require_once("../classes/ManagerEngine.php");
require_once("../classes/Utils/Error.php");
require_once("../classes/Utils/JSON.php");

$MCErrorHandler = new Moxiecode_Error(false);
set_error_handler("JSErrorHandler");

$json = new Moxiecode_JSON();

// NOTE: Remove default value
$type = getRequestParam("type", "im");
$format = getRequestParam("format", false);
$prefix = getRequestParam("prefix", "");
$groupIDs = getRequestParam("groups", "");
$code = getRequestParam("code", "en");

if ($type == "")
	die("alert('No type set.');");

// Clean up type, only a-z stuff.
$type = preg_replace("/[^a-z]/i", "", $type);

// Include Base and Core and Config.
$man = new Moxiecode_ManagerEngine($type);

require_once(MCMANAGER_ABSPATH ."CorePlugin.php");
require_once("../config.php");

$man->dispatchEvent("onPreInit", array($type));

// Include all plugins
$pluginPaths = $man->getPluginPaths();

foreach ($pluginPaths as $path)
	require_once("../". $path);

// Dispatch auth event to make authenticators override config options
$man->isAuthenticated();

$langPack =& $man->getLangPack();
$groups =& $langPack->getGroups();

// TinyMCE specific format
if ($format == "tinymce") {
	echo "tinyMCE.addToLang('',{\n";

	$group = $groups['tinymce'];
	$keys = array_keys($group);

	for ($i=0; $i<count($keys); $i++) {
		echo $prefix . $keys[$i] . ":" . $json->encodeString($group[$keys[$i]]);

		if ($i != count($keys) - 1)
			echo ",";

		echo "\n";
	}

	echo "});";
} else if ($format == "tinymce_3_x") {
	echo "tinyMCE.addI18n('" . $langPack->getLanguage() . "',{\n";

	$group = $groups['tinymce'];
	$keys = array_keys($group);

	for ($i=0; $i<count($keys); $i++) {
		echo $prefix . $keys[$i] . ":" . $json->encodeString($group[$keys[$i]]);

		if ($i != count($keys) - 1)
			echo ",";

		echo "\n";
	}

	echo "});";
} else if ($format == "old") {
	// Normal MC manager format
	echo "mox.require(['mox.lang.LangPack'], function() {\n";

	foreach ($groups as $groupName => $group) {
		echo "mox.lang.LangPack.add('en', '" . $groupName . "', {\n";

		$keys = array_keys($group);

		for ($i=0; $i<count($keys); $i++) {
			echo $keys[$i] . ":" . $json->encodeString($group[$keys[$i]]);

			if ($i != count($keys) - 1)
				echo ",";

			echo "\n";
		}

		echo "});\n\n";
	}

	echo "\n});\n\n";

	echo "function translatePage() {";
	echo "if (mox && mox.lang && mox.lang.LangPack)";
	echo "mox.lang.LangPack.translatePage();";
	echo "}";
} else {
	$content = "";
	echo "var MCManagerI18n = {\n";

	$groupNames = $groupIDs ? explode(',', $groupIDs) : array_keys($groups);
	foreach ($groupNames as $group) {
		if (strlen($content) > 0)
			$content .= ',';

		$content .= "'" . $group . "':{\n";
		$group = $groups[$group];
		$keys = array_keys($group);

		for ($i=0; $i<count($keys); $i++) {
			$content .= ' ' . $keys[$i] . ":" . $json->encodeString($group[$keys[$i]]);

			if ($i != count($keys) - 1)
				$content .= ",";

			$content .= "\n";
		}

		$content .= "}";
	}

	echo $content . "};";
}

?>