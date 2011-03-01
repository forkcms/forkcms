<?php

/**
 * This is the configuration-object
 *
 * @package		frontend
 * @subpackage	blog
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
final class FrontendBlogConfig extends FrontendBaseConfig
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