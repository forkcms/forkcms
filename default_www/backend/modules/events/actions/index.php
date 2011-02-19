<?php

/**
 * This is the index-action (default), it will display the overview
 *
 * @package		backend
 * @subpackage	events
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class BackendEventsIndex extends BackendBaseActionIndex
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
	private function loadDatagrid()
	{
		// create datagrid
		$this->datagrid = new BackendDataGridDB(BackendEventsModel::QRY_DATAGRID_BROWSE, array('active', BL::getWorkingLanguage()));

		// set headers
		$this->datagrid->setHeaderLabels(array('publish_on' => ucfirst(BL::lbl('PublishedOn'))));

		// hide columns
		$this->datagrid->setColumnsHidden(array('revision_id'));

		// sorting columns
		$this->datagrid->setSortingColumns(array('starts_on', 'ends_on', 'publish_on', 'title', 'comments'), 'starts_on');
		$this->datagrid->setSortParameter('desc');

		// set colum URLs
		$this->datagrid->setColumnURL('title', BackendModel::createURLForAction('edit') .'&amp;id=[id]');

		// set column functions
		$this->datagrid->setColumnFunction(array('BackendDatagridFunctions', 'getLongDate'), array('[starts_on]'), 'starts_on', true);
		$this->datagrid->setColumnFunction(array('BackendDatagridFunctions', 'getLongDate'), array('[ends_on]'), 'ends_on', true);
		$this->datagrid->setColumnFunction(array('BackendDatagridFunctions', 'getLongDate'), array('[publish_on]'), 'publish_on', true);

		// add edit column
		$this->datagrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') .'&amp;id=[id]', BL::lbl('Edit'));

		// our JS needs to know an id, so we can highlight it
		$this->datagrid->setRowAttributes(array('id' => 'row-[revision_id]'));
	}


	/**
	 * Parse all datagrids
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse the datagrid for the drafts
		$this->tpl->assign('datagrid', ($this->datagrid->getNumResults() != 0) ? $this->datagrid->getContent() : false);
	}
}

?>