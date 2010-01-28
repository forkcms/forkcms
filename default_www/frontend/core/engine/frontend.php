<?php

/**
 * Frontend
 *
 * This class defines the frontend, it is the core. Everything starts here.
 * We create all needed instances
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class Frontend
{
	/**
	 * Default constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// create url-object
		new FrontendURL();

		// create and set template reference
		new FrontendTemplate();

		// create and set page reference
		new FrontendPage();
	}
}

?>