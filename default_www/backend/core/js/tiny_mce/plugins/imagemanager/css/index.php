<?php
	require_once("../includes/general.php");
	require_once('../classes/Utils/ClientResources.php');
	require_once('../classes/Utils/CSSCompressor.php');
	require_once("../classes/ManagerEngine.php");

	// Set the error reporting to minimal
	@error_reporting(E_ERROR | E_WARNING | E_PARSE);

	$theme = getRequestParam("theme", "", true);
	$package = getRequestParam("package", "", true);
	$type = getRequestParam("type", "", true);

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

	$compressor = new Moxiecode_CSSCompressor(array(
		'expires_offset' => 3600 * 24 * 10,
		'disk_cache' => true,
		'cache_dir' => '_cache',
		'gzip_compress' => true,
		'remove_whitespace' => false,
		'charset' => 'UTF-8',
		'name' => $theme . "_" . $package,
		'convert_urls' => true
	));

	$resources = new Moxiecode_ClientResources();

	// Load theme resources
	$resources->load('../pages/' . $theme . '/resources.xml');

	// Load plugin resources
	$plugins = explode(',', $mcConfig["general.plugins"]);
	foreach ($plugins as $plugin)
		$resources->load('../plugins/' . $plugin . '/resources.xml');

	$man->dispatchEvent("onRequestResources", array($theme, $package, $type, "text/css", $resources));

	$files = $resources->getFiles($package);

	if ($resources->isDebugEnabled() || checkBool($mcConfig["general.debug"])) {
		header('Content-type: text/css');

		$pagePath = dirname($_SERVER['SCRIPT_NAME']);
		echo "/* Debug enabled, css files will be loaded without compression */\n";

		foreach ($files as $file)
			echo '@import url("' . $pagePath . '/' . $file->getPath() . '");' . "\n";
	} else {
		foreach ($files as $file)
			$compressor->addFile($file->getPath(), $file->isRemoveWhiteSpaceEnabled());

		$compressor->compress($package);
	}
?>