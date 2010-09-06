<?php

/**
 * BackendTagsIndex
 * This is the index-action, it will display the overview of tags
 *
 * @package		backend
 * @subpackage	tags
 *
 * @author 		Dave Lens <dave@netlash.com>
 * @author		Davy Hellemans <davy@netlash.com>
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
	 * @return void
	 */
	private function loadDataGrid()
	{
		// create datagrid
		$this->datagrid = new BackendDataGridDB(BackendTagsModel::QRY_DATAGRID_BROWSE, BL::getWorkingLanguage());

		// header labels
		$this->datagrid->setHeaderLabels(array('tag' => ucfirst(BL::getLabel('Name')), 'num_tags' => ucfirst(BL::getLabel('Amount'))));

		// sorting columns
		$this->datagrid->setSortingColumns(array('tag', 'num_tags'), 'num_tags');
		$this->datagrid->setSortParameter('desc');

		// add the multicheckbox column
		$this->datagrid->setMassActionCheckboxes('checkbox', '[id]');

		// add mass action dropdown
		$ddmMassAction = new SpoonFormDropdown('action', array('delete' => BL::getLabel('Delete')), 'delete');
		$ddmMassAction->setOptionAttributes('delete', array('rel' => 'confirmDelete'));
		$this->datagrid->setMassAction($ddmMassAction);

		// add column
		$this->datagrid->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit') .'&amp;id=[id]', BL::getLabel('Edit'));

		// add attributes, so the inline editing has all the needed data
		$this->datagrid->setColumnAttributes('tag', array('rel' => '{id:[id]}'));
	}


	/**
	 * Parse & display the page
	 *
	 * @return	void
	 */
	private function parse()
	{
		$this->tpl->assign('datagrid', ($this->datagrid->getNumResults() != 0) ? $this->datagrid->getContent() : false);
	}
}

?>