<?php

/**
 * This is the index-action (default), it will display the overview of mail_to_friend posts
 *
 * @package		backend
 * @subpackage	mail_to_friend
 *
 * @author		Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @since		2.6.10
 */
class BackendMailToFriendIndex extends BackendBaseActionIndex
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load the dataGrid
		$this->loadDataGrid();

		// parse
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the dataGrid
	 *
	 * @return	void
	 */
	private function loadDataGrid()
	{
		// create the dataGrid
		$this->dataGrid = new BackendDataGridDB(BackendMailToFriendModel::QRY_BROWSE_MAILS, array(BL::getWorkingLanguage()));

		// add edit column
		$this->dataGrid->addColumn('details', null, BL::lbl('Detail'), BackendModel::createURLForAction('detail') . '&amp;id=[id]', BL::lbl('Detail'));

		// column functions
		$this->dataGrid->setColumnFunction(array('BackendMailToFriendModel', 'getDataGridData'), array('[own]', 'name'), array('own'));
		$this->dataGrid->setColumnFunction(array('BackendMailToFriendModel', 'getDataGridData'), array('[friend]', 'name'), array('friend'));
		$this->dataGrid->setColumnFunction(array('BackendDatagridFunctions', 'getLongDate'), array('[send_on]'), array('send_on'));

		// set header labels
		$this->dataGrid->setHeaderLabels(array('own' => ucfirst(BL::lbl('From')), 'friend' => ucfirst(BL::lbl('To'))));
	}


	/**
	 * Parse
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// parse the dataGrid
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
	}
}
