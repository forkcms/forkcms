<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the index-action (default), it will display the overview
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendEventsIndex extends BackendBaseActionIndex
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->loadDataGrid();
		$this->parse();
		$this->display();
	}

	/**
	 * Loads the datagrid
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
		$this->datagrid->setSortingColumns(array('starts_on', 'ends_on', 'publish_on', 'title', 'comments', 'subscriptions'), 'starts_on');
		$this->datagrid->setSortParameter('desc');

		// set colum URLs
		$this->datagrid->setColumnURL('title', BackendModel::createURLForAction('edit') . '&amp;id=[id]');

		// set column functions
		$this->datagrid->setColumnFunction(array('BackendDatagridFunctions', 'getLongDate'), array('[starts_on]'), 'starts_on', true);
		$this->datagrid->setColumnFunction(array('BackendDatagridFunctions', 'getLongDate'), array('[ends_on]'), 'ends_on', true);
		$this->datagrid->setColumnFunction(array('BackendDatagridFunctions', 'getLongDate'), array('[publish_on]'), 'publish_on', true);

		// add edit column
		$this->datagrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::lbl('Edit'));

		// our JS needs to know an id, so we can highlight it
		$this->datagrid->setRowAttributes(array('id' => 'row-[revision_id]'));
	}

	/**
	 * Parse all datagrids
	 */
	private function parse()
	{
		// parse the datagrid for the drafts
		$this->tpl->assign('datagrid', ($this->datagrid->getNumResults() != 0) ? $this->datagrid->getContent() : false);
	}
}
