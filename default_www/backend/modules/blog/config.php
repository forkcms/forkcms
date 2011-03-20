<?php

/**
 * This is the configuration-object for the blog module
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
final class BackendBlogConfig extends BackendBaseConfig
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