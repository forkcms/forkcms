<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This page will display the overview of campaigns
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class BackendMailmotorCampaigns extends BackendBaseActionIndex
{
	const PAGING_LIMIT = 10;

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
	 * Loads the datagrid with the campaigns
	 */
	private function loadDataGrid()
	{
		// create datagrid
		$this->dataGrid = new BackendDataGridDB(BackendMailmotorModel::QRY_DATAGRID_BROWSE_CAMPAIGNS);

		// set headers values
		$headers['name'] = SpoonFilter::ucfirst(BL::lbl('Title'));
		$headers['created_on'] = SpoonFilter::ucfirst(BL::lbl('Created'));

		// set headers
		$this->dataGrid->setHeaderLabels($headers);

		// sorting columns
		$this->dataGrid->setSortingColumns(array('name', 'created_on'), 'name');
		$this->dataGrid->setSortParameter('desc');

		// add the multicheckbox column
		$this->dataGrid->addColumn('checkbox', '<span class="checkboxHolder"><input type="checkbox" name="toggleChecks" value="toggleChecks" /></span>', '<span><input type="checkbox" name="id[]" value="[id]" class="inputCheckbox" /></span>');
		$this->dataGrid->setColumnsSequence('checkbox');

		// add mass action dropdown
		$ddmMassAction = new SpoonFormDropdown('action', array('delete' => BL::lbl('Delete')), 'delete');
		$this->dataGrid->setMassAction($ddmMassAction);

		// set column functions
		$this->dataGrid->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), array('[created_on]'), 'created_on', true);

		// add statistics column
		$this->dataGrid->addColumn('statistics');
		$this->dataGrid->setColumnAttributes('statistics', array('class' => 'action actionStatistics', 'width' => '10%'));
		$this->dataGrid->setColumnFunction(array(__CLASS__, 'setStatisticsLink'), array('[id]'), 'statistics', true);

		// add edit column
		$this->dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit_campaign') . '&amp;id=[id]', BL::lbl('Edit'));

		// add styles
		$this->dataGrid->setColumnAttributes('name', array('class' => 'title'));

		// set paging limit
		$this->dataGrid->setPagingLimit(self::PAGING_LIMIT);

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('index'))
		{
			// set column URLs
			$this->dataGrid->setColumnURL('name', BackendModel::createURLForAction('index') . '&amp;campaign=[id]');
		}
	}

	/**
	 * Parse all datagrids
	 */
	protected function parse()
	{
		parent::parse();

		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
	}

	/**
	 * Sets a link to the campaign statistics if it contains sent mailings
	 *
	 * @param int $id The ID of the campaign.
	 * @return string
	 */
	public static function setStatisticsLink($id)
	{
		// build the link HTML
		$html = '<a href="' . BackendModel::createURLForAction('statistics_campaign') . '&amp;id=' . $id . '" class="button icon iconStats linkButton"><span>' . BL::lbl('Statistics') . '</span></a>';

		// check if this campaign has sent mailings
		$hasSentMailings = (BackendMailmotorModel::existsSentMailingsByCampaignID($id) > 0) ? true : false;

		// return the result
		return ($hasSentMailings) ? $html : '';
	}
}
