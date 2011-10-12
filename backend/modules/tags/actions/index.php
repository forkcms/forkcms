<?php

/**
 * This is the index-action, it will display the overview of tags
 *
 * @package		backend
 * @subpackage	tags
 *
 * @author		Dave Lens <dave@netlash.com>
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class BackendTagsIndex extends BackendBaseActionIndex
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

		// load datagrid
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
		$this->dataGrid = new BackendDataGridDB(BackendTagsModel::QRY_DATAGRID_BROWSE, BL::getWorkingLanguage());

		// header labels
		$this->dataGrid->setHeaderLabels(array('tag' => ucfirst(BL::lbl('Name')), 'num_tags' => ucfirst(BL::lbl('Amount'))));

		// sorting columns
		$this->dataGrid->setSortingColumns(array('tag', 'num_tags'), 'num_tags');
		$this->dataGrid->setSortParameter('desc');

		// add the multicheckbox column
		$this->dataGrid->setMassActionCheckboxes('checkbox', '[id]');

		// add mass action dropdown
		$ddmMassAction = new SpoonFormDropdown('action', array('delete' => BL::lbl('Delete')), 'delete');
		$ddmMassAction->setOptionAttributes('delete', array('message-id' => 'confirmDelete'));
		$this->dataGrid->setMassAction($ddmMassAction);

		// add column
		$this->dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::lbl('Edit'));

		// add attributes, so the inline editing has all the needed data
		$this->dataGrid->setColumnAttributes('tag', array('data-id' => '{id:[id]}'));
	}


	/**
	 * Parse & display the page
	 *
	 * @return	void
	 */
	private function parse()
	{
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
	}
}

?>