<?php

// require the Model-class
require_once BACKEND_MODULE_PATH .'/engine/model.php';

/**
 * TagsConfig
 *
 * This is the configuration-object for the Tags module
 *
 * @package		backend
 * @subpackage	tags
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
final class TagsConfig extends BackendBaseConfig
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