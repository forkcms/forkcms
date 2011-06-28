<?php

/**
 * This page will display the overview of groups
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorGroups extends BackendBaseActionIndex
{
	// maximim number of items
	const PAGING_LIMIT = 10;


	/**
	 * Checks if default groups were set, and shows a message with more info if they are not.
	 *
	 * @return	void
	 */
	private function checkForDefaultGroups()
	{
		// groups are already set
		if(BackendModel::getModuleSetting('mailmotor', 'cm_groups_defaults_set')) return true;

		// show the message
		$this->tpl->assign('noDefaultsSet', true);
	}


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// check for default groups
		$this->checkForDefaultGroups();

		// load datagrid
		$this->loadDataGrid();

		// parse page
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Loads the datagrid with the groups
	 *
	 * @return	void
	 */
	private function loadDataGrid()
	{
		// create datagrid
		$this->dataGrid = new BackendDataGridDB(BackendMailmotorModel::QRY_DATAGRID_BROWSE_GROUPS);
		$this->dataGrid->setColumnsHidden(array('language', 'is_default'));

		// sorting columns
		$this->dataGrid->setSortingColumns(array('name', 'created_on'), 'created_on');
		$this->dataGrid->setSortParameter('desc');

		// set colum URLs
		$this->dataGrid->setColumnURL('name', BackendModel::createURLForAction('addresses') . '&amp;group_id=[id]');

		// set the datagrid ID so we don't run into trouble with multiple datagrids that use mass actions
		$this->dataGrid->setAttributes(array('id' => 'dgGroups'));

		// add the multicheckbox column
		$this->dataGrid->setMassActionCheckboxes('checkbox', '[id]', BackendMailmotorModel::getDefaultGroupIds());
		$this->dataGrid->setColumnsSequence('checkbox', 'name', 'created_on', 'language');

		// add mass action dropdown
		$ddmMassAction = new SpoonFormDropdown('action', array('delete' => BL::lbl('Delete')), 'delete');
		$this->dataGrid->setMassAction($ddmMassAction);

		// set column functions
		$this->dataGrid->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), array('[created_on]'), 'created_on', true);

		// add delete column
		$this->dataGrid->addColumnAction('custom_fields', null, BL::lbl('CustomFields'), BackendModel::createURLForAction('custom_fields') . '&amp;group_id=[id]', BL::lbl('CustomFields'), array('class' => 'button icon iconEdit linkButton'));
		$this->dataGrid->addColumnAction('export', null, BL::lbl('Export'), BackendModel::createURLForAction('export_addresses') . '&amp;id=[id]', BL::lbl('Export'), array('class' => 'button icon iconExport linkButton'));
		$this->dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit_group') . '&amp;id=[id]', BL::lbl('Edit'));

		// add styles
		$this->dataGrid->setColumnAttributes('name', array('class' => 'title'));

		// set paging limit
		$this->dataGrid->setPagingLimit(self::PAGING_LIMIT);
	}


	/**
	 * Parse all datagrids
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse the datagrid
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
	}
}

?>