<?php

/**
 * This is the index-action (default), it will display the overview of location items
 *
 * @package		backend
 * @subpackage	location
 *
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.1
 */
class BackendLocationIndex extends BackendBaseActionIndex
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

		// load datagrid
		$this->loadDataGrid();

		// parse page
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Loads the datagrid
	 *
	 * @return	void
	 */
	private function loadDataGrid()
	{
		// create datagrid
		$this->datagrid = new BackendDataGridDB(BackendLocationModel::QRY_DATAGRID_BROWSE, array(BL::getWorkingLanguage()));

		// sorting columns
		$this->datagrid->setSortingColumns(array('address', 'title'), 'address');
		$this->datagrid->setSortParameter('ASC');

		// set colum URLs
		$this->datagrid->setColumnURL('title', BackendModel::createURLForAction('edit') . '&amp;id=[id]');

		// add edit column
		$this->datagrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::lbl('Edit'));
	}


	/**
	 * Parse the datagrid
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse the datagrid
		$this->tpl->assign('datagrid', ($this->datagrid->getNumResults() != 0) ? $this->datagrid->getContent() : false);

		// get settings
		$settings = BackendModel::getModuleSettings();

		// assign to template
		$this->tpl->assign('items', BackendLocationModel::getAll());
		$this->tpl->assign('settings', $settings['location']);
	}
}

?>