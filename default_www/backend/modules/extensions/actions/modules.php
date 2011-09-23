<?php

/**
 * This is the index-action (default), it will display the overview of modules.
 *
 * @package		backend
 * @subpackage	extensions
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.1
 */
class BackendExtensionsModules extends BackendBaseActionIndex
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

		// load the data grid
		$this->loadDataGrid();

		// parse the data grid
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the data grid
	 *
	 * @return	void
	 */
	private function loadDataGrid()
	{
		// create datagrid
		$this->dataGrid = new BackendDataGridArray(BackendExtensionsModel::getModules());

		// header labels
		$this->dataGrid->setHeaderLabels(array('active' => ''));

		// sorting columns
		$this->dataGrid->setSortingColumns(array('name'));

		// order of columns
		$this->dataGrid->setColumnsSequence(array('name', 'description', 'version', 'active'));

		// set colum URLs
		$this->dataGrid->setColumnURL('name', BackendModel::createURLForAction('module_detail') . '&amp;module=[name]');

		// clean status message
		$this->dataGrid->setColumnFunction(array(__CLASS__, 'parseModuleStatus'), '[active]', 'active');

		// add edit column
		$this->dataGrid->addColumn('details', null, BL::lbl('Details'), BackendModel::createURLForAction('module_detail') . '&amp;module=[name]', BL::lbl('Details'));
	}


	/**
	 * Parse the datagrids and the reports
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse data grid
		$this->tpl->assign('dataGrid', $this->dataGrid->getContent());
	}


	/**
	 * Parse the status of a module in the datagrid.
	 *
	 * @return	string
	 * @param	string $status
	 */
	public static function parseModuleStatus($status)
	{
		if($status == 'Y') return BL::getLabel('Active');
		else return BL::getLabel('InActive');
	}
}

?>