<?php

/**
 * This is the index-action
 *
 * @package		frontend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
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
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call the parent
		parent::execute();

		// hide contenTitle, in the template the title is wrapped with an inverse-option
		$this->tpl->assign('hideContentTitle', true);

		// load template
		$this->loadTemplate();

		// load the datagrid
		$this->loadDataGrid();

		// parse datagrid
		$this->parseDataGrid();

	}


	/**
	 * Load the datagrid
	 *
	 * @return	void
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
		$headers['name'] = ucfirst(FL::lbl('Name'));
		$headers['send_on'] = ucfirst(FL::lbl('Sent'));

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
	 *
	 * @return	void
	 */
	private function parseDataGrid()
	{
		// parse the datagrid in the template
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
	}
}

?>