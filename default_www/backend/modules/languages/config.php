<?php

/**
 * Languages
 *
 * This is the configuration-object
 *
 * @package		backend
 * @subpackage	languages
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
final class LanguagesConfig extends BackendBaseConfig
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