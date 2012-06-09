<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This page will display the statistical overview of all sent mailings in a specified campaign
 *
 * @author Dave Lens <dave.lens@netlash.com>
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
	 */
	public function execute()
	{
		parent::execute();
		$this->header->addJS('highcharts.js', 'core', false);
		$this->getData();
		$this->loadDataGrid();
		$this->parse();
		$this->display();
	}

	/**
	 * Gets all data needed for this page
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
	 */
	private function loadDataGrid()
	{
		// call the parent, as in create a new datagrid with the created source
		$this->dataGrid = new BackendDataGridDB(BackendMailmotorModel::QRY_DATAGRID_BROWSE_SENT_FOR_CAMPAIGN, array('sent', $this->id));
		$this->dataGrid->setColumnsHidden(array('campaign_id', 'campaign_name', 'status'));
		$this->dataGrid->setURL(BackendModel::createURLForAction() . '&offset=[offset]&order=[order]&sort=[sort]&id=' . $this->id);

		// set headers values
		$headers['sent'] = SpoonFilter::ucfirst(BL::lbl('Sent'));

		// set headers
		$this->dataGrid->setHeaderLabels($headers);

		// sorting columns
		$this->dataGrid->setSortingColumns(array('name', 'sent'), 'name');

		// set column functions
		$this->dataGrid->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), array('[sent]'), 'sent', true);

		// set paging limit
		$this->dataGrid->setPagingLimit(self::PAGING_LIMIT);

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('statistics'))
		{
			// set url for mailing name
			$this->dataGrid->setColumnURL('name', BackendModel::createURLForAction('statistics') . '&amp;id=[id]');
		}
	}

	/**
	 * Parse all datagrids
	 */
	protected function parse()
	{
		parent::parse();

		// parse the datagrid
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);

		// parse the campaign record
		$this->tpl->assign('campaign', $this->campaign);

		// parse statistics
		$this->tpl->assign('stats', $this->statistics);
	}
}
