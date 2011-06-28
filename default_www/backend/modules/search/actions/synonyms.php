<?php

/**
 * This is the synonyms-action, it will display the overview of search synonyms
 *
 * @package		backend
 * @subpackage	search
 *
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
class BackendSearchSynonyms extends BackendBaseActionIndex
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

		// load datagrids
		$this->loadDataGrid();

		// parse page
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Loads the datagrids
	 *
	 * @return	void
	 */
	private function loadDataGrid()
	{
		// create datagrid
		$this->dataGrid = new BackendDataGridDB(BackendSearchModel::QRY_DATAGRID_BROWSE_SYNONYMS, BL::getWorkingLanguage());

		// sorting columns
		$this->dataGrid->setSortingColumns(array('term'), 'term');

		// column function
		$this->dataGrid->setColumnFunction('str_replace', array(',', ', ', '[synonym]'), 'synonym', true);

		// set colum URLs
		$this->dataGrid->setColumnURL('term', BackendModel::createURLForAction('edit_synonym') . '&amp;id=[id]');

		// add column
		$this->dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit_synonym') . '&amp;id=[id]', BL::lbl('Edit'));
	}


	/**
	 * Parse & display the page
	 *
	 * @return	void
	 */
	private function parse()
	{
		// assign the datagrid
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
	}
}

?>