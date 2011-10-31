<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the index-action (default), it will display the overview of mail_to_friend posts
 *
 * @author Jelmer Snoeck <jelmer@netlash.com>
 */
class BackendMailToFriendIndex extends BackendBaseActionIndex
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
	 * Load the dataGrid
	 */
	private function loadDataGrid()
	{
		// create the dataGrid
		$this->dataGrid = new BackendDataGridDB(BackendMailToFriendModel::QRY_BROWSE_MAILS, array(BL::getWorkingLanguage()));
		$this->dataGrid->addColumn('details', null, BL::lbl('Detail'), BackendModel::createURLForAction('detail') . '&amp;id=[id]', BL::lbl('Detail'));
		$this->dataGrid->setColumnFunction(array('BackendMailToFriendModel', 'getDataGridData'), array('[own]', 'name'), array('own'));
		$this->dataGrid->setColumnFunction(array('BackendMailToFriendModel', 'getDataGridData'), array('[friend]', 'name'), array('friend'));
		$this->dataGrid->setColumnFunction(array('BackendDatagridFunctions', 'getLongDate'), array('[send_on]'), array('send_on'));
		$this->dataGrid->setHeaderLabels(array('own' => ucfirst(BL::lbl('From')), 'friend' => ucfirst(BL::lbl('To'))));
	}

	/**
	 * Parsethe data
	 */
	protected function parse()
	{
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
	}
}
