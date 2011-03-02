<?php
/**
 * BackendModulemanagerActions
 * This is the actions-action (default), it will display the actions of a module
 *
 * @package		backend
 * @subpackage	module_manager
 *
 * @author 		Frederik Heyninck <frederik@figure8.be>
 * @since		2.0
 */
class BackendModulemanagerActions extends BackendBaseActionIndex
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
		
		// load record
		$this->loadData();
		
		// load datagrid
		$this->loadDatagrid();

		// parse
		$this->parse();

		// display the page
		$this->display();
	}
	
	
	/**
	 * Load the record
	 *
	 * @return	void
	 */
	private function loadData()
	{
		// get record
		$this->module = $this->getParameter('module', 'string');

		// validate id
		if($this->module === null || !BackendModulemanagerModel::exists($this->module)) $this->redirect(BackendModel::createURLForAction('index') .'&error=non-existing');

		// get the record
		$this->record = BackendModulemanagerModel::get($this->module);
	}
	
	
	/**
	 * Load the datagrids
	 *
	 * @return	void
	 */
	private function loadDatagrid()
	{
		// create datagrid
		$this->datagrid = new BackendDataGridDB(BackendModulemanagerModel::QRY_DATAGRID_BROWSE_ACTIONS, $this->module);

		// add column
		$this->datagrid->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit_action') .'&amp;id=[id]', BL::getLabel('Edit'));

		// disable paging
		$this->datagrid->setPaging(false);
	}


	/**
	 * Parse the form
	 *
	 * @return	void
	 */
	protected function parse()
	{
		$this->tpl->assign('item', $this->record);
		$this->tpl->assign('datagrid', ($this->datagrid->getNumResults() != 0) ? $this->datagrid->getContent() : false);
	}
}

?>