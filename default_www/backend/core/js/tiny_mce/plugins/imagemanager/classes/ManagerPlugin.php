<?php
/**
 * $Id: ManagerPlugin.php 663 2009-02-09 13:03:11Z spocke $
 *
 * @package ManagerEngine
 * @author Moxiecode
 * @copyright Copyright © 2007, Moxiecode Systems AB, All rights reserved.
 */

/**
 * Base class for all manager plugins. This class should be extended by all plugins, the default implementations of all these methods
 * return true or null.
 *
 * @package ManagerEngine
 */
class Moxiecode_ManagerPlugin {
	/**#@+
	 * @access public
	 */

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
		return true;
	}

	/**
	 * Gets executed before the ManagerEngine is initialized. This method should only be implemeneted
	 * by core plugins.
	 *
	 * @param ManagerEngine $man ManagerEngine reference that the plugin is assigned to.
	 * @param string $prefix Specified prefix, use this to resolve config and language pack location.
	 * @return bool true/false if the execution of the event chain should continue.
	 */
	function onPreInit(&$man, $prefix) {
		return true;
	}

	/**
	 * Gets called after any authenication is performed and verified. This method can be used
	 * to override config options or add custom filesystems.
	 *
	 * @param ManagerEngine $man ManagerEngine reference that the plugin is assigned to.
	 * @return bool true/false if the execution of the event chain should continue.
	 */
	function onInit(&$man) {
		return true;
	}

	/**
	 * Gets called when a user has logged in to the system. This event should be dispatched from the login page.
	 * These events is not fired internaly and should be fired/dispatched externally.
	 *
	 * @param ManagerEngine $man ManagerEngine reference that the plugin is assigned to.
	 * @return bool true/false if the execution of the event chain should continue.
	 */
	function onLogin(&$man) {
		return true;
	}

	/**
	 * Gets called when a user has logged out from the system. This event should be dispatched from the logout page.
	 * These events is not fired internaly and should be fired/dispatched externally.
	 *
	 * @param ManagerEngine $man ManagerEngine reference that the plugin is assigned to.
	 * @return bool true/false if the execution of the event chain should continue.
	 */
	function onLogout(&$man) {
		return true;
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
		return true;
	}

	/**
	 * Gets called before a RPC command is handled.
	 *
	 * @param ManagerEngine $man ManagerEngine reference that the plugin is assigned to.
	 * @param string $cmd RPC Command to be executed.
	 * @param object $input RPC input object data.
	 * @return bool true/false if the execution of the event chain should continue.
	 */
	function onBeforeRPC(&$man, $cmd, $input) {
		return null;
	}

	/**
	 * Gets executed when a RPC command is to be executed.
	 *
	 * @param ManagerEngine $man ManagerEngine reference that the plugin is assigned to.
	 * @param string $cmd RPC Command to be executed.
	 * @param object $input RPC input object data.
	 * @return object Result data from RPC call or null if it should be passed to the next handler in chain.
	 */
	function onRPC(&$man, $cmd, $input) {
		return null;
	}

	/**
	 * Gets called before data is streamed to client.
	 *
	 * @param ManagerEngine $man ManagerEngine reference that the plugin is assigned to.
	 * @param string $cmd Stream command that is to be performed.
	 * @param string $input Array of input arguments.
	 * @return bool true/false if the execution of the event chain should continue.
	 */
	function onBeforeStream(&$man, $cmd, $input) {
		return true;
	}

	/**
	 * Gets called when data is streamed to client. This method should setup
	 * HTTP headers, content type etc and simply send out the binary data to the client and the return false
	 * ones that is done.
	 *
	 * @param ManagerEngine $man ManagerEngine reference that the plugin is assigned to.
	 * @param string $cmd Stream command that is to be performed.
	 * @param string $input Array of input arguments.
	 * @return bool true/false if the execution of the event chain should continue.
	 */
	function onStream(&$man, $cmd, $input) {
		return true;
	}

	/**
	 * Gets called after data was streamed to client.
	 *
	 * @param ManagerEngine $man ManagerEngine reference that the plugin is assigned to.
	 * @param string $cmd Stream command that is to was performed.
	 * @param string $input Array of input arguments.
	 * @return bool true/false if the execution of the event chain should continue.
	 */
	function onAfterStream(&$man, $cmd, $input) {
		return true;
	}

	/**
	 * Gets called before data is streamed/uploaded from client.
	 *
	 * @param ManagerEngine $man ManagerEngine reference that the plugin is assigned to.
	 * @param string $cmd Upload command that is to be performed.
	 * @param string $input Array of input arguments.
	 * @return bool true/false if the execution of the event chain should continue.
	 */
	function onBeforeUpload(&$man, $cmd, $input) {
		return true;
	}

	/**
	 * Gets called when data is streamed/uploaded from client. This method should take care of
	 * any uploaded files and move them to the correct location.
	 *
	 * @param ManagerEngine $man ManagerEngine reference that the plugin is assigned to.
	 * @param string $cmd Upload command that is to be performed.
	 * @param string $input Array of input arguments.
	 * @return object Result object data or null if the event wasn't handled.
	 */
	function onUpload(&$man, $cmd, $input) {
		return null;
	}

	/**
	 * Gets called before data is streamed/uploaded from client.
	 *
	 * @param ManagerEngine $man ManagerEngine reference that the plugin is assigned to.
	 * @param string $cmd Upload command that is to was performed.
	 * @param string $input Array of input arguments.
	 * @return bool true/false if the execution of the event chain should continue.
	 */
	function onAfterUpload(&$man, $cmd, $input) {
		return true;
	}

	/**
	 * Gets called when custom data is to be added for a file custom data can for example be
	 * plugin specific name value items that should get added into a file listning.
	 *
	 * @param ManagerEngine $man ManagerEngine reference that the plugin is assigned to.
	 * @param BaseFile $file File reference to add custom info/data to.
	 * @param string $type Where is the info needed for example list or info.
	 * @param Array $custom Name/Value array to add custom items to.
	 * @return bool true/false if the execution of the event chain should continue.
	 */
	function onCustomInfo(&$man, &$file, $type, &$custom) {
		return true;
	}

	/**
	 * Gets called when the user selects a file and inserts it into TinyMCE or a form or similar.
	 *
	 * @param ManagerEngine $man ManagerEngine reference that the plugin is assigned to.
	 * @param BaseFile $file Implementation of the BaseFile class that was inserted/returned to external system.
	 * @return bool true/false if the execution of the event chain should continue.
	 */
	function onInsertFile(&$man, &$file) {
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
		return true;
	}

	/**#@-*/
}
?>