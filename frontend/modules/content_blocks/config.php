<?php

/**
 * This is the configuration-object
 *
 * @package		frontend
 * @subpackage	content_blocks
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
final class FrontendContentBlocksConfig extends FrontendBaseConfig
{
	/**
	 * The default action
	 *
	 * @var	string
	 */
	protected $defaultAction = 'detail';


	/**
	 * The disabled actions
	 *
	 * @var	array
	 */
	protected $disabledActions = array();
}

?>