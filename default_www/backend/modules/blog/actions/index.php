<?php

/**
 * This is the index-action (default), it will display the overview of blog posts
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Dave Lens <dave@netlash.com>
 * @author		Tijs Verkoyen <tijs@sumocoders.com>
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
class BackendBlogIndex extends BackendBaseActionIndex
{
	/**
	 * Datagrids
	 *
	 * @var	SpoonDataGrid
	 */
	private $dgDrafts, $dgPosts, $dgRecent;


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
		$this->loadDataGrids();

		// parse page
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Loads the datagrid with all the posts
	 *
	 * @return	void
	 */
	private function loadDatagridAllPosts()
	{
		// create datagrid
		$this->dgPosts = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE, array('active', BL::getWorkingLanguage()));

		// set headers
		$this->dgPosts->setHeaderLabels(array('user_id' => ucfirst(BL::lbl('Author')), 'publish_on' => ucfirst(BL::lbl('PublishedOn'))));

		// hide columns
		$this->dgPosts->setColumnsHidden(array('revision_id'));

		// sorting columns
		$this->dgPosts->setSortingColumns(array('publish_on', 'title', 'user_id', 'comments'), 'publish_on');
		$this->dgPosts->setSortParameter('desc');

		// set colum URLs
		$this->dgPosts->setColumnURL('title', BackendModel::createURLForAction('edit') . '&amp;id=[id]');

		// set column functions
		$this->dgPosts->setColumnFunction(array('BackendDatagridFunctions', 'getLongDate'), array('[publish_on]'), 'publish_on', true);
		$this->dgPosts->setColumnFunction(array('BackendDatagridFunctions', 'getUser'), array('[user_id]'), 'user_id', true);

		// add edit column
		$this->dgPosts->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::lbl('Edit'));

		// our JS needs to know an id, so we can highlight it
		$this->dgPosts->setRowAttributes(array('id' => 'row-[revision_id]'));
	}


	/**
	 * Loads the datagrid with all the drafts
	 *
	 * @return	void
	 */
	private function loadDatagridDrafts()
	{
		// create datagrid
		$this->dgDrafts = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_DRAFTS, array('draft', BackendAuthentication::getUser()->getUserId(), BL::getWorkingLanguage()));

		// set headers
		$this->dgDrafts->setHeaderLabels(array('user_id' => ucfirst(BL::lbl('Author'))));

		// hide columns
		$this->dgDrafts->setColumnsHidden(array('revision_id'));

		// sorting columns
		$this->dgDrafts->setSortingColumns(array('edited_on', 'title', 'user_id', 'comments'), 'edited_on');
		$this->dgDrafts->setSortParameter('desc');

		// set colum URLs
		$this->dgDrafts->setColumnURL('title', BackendModel::createURLForAction('edit') . '&amp;id=[id]&amp;draft=[revision_id]');

		// set column functions
		$this->dgDrafts->setColumnFunction(array('BackendDatagridFunctions', 'getLongDate'), array('[edited_on]'), 'edited_on', true);
		$this->dgDrafts->setColumnFunction(array('BackendDatagridFunctions', 'getUser'), array('[user_id]'), 'user_id', true);

		// add edit column
		$this->dgDrafts->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]&amp;draft=[revision_id]', BL::lbl('Edit'));

		// our JS needs to know an id, so we can highlight it
		$this->dgDrafts->setRowAttributes(array('id' => 'row-[revision_id]'));
	}


	/**
	 * Loads the datagrid with the most recent posts.
	 *
	 * @return	void
	 */
	private function loadDatagridRecentPosts()
	{
		// create datagrid
		$this->dgRecent = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_RECENT, array('active', BL::getWorkingLanguage(), 4));

		// set headers
		$this->dgRecent->setHeaderLabels(array('user_id' => ucfirst(BL::lbl('Author'))));

		// hide columns
		$this->dgRecent->setColumnsHidden(array('revision_id'));

		// set paging
		$this->dgRecent->setPaging(false);

		// set colum URLs
		$this->dgRecent->setColumnURL('title', BackendModel::createURLForAction('edit') . '&amp;id=[id]');

		// set column functions
		$this->dgRecent->setColumnFunction(array('BackendDatagridFunctions', 'getLongDate'), array('[edited_on]'), 'edited_on', true);
		$this->dgRecent->setColumnFunction(array('BackendDatagridFunctions', 'getUser'), array('[user_id]'), 'user_id', true);

		// add edit column
		$this->dgRecent->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::lbl('Edit'));

		// our JS needs to know an id, so we can highlight it
		$this->dgRecent->setRowAttributes(array('id' => 'row-[revision_id]'));
	}


	/**
	 * Loads the datagrids for the blogposts
	 *
	 * @return	void
	 */
	private function loadDataGrids()
	{
		// all blogposts
		$this->loadDatagridAllPosts();

		// drafts
		$this->loadDatagridDrafts();

		// the most recent blogposts, only shown when we have more than 1 page in total
		if($this->dgPosts->getNumResults() > $this->dgPosts->getPagingLimit()) $this->loadDatagridRecentPosts();
	}


	/**
	 * Parse all datagrids
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse the datagrid for the drafts
		$this->tpl->assign('dgDrafts', ($this->dgDrafts->getNumResults() != 0) ? $this->dgDrafts->getContent() : false);

		// parse the datagrid for all blogposts
		$this->tpl->assign('dgPosts', ($this->dgPosts->getNumResults() != 0) ? $this->dgPosts->getContent() : false);

		// parse the datagrid for the most recent blogposts
		$this->tpl->assign('dgRecent', (is_object($this->dgRecent) && $this->dgRecent->getNumResults() != 0) ? $this->dgRecent->getContent() : false);
	}
}

?>