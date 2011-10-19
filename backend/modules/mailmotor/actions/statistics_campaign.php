<?php

/**
 * This page will display the statistical overview of all sent mailings in a specified campaign
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorStatisticsCampaign extends BackendBaseActionIndex
{
	// maximum number of items
	const PAGING_LIMIT = 10;


	/**
	 * The given campaign ID
	 *
	 * @var	int
	 */
	private $id;


	/**
	 * The campaign record
	 *
	 * @var	array
	 */
	private $campaign;


	/**
	 * The statistics record
	 *
	 * @var	array
	 */
	private $statistics;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// add highchart javascript
		$this->header->addJS('highcharts.js');

		// get the data
		$this->getData();

		// load datagrid
		$this->loadDataGrid();

		// parse page
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Gets all data needed for this page
	 *
	 * @return	void
	 */
	private function getData()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if(!BackendMailmotorModel::existsCampaign($this->id)) $this->redirect(BackendModel::createURLForAction('campaigns') . '&error=campaign-does-not-exist');

		// store mailing
		$this->campaign = BackendMailmotorModel::getCampaign($this->id);

		// fetch the statistics
		$this->statistics = BackendMailmotorCMHelper::getStatisticsByCampaignID($this->id, true);

		// no stats found
		if($this->statistics === false || empty($this->statistics)) $this->redirect(BackendModel::createURLForAction('campaigns') . '&error=no-statistics-loaded');
	}


	/**
	 * Loads the datagrid with the clicked link
	 *
	 * @return	void
	 */
	private function loadDataGrid()
	{
		// call the parent, as in create a new datagrid with the created source
		$this->dataGrid = new BackendDataGridDB(BackendMailmotorModel::QRY_DATAGRID_BROWSE_SENT_FOR_CAMPAIGN, array('sent', $this->id));
		$this->dataGrid->setColumnsHidden(array('campaign_id', 'campaign_name', 'status'));

		// set headers values
		$headers['sent'] = ucfirst(BL::lbl('Sent'));

		// set headers
		$this->dataGrid->setHeaderLabels($headers);

		// sorting columns
		$this->dataGrid->setSortingColumns(array('name', 'sent'), 'name');

		// set url for mailing name
		$this->dataGrid->setColumnURL('name', BackendModel::createURLForAction('statistics') . '&amp;id=[id]');

		// set column functions
		$this->dataGrid->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), array('[sent]'), 'sent', true);

		// set paging limit
		$this->dataGrid->setPagingLimit(self::PAGING_LIMIT);
	}


	/**
	 * Parse all datagrids
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse the datagrid
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);

		// parse the campaign record
		$this->tpl->assign('campaign', $this->campaign);

		// parse statistics
		$this->tpl->assign('stats', $this->statistics);
	}
}

?>