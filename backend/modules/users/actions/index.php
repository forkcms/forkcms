<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the index-action (default), it will display the users-overview
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class BackendUsersIndex extends BackendBaseActionIndex
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
	 * Load the datagrid.
	 */
	private function loadDataGrid()
	{
		// create datagrid with an overview of all active and undeleted users
		$this->dataGrid = new BackendDataGridDB(BackendUsersModel::QRY_BROWSE, array('N'));

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('edit'))
		{
			// add column
			$this->dataGrid->addColumn('nickname', SpoonFilter::ucfirst(BL::lbl('Nickname')), null, BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::lbl('Edit'));

			// add edit column
			$this->dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]');
		}

		// show the user's nickname
		$this->dataGrid->setColumnFunction(array('BackendUser', 'getSettingByUserId'), array('[id]', 'nickname'), 'nickname', false);
	}

	/**
	 * Parse the datagrid
	 */
	protected function parse()
	{
		parent::parse();

		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
	}
}
