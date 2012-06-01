<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the index-action (default), it will display the pages-overview
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendPagesIndex extends BackendBaseActionIndex
{
	/**
	 * DataGrids
	 *
	 * @var	BackendDataGridDB
	 */
	private $dgDrafts, $dgRecentlyEdited;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// add js
		$this->header->addJS('jstree/jquery.tree.js', null, false);
		$this->header->addJS('jstree/lib/jquery.cookie.js', null, false);
		$this->header->addJS('jstree/plugins/jquery.tree.cookie.js', null, false);

		// add css
		$this->header->addCSS('/backend/modules/pages/js/jstree/themes/fork/style.css', null, true);

		// check if the cached files exists
		if(!SpoonFile::exists(PATH_WWW . '/frontend/cache/navigation/keys_' . BackendLanguage::getWorkingLanguage() . '.php')) BackendPagesModel::buildCache(BL::getWorkingLanguage());
		if(!SpoonFile::exists(PATH_WWW . '/frontend/cache/navigation/navigation_' . BackendLanguage::getWorkingLanguage() . '.php')) BackendPagesModel::buildCache(BL::getWorkingLanguage());

		// load the dgRecentlyEdited
		$this->loadDataGrids();

		// parse
		$this->parse();

		// display the page
		$this->display();
	}

	/**
	 * Load the datagird with the drafts
	 */
	private function loadDataGridDrafts()
	{
		// create datagrid
		$this->dgDrafts = new BackendDataGridDB(BackendPagesModel::QRY_DATAGRID_BROWSE_DRAFTS, array('draft', BackendAuthentication::getUser()->getUserId(), BL::getWorkingLanguage()));

		// hide columns
		$this->dgDrafts->setColumnsHidden(array('revision_id'));

		// disable paging
		$this->dgDrafts->setPaging(false);

		// set column functions
		$this->dgDrafts->setColumnFunction(array('BackendDataGridFunctions', 'getUser'), array('[user_id]'), 'user_id', true);
		$this->dgDrafts->setColumnFunction(array('BackendDataGridFunctions', 'getLongDate'), array('[edited_on]'), 'edited_on');

		// set headers
		$this->dgDrafts->setHeaderLabels(array('user_id' => SpoonFilter::ucfirst(BL::lbl('By')), 'edited_on' => SpoonFilter::ucfirst(BL::lbl('LastEdited'))));

		// check if allowed to edit
		if(BackendAuthentication::isAllowedAction('edit', $this->getModule()))
		{
			// set column URLs
			$this->dgDrafts->setColumnURL('title', BackendModel::createURLForAction('edit') . '&amp;id=[id]&amp;draft=[revision_id]');

			// add edit column
			$this->dgDrafts->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]&amp;draft=[revision_id]', BL::lbl('Edit'));
		}
	}

	/**
	 * Load the datagrid with the recently edited items
	 */
	private function loadDataGridRecentlyEdited()
	{
		// create dgRecentlyEdited
		$this->dgRecentlyEdited = new BackendDataGridDB(BackendPagesModel::QRY_BROWSE_RECENT, array('active', BL::getWorkingLanguage(), 7));

		// disable paging
		$this->dgRecentlyEdited->setPaging(false);

		// hide columns
		$this->dgRecentlyEdited->setColumnsHidden(array('id'));

		// set functions
		$this->dgRecentlyEdited->setColumnFunction(array('BackendDataGridFunctions', 'getUser'), array('[user_id]'), 'user_id');
		$this->dgRecentlyEdited->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), array('[edited_on]'), 'edited_on');

		// set headers
		$this->dgRecentlyEdited->setHeaderLabels(array('user_id' => SpoonFilter::ucfirst(BL::lbl('By')), 'edited_on' => SpoonFilter::ucfirst(BL::lbl('LastEdited'))));

		// check if allowed to edit
		if(BackendAuthentication::isAllowedAction('edit', $this->getModule()))
		{
			// set column URL
			$this->dgRecentlyEdited->setColumnUrl('title', BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::lbl('Edit'));

			// add column
			$this->dgRecentlyEdited->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::lbl('Edit'));
		}
	}

	/**
	 * Load the datagrids
	 */
	private function loadDataGrids()
	{
		// load the datagrid with the recently edited items
		$this->loadDataGridRecentlyEdited();

		// load the dategird with the drafts
		$this->loadDataGridDrafts();
	}

	/**
	 * Parse the datagrid and the reports
	 */
	protected function parse()
	{
		parent::parse();

		// parse dgRecentlyEdited
		$this->tpl->assign('dgRecentlyEdited', ($this->dgRecentlyEdited->getNumResults() != 0) ? $this->dgRecentlyEdited->getContent() : false);
		$this->tpl->assign('dgDrafts', ($this->dgDrafts->getNumResults() != 0) ? $this->dgDrafts->getContent() : false);

		// parse the tree
		$this->tpl->assign('tree', BackendPagesModel::getTreeHTML());

		// open the tree on a specific page
		if($this->getParameter('id', 'int') !== null) $this->tpl->assign('openedPageId', $this->getParameter('id', 'int'));
		else $this->tpl->assign('openedPageId', 1);
	}
}
