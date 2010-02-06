<?php
	// Use installer
	if (file_exists("../install"))
		die("alert('You need to run the installer or rename/remove the \"install\" directory.');");

	error_reporting(E_ALL ^ E_NOTICE);

	require_once("../includes/general.php");
	require_once('../classes/Utils/JSCompressor.php');
	require_once("../classes/ManagerEngine.php");

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

	if ($package) {
		$compressor = new Moxiecode_JSCompressor(array(
			'expires_offset' => 3600 * 24 * 10,
			'disk_cache' => true,
			'cache_dir' => '_cache',
			'gzip_compress' => true,
			'remove_whitespace' => true,
			'charset' => 'UTF-8',
			'name' => $theme . "_" . $package
		));

		require_once('../classes/Utils/ClientResources.php');

		$resources = new Moxiecode_ClientResources();

		// Load theme resources
		$resources->load('../pages/' . $theme . '/resources.xml');

		// Load plugin resources
		$plugins = explode(',', $mcConfig["general.plugins"]);
		foreach ($plugins as $plugin)
			$resources->load('../plugins/' . $plugin . '/resources.xml');

		$man->dispatchEvent("onRequestResources", array($theme, $package, $type, "text/javascript", $resources));
	
		$files = $resources->getFiles($package);

		if ($resources->isDebugEnabled() || checkBool($mcConfig["general.debug"])) {
			header('Content-type: text/javascript');

			$pagePath = dirname($_SERVER['SCRIPT_NAME']);
			echo "// Debug enabled, scripts will be loaded without compression\n";
			echo "(function() {\n";
			echo "var h = '';\n";

			foreach ($files as $file)
				echo 'h += \'<script type="text/javascript" src="' . $pagePath . '/' . $file->getPath() . '"></script>\';' . "\n";

			echo "document.write(h);\n";
			echo "})();\n";
		} else {
			foreach ($files as $file)
				$compressor->addFile($file->getPath(), $file->isRemoveWhiteSpaceEnabled());

			$compressor->compress($package);
		}

		die;
	}
?>