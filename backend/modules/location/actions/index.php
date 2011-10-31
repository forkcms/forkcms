<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the index-action (default), it will display the overview of location items
 *
 * @author Matthias Mullie <matthias@mullie.eu>
 */
class BackendLocationIndex extends BackendBaseActionIndex
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
	private function loadDataGrid()
	{
		$this->dataGrid = new BackendDataGridDB(BackendLocationModel::QRY_DATAGRID_BROWSE, array(BL::getWorkingLanguage()));
		$this->dataGrid->setSortingColumns(array('address', 'title'), 'address');
		$this->dataGrid->setSortParameter('ASC');
		$this->dataGrid->setColumnURL('title', BackendModel::createURLForAction('edit') . '&amp;id=[id]');
		$this->dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::lbl('Edit'));
	}

	/**
	 * Parse the datagrid
	 */
	private function parse()
	{
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);

		// get settings
		$settings = BackendModel::getModuleSettings();

		// assign to template
		$this->tpl->assign('items', BackendLocationModel::getAll());
		$this->tpl->assign('settings', $settings['location']);
	}
}
