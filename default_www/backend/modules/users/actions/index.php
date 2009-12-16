<?php

/**
 * UsersIndex
 *
 * This is the index-action (default), it will display the users-overview
 *
 * @package		backend
 * @subpackage	users
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class UsersIndex extends BackendBaseActionIndex
{
	/**
	 * Datagrid instance
	 *
	 * @var	BackendDataGridDB
	 */
	private $datagrid;


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
	 * Load the datagrids
	 *
	 * @return	void
	 */
	private function loadDatagrid()
	{
		// create datagrid with an overview of all active and undeleted users
		$this->datagrid = new BackendDataGridDB(BackendUsersModel::QRY_BROWSE, array('Y', 'N'));

		// hide id
		$this->datagrid->setColumnsHidden('id');

		// add edit column
		$this->datagrid->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit') .'&id=[id]');

		// set id on rows, we will need this for the highlighting
		$this->datagrid->setRowAttributes(array('id' => 'userid-[id]'));
	}


	/**
	 * Parse the datagrid and the reports
	 *
	 * @return	void
	 */
	private function parse()
	{
		$this->tpl->assign('datagrid', ($this->datagrid->getNumResults() != 0) ? $this->datagrid->getContent() : false);
	}
}

?>