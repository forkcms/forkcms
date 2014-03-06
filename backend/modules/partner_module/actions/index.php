<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the index-action (default), it will display the overview of partners
 *
 * @author Jelmer Prins <jelmer@sumocoders.be>
 */
class BackendPartnerModuleIndex extends BackendBaseActionIndex
{
	/**
	 * datagrid with partners
	 *
	 * @var	SpoonDataGrid
	 */
	private $dgPartners;


	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		$this->dgPartners = $this->loadDataGrid();

		$this->parse();
		$this->display();
	}

    /**
     * Loads the datagrid with the post
     *
     * @internal param string $published 'Y' or 'N'.
     * @return the datagrid
     */
	private function loadDataGrid()
	{
		// create datagrid
		$dg = new BackendDataGridDB(BackendPartnerModuleModel::QRY_DATAGRID_BROWSE);

		// set headers
		$dg->setHeaderLabels(array('user_id' => ucfirst(BL::lbl('Author'))));

		// sorting columns
		$dg->setSortingColumns(array('name', 'user_id'), 'name');
		$dg->setSortParameter('asc');

		// set colum URLs
		$dg->setColumnURL('name', BackendModel::createURLForAction('edit') . '&amp;id=[id]');

		// set column functions
		$dg->setColumnFunction(array('BackendDatagridFunctions', 'getUser'), array('[created_by]'), 'user_id', true);

		// add edit column
		$dg->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::lbl('Edit'));

		// add delete column
		$dg->addColumn('delete', null, BL::lbl('Delete'), BackendModel::createURLForAction('delete') . '&amp;id=[id]', BL::lbl('Delete'));

		return $dg;
	}

	/**
	 * Parse all datagrids
	 */
	protected function parse()
	{
		// parse the datagrid for all blogposts
		if($this->dgPartners->getNumResults() != 0) $this->tpl->assign('dgPartners', $this->dgPublished->getContent());
		if($this->dgPartners->getNumResults() == 0) $this->tpl->assign('noItems', 1);
	}
}
