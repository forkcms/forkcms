<?php

/**
 * This is the index-action (default), it will display the users-overview
 *
 * @package		backend
 * @subpackage	users
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendUsersIndex extends BackendBaseActionIndex
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

		// load the datagrid
		$this->loadDatagrid();

		// parse the datagrid
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the datagrid.
	 *
	 * @return	void
	 */
	private function loadDatagrid()
	{
		// create datagrid with an overview of all active and undeleted users
		$this->datagrid = new BackendDataGridDB(BackendUsersModel::QRY_BROWSE, array('N'));

		// add column
		$this->datagrid->addColumn('nickname', ucfirst(BL::lbl('Nickname')), null, BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::lbl('Edit'));

		// show the user's nickname
		$this->datagrid->setColumnFunction(array('BackendUser', 'getSettingByUserId'), array('[id]', 'nickname'), 'nickname', false);

		// add edit column
		$this->datagrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]');
	}


	/**
	 * Parse the datagrid
	 *
	 * @return	void
	 */
	private function parse()
	{
		$this->tpl->assign('datagrid', ($this->datagrid->getNumResults() != 0) ? $this->datagrid->getContent() : false);
	}
}

?>