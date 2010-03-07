<?php

// @todo tijs - ik zou de 'recent aangepaste' hier enkel zetten als we meer dan 1 pagina hebben. Wat vind jij?

/**
 * BackendBlogIndex
 * This is the index-action (default), it will display the overview of blog posts
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @author		Dave Lens <dave@netlash.com>
 * @author		Tijs Verkoyen <tijs@netlash.com>
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

		// the most recent blogposts
		$this->loadDatagridRecentPosts();
	}


	/**
	 * Loads the datagrid with all the posts
	 *
	 * @return	void
	 */
	private function loadDatagridAllPosts()
	{
		// create datagrid
		$this->dgPosts = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE, array('active'));

		// set headers values
		$headers['user_id'] = ucfirst(BL::getLabel('Author'));
		$headers['title'] = ucfirst(BL::getLabel('Title'));
		$headers['publish_on'] = ucfirst(BL::getLabel('PublishedOn'));
		$headers['comments'] = ucfirst(BL::getLabel('Comments'));

		// set headers
		$this->dgPosts->setHeaderLabels($headers);

		// sorting columns
		$this->dgPosts->setSortingColumns(array('publish_on', 'title', 'user_id', 'comments'), 'publish_on');
		$this->dgPosts->setSortParameter('desc');

		// set colum URLs
		$this->dgPosts->setColumnURL('title', BackendModel::createURLForAction('edit') .'&id=[id]');

		// add the multicheckbox column
		$this->dgPosts->addColumn('checkbox', '<div class="checkboxHolder"><input type="checkbox" name="toggleChecks" value="toggleChecks" />', '<input type="checkbox" name="id[]" value="[id]" class="inputCheckbox" /></div>');
		$this->dgPosts->setColumnsSequence('checkbox');

		// set column functions
		$this->dgPosts->setColumnFunction(array('BackendDatagridFunctions', 'getLongDate'), array('[publish_on]'), 'publish_on', true);
		$this->dgPosts->setColumnFunction(array('BackendDatagridFunctions', 'getUser'), array('[user_id]'), 'user_id', true);

		// add mass action dropdown
		$ddmMassAction = new SpoonFormDropdown('action', array('delete' => BL::getLabel('Delete')), 'delete');
		$this->dgPosts->setMassAction($ddmMassAction);

		// add edit column
		$this->dgPosts->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit') .'&id=[id]', BL::getLabel('Edit'));
	}


	/**
	 * Loads the datagrid with all the posts
	 *
	 * @return	void
	 */
	private function loadDatagridDrafts()
	{
		// create datagrid
		$this->dgDrafts = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_DRAFTS, array('draft', BackendAuthentication::getUser()->getUserId()));

		// set headers values
		$headers['user_id'] = ucfirst(BL::getLabel('Author'));
		$headers['title'] = ucfirst(BL::getLabel('Title'));
		$headers['edited_on'] = ucfirst(BL::getLabel('EditedOn'));
		$headers['comments'] = ucfirst(BL::getLabel('Comments'));

		// set headers
		$this->dgDrafts->setHeaderLabels($headers);

		$this->dgDrafts->setColumnsHidden(array('revision_id'));

		// sorting columns
		$this->dgDrafts->setSortingColumns(array('edited_on', 'title', 'user_id', 'comments'), 'edited_on');
		$this->dgDrafts->setSortParameter('desc');

		// set colum URLs
		$this->dgDrafts->setColumnURL('title', BackendModel::createURLForAction('edit') .'&id=[id]&draft=[revision_id]');

		// add the fake multicheckbox column
		$this->dgDrafts->addColumn('checkbox', '', '');
		$this->dgDrafts->setColumnsSequence('checkbox');

		// set column functions
		$this->dgDrafts->setColumnFunction(array('BackendDatagridFunctions', 'getLongDate'), array('[edited_on]'), 'edited_on', true);
		$this->dgDrafts->setColumnFunction(array('BackendDatagridFunctions', 'getUser'), array('[user_id]'), 'user_id', true);

		// add edit column
		$this->dgDrafts->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit') .'&id=[id]&draft=[revision_id]', BL::getLabel('Edit'));
	}


	/**
	 * Loads the datagrid with all the posts
	 *
	 * @return	void
	 */
	private function loadDatagridRecentPosts()
	{
		// create datagrid
		$this->dgRecent = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_RECENT, array('active'));

		// set headers values
		$headers['user_id'] = ucfirst(BL::getLabel('Author'));
		$headers['title'] = ucfirst(BL::getLabel('Title'));
		$headers['edited_on'] = ucfirst(BL::getLabel('EditedOn'));
		$headers['comments'] = ucfirst(BL::getLabel('Comments'));

		// set headers
		$this->dgRecent->setHeaderLabels($headers);

		// set paging
		$this->dgRecent->setPaging(false);

		// set colum URLs
		$this->dgRecent->setColumnURL('title', BackendModel::createURLForAction('edit') .'&id=[id]');

		// add the fake multicheckbox column
		$this->dgRecent->addColumn('checkbox', '', '');
		$this->dgRecent->setColumnsSequence('checkbox');

		// set column functions
		$this->dgRecent->setColumnFunction(array('BackendDatagridFunctions', 'getLongDate'), array('[edited_on]'), 'edited_on', true);
		$this->dgRecent->setColumnFunction(array('BackendDatagridFunctions', 'getUser'), array('[user_id]'), 'user_id', true);

		// add edit column
		$this->dgRecent->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit') .'&id=[id]', BL::getLabel('Edit'));
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
		$this->tpl->assign('dgRecent', ($this->dgRecent->getNumResults() != 0) ? $this->dgRecent->getContent() : false);
	}
}

?>