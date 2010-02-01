<?php
/**
 * FrontendSnippetsConfig
 *
 * This is the configuration-object
 *
 * @package		frontend
 * @subpackage	snippets
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendSnippetsConfig extends FrontendBaseConfig
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