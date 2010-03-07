<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package		spoon
 * @subpackage	http
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @author 		Tijs Verkoyen <tijs@spoon-library.be>
 * @author		Dave Lens <dave@spoon-library.be>
 * @since		0.1.1
 */


/** SpoonFilterExecption class */
require_once 'spoon/filter/filter.php';


/**
 * This exception is used to handle HTTP related exceptions.
 *
 * @package		spoon
 * @subpackage	http
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		0.1.1
 */
class SpoonHTTPException extends SpoonException {}

?>