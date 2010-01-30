<?php

// @todo fix URL for author column

/**
 * BackendBlogIndex
 *
 * This is the index-action (default), it will display the overview of blog posts
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendBlogIndex extends BackendBaseActionIndex
{
	/**
	 * Datagrids
	 *
	 * @var	SpoonDataGrid
	 */
	private $dgRecent, $dgPosts;


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
		$this->dgPosts->setColumnHidden('author_id');

		// set headers values
		$headers['author'] = ucfirst(BL::getLabel('Author'));
		$headers['title'] = ucfirst(BL::getLabel('Title'));
		$headers['publish_on'] = ucfirst(BL::getLabel('PublishedOn'));
		$headers['comments'] = ucfirst(BL::getLabel('Comments'));

		// set headers
		$this->dgPosts->setHeaderLabels($headers);

		// sorting columns
		$this->dgPosts->setSortingColumns(array('publish_on', 'title', 'author', 'comments'), 'publish_on');
		$this->dgPosts->setSortParameter('desc');

		// set colum URLs
		$this->dgPosts->setColumnURL('title', BackendModel::createURLForAction('edit') .'&id=[id]');

		// column attributes
		$this->dgPosts->setColumnAttributes('publish_on', array('class' => 'date'));

		// add the multicheckbox column
		$this->dgPosts->addColumn('checkbox', '<div class="checkboxHolder"><input type="checkbox" name="toggleChecks" value="toggleChecks" />', '<input type="checkbox" name="id[]" value="[id]" class="inputCheckbox" /></div>');
		$this->dgPosts->setColumnsSequence('checkbox');

		// set column functions
		$this->dgPosts->setColumnFunction(array('SpoonDate', 'getDate'), array('d/m/Y @ H:i', '[publish_on]'), 'publish_on', true);
		$this->dgPosts->setColumnFunction(array('BackendBlogModel', 'getAuthorHTML'), array('[author]', '[author_id]'), 'author', true);

		// add mass action dropdown
		$ddmMassAction = new SpoonDropDown('action', array('delete' => BL::getLabel('Delete')), 'delete');
		$this->dgPosts->setMassAction($ddmMassAction);

		// add edit column
		$this->dgPosts->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit') .'&id=[id]', BL::getLabel('Edit'));
	}


	/**
	 * Loads the datagrid with all the posts
	 *
	 * @return	void
	 */
	private function loadDatagridRecentPosts()
	{
		// create datagrid
		$this->dgRecent = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE, array('active'));

		// set headers
		$this->dgRecent->setHeaderLabels(array('title' => ucfirst(BL::getLabel('Title'))));

		// sorting columns
		$this->dgRecent->setSortingColumns(array('title'));

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
		// parse the datagrid for the most recent blogposts
		//$this->tpl->assign('dgRecent', ($this->dgRecent->getNumResults() != 0) ? $this->dgRecent->getContent() : false);

		// parse the datagrid for all blogposts
		$this->tpl->assign('dgPosts', ($this->dgPosts->getNumResults() != 0) ? $this->dgPosts->getContent() : false);
	}
}

?>