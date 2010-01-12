<?php

/**
 * PagesIndex
 *
 * This is the index-action (default), it will display the pages-overview
 *
 * @package		backend
 * @subpackage	pages
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class PagesIndex extends BackendBaseActionIndex
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

		// check if the cached files exists
		if(!SpoonFile::exists(PATH_WWW .'/frontend/cache/navigation/keys_'. BackendLanguage::getWorkingLanguage() .'.php')) BackendPagesModel::buildCache();
		if(!SpoonFile::exists(PATH_WWW .'/frontend/cache/navigation/navigation_'. BackendLanguage::getWorkingLanguage() .'.php')) BackendPagesModel::buildCache();

		// load the datagrid
		$this->loadDatagrid();

		// parse
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the datagrid
	 *
	 * @return	void
	 */
	private function loadDatagrid()
	{
		// create datagrid
		$this->datagrid = new BackendDataGridDB(BackendPagesModel::QRY_BROWSE_RECENT, array('active', BL::getWorkingLanguage(), 7));

		// disable paging
		$this->datagrid->setPaging(false);

		// hide columns
		$this->datagrid->setColumnsHidden(array('id'));

		// set functions
		$this->datagrid->setColumnFunction(array('BackendDataGridFunctions', 'getUser'), array('[user_id]'), 'user_id');
		$this->datagrid->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), array('[edited_on]'), 'edited_on');

		// set column url
		$this->datagrid->setColumnUrl('title', BackendModel::createURLForAction('edit') .'&id=[id]', BL::getLabel('Edit'));

		// add edit column @todo Davy, [id] werkt niet.
		$this->datagrid->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit') .'&id=[id]', BL::getLabel('Edit'));

		// set headers
		$this->datagrid->setHeaderLabels(array(	'user_id' => ucfirst(BL::getLabel('By')),
												'edited_on' => ucfirst(BL::getLabel('Date')),
												'title' => ucfirst(BL::getLabel('Page'))));
	}


	/**
	 * Parse the datagrid and the reports
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse datagrid
		$this->tpl->assign('datagrid', ($this->datagrid->getNumResults() != 0) ? $this->datagrid->getContent() : false);

		// parse the tree
		$this->tpl->assign('tree', BackendPagesModel::getTreeHTML());
	}
}

?>