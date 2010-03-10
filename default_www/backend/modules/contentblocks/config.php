<?php

/**
 * ContentblocksConfig
 *
 * This is the configuration-object for the contentblocks module
 *
 * @package		backend
 * @subpackage	contentblocks
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
final class BackendContentblocksConfig extends BackendBaseConfig
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