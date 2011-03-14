<?php

/**
 * This is the index-action (default), it will display the pages-overview
 *
 * @package		backend
 * @subpackage	pages
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendPagesIndex extends BackendBaseActionIndex
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

		// add js
		$this->header->addJavascript('jstree/jquery.tree.js');
		$this->header->addJavascript('jstree/lib/jquery.cookie.js');
		$this->header->addJavascript('jstree/plugins/jquery.tree.cookie.js');

		// add css
		$this->header->addCSS('/backend/modules/pages/js/jstree/themes/fork/style.css', null, true);

		// check if the cached files exists
		if(!SpoonFile::exists(PATH_WWW . '/frontend/cache/navigation/keys_' . BackendLanguage::getWorkingLanguage() . '.php')) BackendPagesModel::buildCache(BL::getWorkingLanguage());
		if(!SpoonFile::exists(PATH_WWW . '/frontend/cache/navigation/navigation_' . BackendLanguage::getWorkingLanguage() . '.php')) BackendPagesModel::buildCache(BL::getWorkingLanguage());

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

		// set column URL
		$this->datagrid->setColumnUrl('title', BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::lbl('Edit'));

		// add column
		$this->datagrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::lbl('Edit'));

		// set headers
		$this->datagrid->setHeaderLabels(array('user_id' => ucfirst(BL::lbl('By')),
												'edited_on' => ucfirst(BL::lbl('LastEdited'))));
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

		// open the tree on a specific page
		if($this->getParameter('id', 'int') !== null) $this->tpl->assign('openedPageId', $this->getParameter('id', 'int'));
		else $this->tpl->assign('openedPageId', 1);
	}
}

?>