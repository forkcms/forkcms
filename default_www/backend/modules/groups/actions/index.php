<?php

/**
 * This is the index-action (default), it will display the groups-overview
 *
 * @package		backend
 * @subpackage	groups
 * @actiongroup	overview	This is the overview action.
 *
 * @author		Jeroen Van den Bossche <jeroenvandenbossche@netlash.com>
 * @since		2.0
 */
class BackendGroupsIndex extends BackendBaseActionIndex
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
		$this->loadDataGrid();

		// parse the datagrid
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the datagrid
	 *
	 * @return	void
	 */
	public function loadDataGrid()
	{
		// create datagrid with overview of groups
		$this->dataGrid = new BackendDataGridDB(BackendGroupsModel::QRY_BROWSE);

		// set collumn URLs
		$this->dataGrid->setColumnURL('name', BackendModel::createURLForAction('edit') . '&amp;id=[id]');
		$this->dataGrid->setColumnURL('num_users', BackendModel::createURLForAction('edit') . '&amp;id=[id]#tabUsers');

		// add edit column
		$this->dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]');
	}


	/**
	 * Parse the datagrid
	 *
	 * @return	void
	 */
	public function parse()
	{
		// assign the datagrid
		$this->tpl->assign('dataGrid', $this->dataGrid->getContent());
	}
}

?>