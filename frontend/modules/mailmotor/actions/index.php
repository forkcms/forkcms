<?php

/**
 * This is the index-action
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class FrontendMailmotorIndex extends FrontendBaseBlock
{
	const MAILINGS_PAGING_LIMIT = 10;

	/**
	 * The datagrid object
	 *
	 * @var	SpoonDataGrid
	 */
	private $datagrid;

	/**
	 * Execute the extra
	 */
	public function execute()
	{
		parent::execute();
		$this->tpl->assign('hideContentTitle', true);
		$this->loadTemplate();
		$this->loadDataGrid();
		$this->parseDataGrid();

	}

	/**
	 * Load the datagrid
	 */
	private function loadDataGrid()
	{
		// create a new source-object
		$source = new SpoonDataGridSourceDB(FrontendModel::getDB(), array(FrontendMailmotorModel::QRY_DATAGRID_BROWSE_SENT, array('sent', FRONTEND_LANGUAGE)));

		// create datagrid
		$this->dataGrid = new SpoonDataGrid($source);
		$this->dataGrid->setCompileDirectory(FRONTEND_CACHE_PATH . '/compiled_templates');

		// set hidden columns
		$this->dataGrid->setColumnsHidden(array('id', 'status'));

		// set headers values
		$headers['name'] = SpoonFilter::ucfirst(FL::lbl('Name'));
		$headers['send_on'] = SpoonFilter::ucfirst(FL::lbl('Sent'));

		// set headers
		$this->dataGrid->setHeaderLabels($headers);

		// sorting columns
		$this->dataGrid->setSortingColumns(array('name', 'send_on'), 'name');
		$this->dataGrid->setSortParameter('desc');

		// set colum URLs
		$this->dataGrid->setColumnURL('name', FrontendNavigation::getURLForBlock('mailmotor', 'detail') . '/[id]');

		// set column functions
		$this->dataGrid->setColumnFunction(array('SpoonDate', 'getTimeAgo'), array('[send_on]'), 'send_on', true);

		// add styles
		$this->dataGrid->setColumnAttributes('name', array('class' => 'title'));

		// set paging limit
		$this->dataGrid->setPagingLimit(self::MAILINGS_PAGING_LIMIT);
	}

	/**
	 * parse the datagrid
	 */
	private function parseDataGrid()
	{
		// parse the datagrid in the template
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
	}
}
