<?php

/**
 * BlogIndex
 *
 * This is the index-action (default), it will display the login screen
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BlogIndex extends BackendBaseActionIndex
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// display the page
		$this->display();
	}
}
?>