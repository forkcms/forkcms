<?php

/**
 * BackendMailmotorGroups
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
	private function loadDatagrid()
	{
		// create datagrid
		$this->datagrid = new BackendDataGridDB(BackendMailmotorModel::QRY_DATAGRID_BROWSE_GROUPS);
		$this->datagrid->setColumnsHidden(array('language', 'is_default'));

		// sorting columns
		$this->datagrid->setSortingColumns(array('name', 'created_on'), 'created_on');
		$this->datagrid->setSortParameter('desc');

		// set colum URLs
		$this->datagrid->setColumnURL('name', BackendModel::createURLForAction('addresses') . '&amp;group_id=[id]');

		// set the datagrid ID so we don't run into trouble with multiple datagrids that use mass actions
		$this->datagrid->setAttributes(array('id' => 'dgGroups'));

		// add the multicheckbox column
		$this->datagrid->setMassActionCheckboxes('checkbox', '[id]', BackendMailmotorModel::getDefaultGroupIds());
		$this->datagrid->setColumnsSequence('checkbox', 'name', 'created_on', 'language');

		// add mass action dropdown
		$ddmMassAction = new SpoonFormDropdown('action', array('delete' => BL::lbl('Delete')), 'delete');
		$this->datagrid->setMassAction($ddmMassAction);

		// set column functions
		$this->datagrid->setColumnFunction(array('BackendDatagridFunctions', 'getTimeAgo'), array('[created_on]'), 'created_on', true);

		// add delete column
		$this->datagrid->addColumnAction('custom_fields', null, BL::lbl('CustomFields'), BackendModel::createURLForAction('custom_fields') . '&amp;group_id=[id]', BL::lbl('CustomFields'), array('class' => 'button icon iconEdit linkButton'));
		$this->datagrid->addColumnAction('export', null, BL::lbl('Export'), BackendModel::createURLForAction('export_addresses') . '&amp;id=[id]', BL::lbl('Export'), array('class' => 'button icon iconExport linkButton'));
		$this->datagrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit_group') . '&amp;id=[id]', BL::lbl('Edit'));

		// add styles
		$this->datagrid->setColumnAttributes('name', array('class' => 'title'));

		// set paging limit
		$this->datagrid->setPagingLimit(self::PAGING_LIMIT);
	}


	/**
	 * Parse all datagrids
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse the datagrid
		$this->tpl->assign('datagrid', ($this->datagrid->getNumResults() != 0) ? $this->datagrid->getContent() : false);
	}
}

?>