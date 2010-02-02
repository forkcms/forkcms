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

		// parse
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Parse
	 *
	 * @return	void
	 */
	private function parse()
	{
		// show report
		if($this->getParameter('reset') == 'success') $this->tpl->assign('resetSuccess', true);
	}
}
?>