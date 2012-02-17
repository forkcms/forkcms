<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the index-action (default), it will display the overview of blog posts
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Dave Lens <dave.lens@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.com>
 * @author Matthias Mullie <matthias@mullie.eu>
 */
class BackendBlogIndex extends BackendBaseActionIndex
{
	/**
	 * The category where is filtered on
	 *
	 * @var	array
	 */
	private $category;

	/**
	 * The id of the category where is filtered on
	 *
	 * @var	int
	 */
	private $categoryId;

	/**
	 * DataGrids
	 *
	 * @var	SpoonDataGrid
	 */
	private $dgDrafts, $dgPosts, $dgRecent;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// set category id
		$this->categoryId = SpoonFilter::getGetValue('category', null, null, 'int');
		if($this->categoryId == 0) $this->categoryId = null;
		else
		{
			// get category
			$this->category = BackendBlogModel::getCategory($this->categoryId);

			// reset
			if(empty($this->category))
			{
				// reset GET to trick Spoon
				$_GET['category'] = null;

				// reset
				$this->categoryId = null;
			}
		}

		$this->loadDataGrids();
		$this->parse();
		$this->display();
	}

	/**
	 * Loads the datagrid with all the posts
	 */
	private function loadDataGridAllPosts()
	{
		// filter on category?
		if($this->categoryId != null)
		{
			// create datagrid
			$this->dgPosts = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_FOR_CATEGORY, array($this->categoryId, 'active', BL::getWorkingLanguage()));

			// set the URL
			$this->dgPosts->setURL('&amp;category=' . $this->categoryId, true);
		}

		else
		{
			// create datagrid
			$this->dgPosts = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE, array('active', BL::getWorkingLanguage()));
		}

		// set headers
		$this->dgPosts->setHeaderLabels(array('user_id' => SpoonFilter::ucfirst(BL::lbl('Author')), 'publish_on' => SpoonFilter::ucfirst(BL::lbl('PublishedOn'))));

		// hide columns
		$this->dgPosts->setColumnsHidden(array('revision_id'));

		// sorting columns
		$this->dgPosts->setSortingColumns(array('publish_on', 'title', 'user_id', 'comments'), 'publish_on');
		$this->dgPosts->setSortParameter('desc');

		// set column functions
		$this->dgPosts->setColumnFunction(array('BackendDataGridFunctions', 'getLongDate'), array('[publish_on]'), 'publish_on', true);
		$this->dgPosts->setColumnFunction(array('BackendDataGridFunctions', 'getUser'), array('[user_id]'), 'user_id', true);

		// our JS needs to know an id, so we can highlight it
		$this->dgPosts->setRowAttributes(array('id' => 'row-[revision_id]'));

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('edit'))
		{
			// set column URLs
			$this->dgPosts->setColumnURL('title', BackendModel::createURLForAction('edit') . '&amp;id=[id]&amp;category=' . $this->categoryId);

			// add edit column
			$this->dgPosts->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]&amp;category=' . $this->categoryId, BL::lbl('Edit'));
		}
	}

	/**
	 * Loads the datagrid with all the drafts
	 */
	private function loadDataGridDrafts()
	{
		// filter on category?
		if($this->categoryId != null)
		{
			// create datagrid
			$this->dgDrafts = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_DRAFTS_FOR_CATEGORY, array($this->categoryId, 'draft', BackendAuthentication::getUser()->getUserId(), BL::getWorkingLanguage()));

			// set the URL
			$this->dgDrafts->setURL('&amp;category=' . $this->categoryId, true);
		}

		else
		{
			// create datagrid
			$this->dgDrafts = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_DRAFTS, array('draft', BackendAuthentication::getUser()->getUserId(), BL::getWorkingLanguage()));
		}

		// set headers
		$this->dgDrafts->setHeaderLabels(array('user_id' => SpoonFilter::ucfirst(BL::lbl('Author'))));

		// hide columns
		$this->dgDrafts->setColumnsHidden(array('revision_id'));

		// sorting columns
		$this->dgDrafts->setSortingColumns(array('edited_on', 'title', 'user_id', 'comments'), 'edited_on');
		$this->dgDrafts->setSortParameter('desc');

		// set column functions
		$this->dgDrafts->setColumnFunction(array('BackendDataGridFunctions', 'getLongDate'), array('[edited_on]'), 'edited_on', true);
		$this->dgDrafts->setColumnFunction(array('BackendDataGridFunctions', 'getUser'), array('[user_id]'), 'user_id', true);

		// our JS needs to know an id, so we can highlight it
		$this->dgDrafts->setRowAttributes(array('id' => 'row-[revision_id]'));

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('edit'))
		{
			// set colum URLs
			$this->dgDrafts->setColumnURL('title', BackendModel::createURLForAction('edit') . '&amp;id=[id]&amp;draft=[revision_id]&amp;category=' . $this->categoryId);

			// add edit column
			$this->dgDrafts->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]&amp;draft=[revision_id]&amp;category=' . $this->categoryId, BL::lbl('Edit'));
		}
	}

	/**
	 * Loads the datagrid with the most recent posts.
	 */
	private function loadDataGridRecentPosts()
	{
		// filter on category?
		if($this->categoryId != null)
		{
			// create datagrid
			$this->dgRecent = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_RECENT_FOR_CATEGORY, array($this->categoryId, 'active', BL::getWorkingLanguage(), 4));

			// set the URL
			$this->dgRecent->setURL('&amp;category=' . $this->categoryId, true);
		}

		else
		{
			// create datagrid
			$this->dgRecent = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_RECENT, array('active', BL::getWorkingLanguage(), 4));
		}

		// set headers
		$this->dgRecent->setHeaderLabels(array('user_id' => SpoonFilter::ucfirst(BL::lbl('Author'))));

		// hide columns
		$this->dgRecent->setColumnsHidden(array('revision_id'));

		// set paging
		$this->dgRecent->setPaging(false);

		// set column functions
		$this->dgRecent->setColumnFunction(array('BackendDataGridFunctions', 'getLongDate'), array('[edited_on]'), 'edited_on', true);
		$this->dgRecent->setColumnFunction(array('BackendDataGridFunctions', 'getUser'), array('[user_id]'), 'user_id', true);

		// our JS needs to know an id, so we can highlight it
		$this->dgRecent->setRowAttributes(array('id' => 'row-[revision_id]'));

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('edit'))
		{
			// set colum URLs
			$this->dgRecent->setColumnURL('title', BackendModel::createURLForAction('edit') . '&amp;id=[id]&amp;category=' . $this->categoryId);

			// add edit column
			$this->dgRecent->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]&amp;category=' . $this->categoryId, BL::lbl('Edit'));
		}
	}

	/**
	 * Loads the datagrids for the blogposts
	 */
	private function loadDataGrids()
	{
		$this->loadDataGridAllPosts();
		$this->loadDataGridDrafts();

		// the most recent blogposts, only shown when we have more than 1 page in total
		if($this->dgPosts->getNumResults() > $this->dgPosts->getPagingLimit()) $this->loadDataGridRecentPosts();
	}

	/**
	 * Parse all datagrids
	 */
	protected function parse()
	{
		parent::parse();

		// parse the datagrid for the drafts
		$this->tpl->assign('dgDrafts', ($this->dgDrafts->getNumResults() != 0) ? $this->dgDrafts->getContent() : false);

		// parse the datagrid for all blogposts
		$this->tpl->assign('dgPosts', ($this->dgPosts->getNumResults() != 0) ? $this->dgPosts->getContent() : false);

		// parse the datagrid for the most recent blogposts
		$this->tpl->assign('dgRecent', (is_object($this->dgRecent) && $this->dgRecent->getNumResults() != 0) ? $this->dgRecent->getContent() : false);

		// get categories
		$categories = BackendBlogModel::getCategories(true);

		// multiple categories?
		if(count($categories) > 1)
		{
			// create form
			$frm = new BackendForm('filter', null, 'get', false);

			// create element
			$frm->addDropdown('category', $categories, $this->categoryId);
			$frm->getField('category')->setDefaultElement('');

			// parse the form
			$frm->parse($this->tpl);
		}

		// parse category
		if(!empty($this->category)) $this->tpl->assign('filterCategory', $this->category);
	}
}
