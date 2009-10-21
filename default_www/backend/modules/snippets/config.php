<?php

// require the Model-class
require_once BACKEND_MODULE_PATH .'/engine/model.php';

/**
 * SnippetsConfig
 *
 * This is the configuration-object for the spotlight module
 *
 * @package		backend
 * @subpackage	snippets
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
final class SnippetsConfig extends BackendBaseConfig
{
	/**
	 * The default action
	 *
	 * @var	string
	 */
	protected $defaultAction = 'index';


	/**
	 * The disabled actions
	 *
	 * @var	array
	 */
	protected $disabledActions = array();


	/**
	 * The disabled AJAX-actions
	 *
	 * @var	array
	 */
	protected $disabledAJAXActions = array();
}

?>