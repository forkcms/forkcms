<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
	<meta http-equiv="X-UA-Compatible" content="chrome=1" />

	<title>Installer - Fork CMS</title>
	<link rel="shortcut icon" href="../backend/favicon.ico" />
	<link rel="stylesheet" type="text/css" media="screen" href="../backend/core/layout/css/screen.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="layout/css/installer.css" />
	<!--[if IE 7]><link rel="stylesheet" type="text/css" media="screen" href="../backend/core/layout/css/conditionals/ie7.css" /><![endif]-->

	<script type="text/javascript" src="../frontend/core/js/jquery/jquery.js"></script>
	<script type="text/javascript" src="../backend/core/js/backend.js"></script>
	<script type="text/javascript" src="js/install.js"></script>
</head>
<body id="installer">

	<div id="installHolder">

		<h2>Install Fork CMS</h2>

		<form action="index.php" method="get" id="step1" class="forkForms submitWithLink">
			<div>
				<input type="hidden" name="step" value="2" />
				<div class="horizontal">

					<div class="{$requirementsStatusOK}">
						<p>Hooray! Your server meets the requirements to run Fork CMS.</p>
						<div class="buttonHolder">
							<input id="installerButton" class="button inputButton mainButton" type="submit" name="installer" value="Start installation" />
						</div>
					</div>


					<div class="{$requirementsStatusError}">
						<div class="formMessage errorMessage">
							<p>Your server doesn't meet the minimum requirements to run Fork CMS. <a href="#" class="toggleInformation">More information...</a></p>
						</div>
					</div>

					<div id="requirementsInformation" class="hidden">
						<h3>PHP version <span class="{$phpVersion}">{$phpVersionStatus}</span></h3>
						<p>We require at least PHP 5.2</p>

						<h3>PHP Extensions</h3>
						<h4>cURL: <span class="{$extensionCURL}">{$extensionCURLStatus}</span></h4>
						<p>
							cURL is a library that allows you to connect and communicate to many different type of servers. More information
							can be found on: <a href="http://php.net/curl">http://php.net/curl</a>.
						</p>

						<h4>SimpleXML: <span class="{$extensionSimpleXML}">{$extensionSimpleXMLStatus}</span></h4>
						<p>
							The SimpleXML extension provides a very simple and easily usable toolset to convert XML to an object that can be
							processed with normal property selectors and array iterators. More information can be found on:
							<a href="http://php.net/simplexml">http://php.net/simplexml</a>.
						</p>

						<h4>SPL: <span class="{$extensionSPL}">{$extensionSPLStatus}</span></h4>
						<p>
							SPL is a collection of interfaces and classes that are meant to solve standard problems. More information can be found
							on: <a href="http://php.net/SPL">http://php.net/SPL</a>.
						</p>

						<h4>PDO: <span class="{$extensionPDO}">{$extensionPDOStatus}</span></h4>
						<p>
							PDO provides a data-access abstraction layer, which means that, regardless of which database you're using, you use the
							same functions to issue queries and fetch data. More information can be found on: <a href="http://php.net/pdo">http://php.net/pdo</a>.
						</p>

						<h4>PDO MySQL driver: <span class="{$extensionPDOMySQL}">{$extensionPDOMySQLStatus}</span></h4>
						<p>
							PDO_MYSQL is a driver that implements the PHP Data Objects (PDO) interface to enable access from PHP to MySQL databases.
							More information can be found on: <a href="http://www.php.net/manual/en/ref.pdo-mysql.php">http://www.php.net/manual/en/ref.pdo-mysql.php</a>.
						</p>

						<h4>mb_string: <span class="{$extensionMBString}">{$extensionMBStringStatus}</span></h4>
						<p>
							mbstring provides multibyte specific string functions that help you deal with multibyte encodings in PHP. In addition to
							that, mbstring handles character encoding conversion between the possible encoding pairs. mbstring is designed to handle Unicode-based
							encodings. More information can be found on: <a href="http://php.net/mb_string">http://php.net/mb_string</a>.
						</p>

						<h4>iconv: <span class="{$extensionIconv}">{$extensionIconvStatus}</span></h4>
						<p>
							This module contains an interface to iconv character set conversion facility. With this module, you can turn a string
							represented by a local character set into the one represented by another character set, which may be the Unicode
							character set. More information can be found on: <a href="http://php.net/iconv">http://php.net/iconv</a>.
						</p>

						<h4>GD2: <span class="{$extensionGD2}">{$extensionGD2Status}</span></h4>
						<p>
							PHP is not limited to creating just HTML output. It can also be used to create and manipulate image files in a variety of
							different image formats. More information can be found on: <a href="http://php.net/gd">http://php.net/gd</a>.
						</p>

						<h3>PHP ini-settings</h3>
						<h4>Safe Mode: <span class="{$settingsSafeMode}">{$settingsSafeModeStatus}</span></h4>
						<p>
							<strong>As of PHP 5.3.0 Safe Mode is deprecated.</strong> For forward compability we highly recommend you to disable Safe Mode.
						</p>
						<h4>Open Basedir: <span class="{$settingsOpenBasedir}">{$settingsOpenBasedirStatus}</span></h4>
						<p>
							For forward compability we highly recommend you not to use open_basedir.
						</p>

						<h3>Required permissions and/or files</h3>
						<h4>{$PATH_WWW}/backend/cache/* <span class="{$fileSystemBackendCache}">{$fileSystemBackendCacheStatus}</span></h4>
						<p>In this location all files created by the backend will be stored. This location must be writable.</p>
						<h4>{$PATH_WWW}/backend/* <span class="{$fileSystemBackend}">{$fileSystemBackendStatus}</span></h4>
						<p>This location must be writable for the installer, afterwards this folder only needs to be readable.</p>
						<h4>{$PATH_WWW}/frontend/cache/* <span class="{$fileSystemFrontendCache}">{$fileSystemFrontendCacheStatus}</span></h4>
						<p>In this location all files created by the frontend will be stored. This location must be writable.</p>
						<h4>{$PATH_WWW}/frontend/files/* <span class="{$fileSystemFrontendFiles}">{$fileSystemFrontendFilesStatus}</span></h4>
						<p>In this location all files uploaded by the user/modules will be stored. This location must be writable.</p>
						<h4>{$PATH_WWW}/frontend/* <span class="{$fileSystemFrontend}">{$fileSystemFrontendStatus}</span></h4>
						<p>This location must be writable for the installer, afterwards this folder only needs to be readable.</p>
						<h4>{$PATH_LIBRARY} <span class="{$fileSystemLibrary}">{$fileSystemLibraryStatus}</span></h4>
						<p>This location must be writable for the installer, afterwards this folder only needs to be readable.</p>
						<h4>{$PATH_WWW}/install <span class="{$fileSystemInstaller}">{$fileSystemInstallerStatus}</span></h4>
						<p>This location must be writable for the installer.</p>
						<h4>{$PATH_LIBRARY}/config.example.php <span class="{$fileSystemConfig}">{$fileSystemConfigStatus}</span></h4>
						<p>This file is used to create the application config file.</p>
						<h4>{$PATH_LIBRARY}/globals.example.php <span class="{$fileSystemGlobals}">{$fileSystemGlobalsStatus}</span></h4>
						<p>This file is used to create the global configuration file.</p>
						<h4>{$PATH_LIBRARY}/globals_backend.example.php <span class="{$fileSystemGlobalsBackend}">{$fileSystemGlobalsBackendStatus}</span></h4>
						<p>This file is used to create the global backend configuration file.</p>
						<h4>{$PATH_LIBRARY}/globals_frontend.example.php <span class="{$fileSystemGlobalsFrontend}">{$fileSystemGlobalsFrontendStatus}</span></h4>
						<p>This file is used to create the global frontend configuration file.</p>
						<h4>{$PATH_LIBRARY} <span class="{$filesystemPathLibrary}">{$fileSystemPathLibraryStatus}</span></h4>
						<p>This directory is used to store your configuration files. The installer tries to find this directory automatically.</p>
					</div>
				</div>
			</div>
		</form>

	</div>

</body>
</html>