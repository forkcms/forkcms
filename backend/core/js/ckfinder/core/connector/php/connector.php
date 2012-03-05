<?php
/*
 * CKFinder
 * ========
 * http://ckfinder.com
 * Copyright (C) 2007-2011, CKSource - Frederico Knabben. All rights reserved.
 *
 * The software, this file and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying or distribute this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
 */

/**
 * Main heart of CKFinder - Connector
 *
 * @package CKFinder
 * @subpackage Connector
 * @copyright CKSource - Frederico Knabben
 */

/**
 * Protect against sending warnings to the browser.
 * Comment out this line during debugging.
 */
// error_reporting(0);

/**
 * Protect against sending content before all HTTP headers are sent (#186).
 */
ob_start();

/**
 * define required constants
 */
require_once "./constants.php";

// @ob_end_clean();
// header("Content-Encoding: none");

/**
 * we need this class in each call
 */
require_once CKFINDER_CONNECTOR_LIB_DIR . "/CommandHandler/CommandHandlerBase.php";
/**
 * singleton factory
 */
require_once CKFINDER_CONNECTOR_LIB_DIR . "/Core/Factory.php";
/**
 * utils class
 */
require_once CKFINDER_CONNECTOR_LIB_DIR . "/Utils/Misc.php";
/**
 * hooks class
 */
require_once CKFINDER_CONNECTOR_LIB_DIR . "/Core/Hooks.php";
/**
 * Simple function required by config.php - discover the server side path
 * to the directory relative to the "$baseUrl" attribute
 *
 * @package CKFinder
 * @subpackage Connector
 * @param string $baseUrl
 * @return string
 */
function resolveUrl($baseUrl) {
    $fileSystem =& CKFinder_Connector_Core_Factory::getInstance("Utils_FileSystem");
    return $fileSystem->getDocumentRootPath() . $baseUrl;
}

$utilsSecurity =& CKFinder_Connector_Core_Factory::getInstance("Utils_Security");
$utilsSecurity->getRidOfMagicQuotes();

/**
 * $config must be initialised
 */
$config = array();
$config['Hooks'] = array();
$config['Plugins'] = array();

/**
 * Fix cookies bug in Flash.
 */
if (!empty($_GET['command']) && $_GET['command'] == 'FileUpload' && !empty($_POST)) {
	foreach ($_POST as $key => $val) {
		if (strpos($key, "ckfcookie_") === 0)
			$_COOKIE[str_replace("ckfcookie_", "", $key)] = $val;
	}
}

/**
 * read config file
 */
require_once CKFINDER_CONNECTOR_CONFIG_FILE_PATH;

CKFinder_Connector_Core_Factory::initFactory();
$connector =& CKFinder_Connector_Core_Factory::getInstance("Core_Connector");

if(isset($_GET['command'])) {
    $connector->executeCommand($_GET['command']);
}
else {
    $connector->handleInvalidCommand();
}
