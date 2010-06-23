<?php

/**
 * BackendBlogComments
 *
 * This is the comments-action , it will display the overview of blog comments
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendBlogComments extends BackendBaseActionIndex
{
	/**
	 * Datagrids
	 *
	 * @var	BackendDataGridDB
	 */
	private $dgPublished, $dgModeration, $dgSpam;


	/**
	 * Add postdata into the comment
	 *
	 * @return	string
	 * @param 	string $text	The comment.
	 * @param	string $title	The title for the blogarticle.
	 * @param	string $URL		The URL for the blogarticle.
	 */
	public static function addPostData($text, $title, $URL)
	{
		// reset URL
		$URL = BackendModel::getURLForBlock('blog', 'detail') .'/'. (string) $URL;

		// build HTML
		return '<p><em>'. sprintf(BL::getMessage('CommentOnWithURL'), $URL, $title) .'</em></p>'."\n". (string) $text;
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

		// load datagrids
		$this->loadDataGrids();

		// parse page
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Loads the datagrids
	 *
	 * @return void
	 */
	private function loadDataGrids()
	{
		/*
		 * Datagrid for the published comments.
		 */
		$this->dgPublished = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_COMMENTS, 'published');

		// active tab
		$this->dgPublished->setActiveTab('tabPublished');

		// num items per page
		$this->dgPublished->setPagingLimit(30);

		// header labels
		$this->dgPublished->setHeaderLabels(array('created_on' => ucfirst(BL::getLabel('Date')), 'author' => ucfirst(BL::getLabel('Author')), 'text' => ucfirst(BL::getLabel('Comment'))));

		// add the multicheckbox column
		$this->dgPublished->addColumn('checkbox', '<div class="checkboxHolder"><input type="checkbox" name="toggleChecks" value="toggleChecks" /></div>', '<div class="checkboxHolder"><input type="checkbox" name="id[]" value="[id]" class="inputCheckbox" /></div>');
		$this->dgPublished->setColumnsSequence('checkbox');

		// assign column functions
		$this->dgPublished->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), '[created_on]', 'created_on', true);
		$this->dgPublished->setColumnFunction(array('BackendDataGridFunctions', 'cleanupPlaintext'), '[text]', 'text', true);
		$this->dgPublished->setColumnFunction(array('BackendBlogComments', 'addPostData'), array('[text]', '[post_title]', '[post_url]'), 'text', true);

		// sorting
		$this->dgPublished->setSortingColumns(array('created_on', 'text'), 'text');

		// hide columns
		$this->dgPublished->setColumnsHidden('post_id', 'post_title', 'post_url');

		// add mass action dropdown
		$ddmMassAction = new SpoonFormDropdown('action', array('moderation' => BL::getLabel('MoveToModeration'), 'spam' => BL::getLabel('MoveToSpam'), 'delete' => BL::getLabel('Delete')), 'spam');
		$this->dgPublished->setMassAction($ddmMassAction);

		// datagrid for the comments that are awaiting moderation
		$this->dgModeration = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_COMMENTS, 'moderation');

		// active tab
		$this->dgModeration->setActiveTab('tabModeration');

		// num items per page
		$this->dgModeration->setPagingLimit(30);

		// header labels
		$this->dgModeration->setHeaderLabels(array('created_on' => ucfirst(BL::getLabel('Date')), 'author' => ucfirst(BL::getLabel('Author')), 'text' => ucfirst(BL::getLabel('Comment'))));

		// add the multicheckbox column
		$this->dgModeration->addColumn('checkbox', '<div class="checkboxHolder"><input type="checkbox" name="toggleChecks" value="toggleChecks" /></div>', '<div class="checkboxHolder"><input type="checkbox" name="id[]" value="[id]" class="inputCheckbox" /></div>');
		$this->dgModeration->setColumnsSequence('checkbox');

		// assign column functions
		$this->dgModeration->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), '[created_on]', 'created_on', true);
		$this->dgModeration->setColumnFunction(array('BackendDataGridFunctions', 'cleanupPlaintext'), '[text]', 'text', true);
		$this->dgModeration->setColumnFunction(array('BackendBlogComments', 'addPostData'), array('[text]', '[post_title]', '[post_url]'), 'text', true);

		// sorting
		$this->dgModeration->setSortingColumns(array('created_on', 'text'), 'text');

		// hide columns
		$this->dgModeration->setColumnsHidden('post_id', 'post_title', 'post_url');

		// add mass action dropdown
		$ddmMassAction = new SpoonFormDropdown('action', array('published' => BL::getLabel('MoveToPublished'), 'spam' => BL::getLabel('MoveToSpam'), 'delete' => BL::getLabel('Delete')), 'published');
		$this->dgModeration->setMassAction($ddmMassAction);

		/*
		 * Datagrid for the comments that are marked as spam
		 */
		$this->dgSpam = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_COMMENTS, 'spam');

		// active tab
		$this->dgSpam->setActiveTab('tabSpam');

		// num items per page
		$this->dgSpam->setPagingLimit(30);

		// header labels
		$this->dgSpam->setHeaderLabels(array('created_on' => ucfirst(BL::getLabel('Date')), 'author' => ucfirst(BL::getLabel('Author')), 'text' => ucfirst(BL::getLabel('Comment'))));

		// add the multicheckbox column
		$this->dgSpam->addColumn('checkbox', '<div class="checkboxHolder"><input type="checkbox" name="toggleChecks" value="toggleChecks" /></div>', '<div class="checkboxHolder"><input type="checkbox" name="id[]" value="[id]" class="inputCheckbox" /></div>');
		$this->dgSpam->setColumnsSequence('checkbox');

		// assign column functions
		$this->dgSpam->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), '[created_on]', 'created_on', true);
		$this->dgSpam->setColumnFunction(array('BackendDataGridFunctions', 'cleanupPlaintext'), '[text]', 'text', true);
		$this->dgSpam->setColumnFunction(array('BackendBlogComments', 'addPostData'), array('[text]', '[post_title]', '[post_url]'), 'text', true);

		// sorting
		$this->dgSpam->setSortingColumns(array('created_on', 'text'), 'text');

		// hide columns
		$this->dgSpam->setColumnsHidden('post_id', 'post_title', 'post_url');

		// add mass action dropdown
		$ddmMassAction = new SpoonFormDropdown('action', array('published' => BL::getLabel('MoveToPublished'), 'moderation' => BL::getLabel('MoveToModeration'), 'delete' => BL::getLabel('Delete')), 'published');
		$this->dgSpam->setMassAction($ddmMassAction);
	}


	/**
	 * Parse & display the page
	 *
	 * @return	void
	 */
	private function parse()
	{
		// published datagrid and num results
		$this->tpl->assign('dgPublished', ($this->dgPublished->getNumResults() != 0) ? $this->dgPublished->getContent() : false);
		$this->tpl->assign('numPublished', $this->dgPublished->getNumResults());

		// moderaton datagrid and num results
		$this->tpl->assign('dgModeration', ($this->dgModeration->getNumResults() != 0) ? $this->dgModeration->getContent() : false);
		$this->tpl->assign('numModeration', $this->dgModeration->getNumResults());

		// spam datagrid and num results
		$this->tpl->assign('dgSpam', ($this->dgSpam->getNumResults() != 0) ? $this->dgSpam->getContent() : false);
		$this->tpl->assign('numSpam', $this->dgSpam->getNumResults());
	}
}

?>