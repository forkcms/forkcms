<?php

/**
 * BackendDashboardIndex
 *
 * This is the index-action (default), it will display the login screen
 *
 * @package		backend
 * @subpackage	dashboard
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendDashboardIndex extends BackendBaseActionIndex
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