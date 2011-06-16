<?php

/**
 * This is the configuration-object for the groups module
 *
 * @package		backend
 * @subpackage	groups
 *
 * @author		Jeroen Van den Bossche <jeroenvandenbossche@netlash.com>
 * @since		2.0
 */
final class BackendGroupsConfig extends BackendBaseConfig
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
}

?>