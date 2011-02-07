<?php

/**
 * This is the configuration-object
 *
 * @package		frontend
 * @subpackage	tags
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
final class FrontendTagsConfig extends FrontendBaseConfig
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