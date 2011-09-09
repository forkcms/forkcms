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
	 * Modules
	 *
	 * @var	array
	 */
	private $modules;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// ignore modules

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

		// sorting columns
		$this->dataGrid->setSortingColumns(array('name'));

		// @todo add version number (problem => stored in the info.xml)

		// set colum URLs
		$this->dataGrid->setColumnURL('name', BackendModel::createURLForAction('module_detail') . '&amp;module=[name]');

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
}

?>