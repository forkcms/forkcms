<?php
/**
 * Template.php
 *
 * @package ManagerEngine
 * @author Moxiecode
 * @copyright Copyright © 2007, Moxiecode Systems AB, All rights reserved.
 * @ignore
 */

// Load CMS
define('MOOZCMS_REQUEST_TYPE', 'EXTERNAL');
require_once(dirname(__FILE__) . "/../../../../load.php");
require_once(MOOZCMS_ABSPATH . "/core/classes/FileActionEventArgs.php");

/**
 * This is an integration class for the MoozCMS.
 */
class Moxiecode_MoozCMSIntegration extends Moxiecode_ManagerPlugin {
	/**
	 * Gets called on a authenication request. This method should check sessions or simmilar to
	 * verify that the user has access to the backend.
	 *
	 * This method should return true if the current request is authenicated or false if it's not.
	 *
	 * @param ManagerEngine $man ManagerEngine reference that the plugin is assigned to.
	 * @return bool true/false if the user is authenticated.
	 */
	function onAuthenticate(&$man) {
		global $moozCMS;

		$config =& $man->getConfig();
		$moozCMSConfig = $moozCMS->getConfig();

		// Override with CMS options
		$prefix = $man->getType() == 'fm' ? 'filemanager' : 'imagemanager';
		foreach ($moozCMSConfig as $key => $value) {
			if (strpos($key, $prefix . '.') === 0)
				$config[preg_replace('/^' . $prefix . '\\./', '', $key)] = $value;
		}

		return $moozCMS->isAuthenticated();
	}

	/**
	 * Gets called before a file action occurs for example before a rename or copy.
	 *
	 * @param ManagerEngine $man ManagerEngine reference that the plugin is assigned to.
	 * @param int $action File action constant for example DELETE_ACTION.
	 * @param BaseFile $file1 File object 1 for example from in a copy operation.
	 * @param BaseFile $file2 File object 2 for example to in a copy operation. Might be null in for example a delete.
	 * @return bool true/false if the execution of the event chain should continue.
	 */
	function onBeforeFileAction(&$man, $action, $file1, $file2) {
		global $moozCMS;

		$moozCMS->fireEvent('BeforeFileAction', $this->_makeFileActionEventArgs($man, $action, $file1, $file2));

		return true;
	}

	/**
	 * Gets called after a file action was perforem for example after a rename or copy.
	 *
	 * @param ManagerEngine $man ManagerEngine reference that the plugin is assigned to.
	 * @param int $action File action constant for example DELETE_ACTION.
	 * @param BaseFile $file1 File object 1 for example from in a copy operation.
	 * @param BaseFile $file2 File object 2 for example to in a copy operation. Might be null in for example a delete.
	 * @return bool true/false if the execution of the event chain should continue.
	 */
	function onFileAction(&$man, $action, $file1, $file2) {
		global $moozCMS;

		$moozCMS->fireEvent('FileAction', $this->_makeFileActionEventArgs($man, $action, $file1, $file2));

		return true;
	}

	/**
	 * Gets called when resources are requested like JS or CSS files. This event enables a plugin to add resources dynamically.
	 *
	 * @param ManagerEngine $man ManagerEngine reference that the plugin is assigned to.
	 * @param string $theme Resource type CSS or JS.
	 * @param string $package Resource type CSS or JS.
	 * @param string $type Resource type CSS or JS.
	 * @param string $content_type Resource type CSS or JS.
	 * @param Moxiecode_ClientResources $resources Resources class that is used to handle client resources.
	 * @return bool true/false if the execution of the event chain should continue.
	 */
	function onRequestResources(&$man, $theme, $package, $type, $content_type, &$resources) {
		global $moozCMS;

		if ($type == "fm" || $type == "im")
			$resources->load('../plugins/MoozCMSIntegration/' . $type . '_' . $moozCMS->getSetting('general.theme') . '_resources.xml');
	}

	/**#@+ @access private */

	function &_makeFileActionEventArgs(&$man, $action, $file1, $file2) {
		switch ($action) {
			case DELETE_ACTION:
				$action = "delete";
				break;

			case ADD_ACTION:
				$action = "add";
				break;

			case UPDATE_ACTION:
				$action = "update";
				break;

			case RENAME_ACTION:
				$action = "rename";
				break;

			case COPY_ACTION:
				$action = "copy";
				break;

			case MKDIR_ACTION:
				$action = "mkdir";
				break;

			case RMDIR_ACTION:
				$action = "rmdir";
				break;

			default:
				$action = "unknown";
		}

		// Setup paths
		$path1 = $file1 ? $file1->getAbsolutePath() : null;
		$path2 = $file2 ? $file2->getAbsolutePath() : null;

		// Remove root path prefix from paths i.e. convert them to URIs
		$config = $man->getConfig();
		$root = $config['preview.wwwroot'];

		if ($path1 && strpos($path1, $root) === 0)
			$path1 = str_replace($root, '', $path1);

		if ($path2 && strpos($path2, $root) === 0)
			$path2 = str_replace($root, '', $path2);

		return new MoozCMS_FileActionEventArgs($action, $path1, $path2);
	}

	/**#@-*/
}

// Add plugin to ManagerEngine
$man->registerPlugin("MoozCMSIntegration", new Moxiecode_MoozCMSIntegration());

?>