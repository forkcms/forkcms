<?php

/**
 * This is the configuration-object for the tags module
 *
 * @package		backend
 * @subpackage	tags
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
final class BackendTagsConfig extends BackendBaseConfig
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