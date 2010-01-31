<?php

/**
 * BackendLocaleIndex
 *
 * This is the index-action, it will display the overview of language labels
 *
 * @package		backend
 * @subpackage	locale
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendLocaleIndex extends BackendBaseActionIndex
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
	 * @return void
	 */
	private function loadDataGrid()
	{
		// create datagrid
		$this->datagrid = new BackendDataGridDB(BackendLocaleModel::QRY_DATAGRID_BROWSE);

		// header labels
		$this->datagrid->setHeaderLabels(array('language' => ucfirst(BL::getLabel('Language')), 'application' => ucfirst(BL::getLabel('Application')), 'module' => ucfirst(BL::getLabel('Module')), 'type' => ucfirst(BL::getLabel('Type')), 'name' => ucfirst(BL::getLabel('Name')), 'value' => ucfirst(BL::getLabel('Value'))));

		// sorting columns
		$this->datagrid->setSortingColumns(array('language', 'application', 'module', 'type', 'name', 'value'), 'name');

		// add the multicheckbox column
		$this->datagrid->addColumn('checkbox', '<div class="checkboxHolder"><input type="checkbox" name="toggleChecks" value="toggleChecks" />', '<input type="checkbox" name="id[]" value="[id]" class="inputCheckbox" /></div>');
		$this->datagrid->setColumnsSequence('checkbox');

		// add mass action dropdown
		$ddmMassAction = new SpoonDropDown('action', array('delete' => BL::getLabel('Delete')), 'delete');
		$this->datagrid->setMassAction($ddmMassAction);

		// update value
		$this->datagrid->setColumnFunction(array('BackendDataGridFunctions', 'truncate'), array('[value]', 30), 'value', true);

		// add columns
		$this->datagrid->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit') .'&id=[id]', BL::getLabel('Edit'));
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