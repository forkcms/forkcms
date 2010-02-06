<?php
/**
 * CustomInfoExample.php
 *
 * @package CustomInfoExample
 * @author Moxiecode
 * @copyright Copyright © 2007, Moxiecode Systems AB, All rights reserved.
 */

/**
 * This class handles displays how to return custom data back to TinyMCE and file listings.
 */
class Moxiecode_CustomInfoExample extends Moxiecode_ManagerPlugin {
	/**
	 * ..
	 */
	function Moxiecode_CustomInfoExample() {
	}

	/**
	 * Gets called when custom data is to be added for a file custom data can for example be
	 * plugin specific name value items that should get added into a file listning.
	 *
	 * @param MCManager $man MCManager reference that the plugin is assigned to.
	 * @param BaseFile $file File reference to add custom info/data to.
	 * @param string $type Where is the info needed for example list or info.
	 * @param Array $custom Name/Value array to add custom items to.
	 * @return bool true/false if the execution of the event chain should continue.
	 */
	function onCustomInfo(&$man, &$file, $type, &$input) {
		switch ($type) {
			// When the file is selected/inserted
			case "insert":
				// Can be used by the insert_templates like this {$custom.mycustomfield}
				$input['mycustomfield'] = strtoupper($file->getName());

				// Will be used as title/alt in TinyMCE link/image dialogs
				$input['description'] = $file->getName() . " (" . $this->_getSizeStr($file->getLength()) . ")";
				break;

			// When the file is displayed in a more info dialog
			case "info":
				//$input['mycustomfield'] = strtoupper($file->getName());
				break;

			// When the file is listed
			case "list":
				//$input['mycustomfield'] = strtoupper($file->getName());
				break;
		}

		// Chain to next
		return true;
	}

	/**
	 * Returns a filesize as a nice truncated string like "10.3 MB".
	 *
	 * @param int $size File size to convert.
	 * @return String Nice truncated string of the file size.
	 */
	function _getSizeStr($size) {
		// MB
		if ($size > 1048576)
			return round($size / 1048576, 1) . "MB";

		// KB
		if ($size > 1024)
			return round($size / 1024, 1) . "KB";

		return trim($size) . "b";
	}
}

// Add plugin to MCManager
$man->registerPlugin("custominfoexample", new Moxiecode_CustomInfoExample());
?>