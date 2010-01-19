<?php

// require model
require_once FRONTEND_CORE_PATH .'/engine/model.php';

/**
 * Fork
 *
 * This source file is part of Fork CMS.
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