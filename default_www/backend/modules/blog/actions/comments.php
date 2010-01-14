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

		// load datagrids

		// parse

			// actieve tab

			// nummertjes op actieve tabs

		// display the page
		$this->display();
	}

	private function loadDataGrids()
	{
		// published comments
		$this->dgPublished = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_COMMENTS, 'published');
		$this->dgPublished->setHeaderLabels(array('created_on' => ucfirst(BL::getLabel('Date')), 'author' => ucfirst(BL::getLabel('Author')), 'text' => ucfirst(BL::getLabel('Comment'))));
		$this->dgPublished->setColumnFunction('nl2br', '[text]', 'text', true); // @todo write nl2p
		$this->dgPublished->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), '[created_on]', 'created_on', true);
		$this->dgPublished->setActiveTab('tabPublished');
		$this->dgPublished->setPagingLimit(5);

		// assign datagrid & num results
		$this->tpl->assign('dgPublished', ($this->dgPublished->getNumResults() != 0) ? $this->dgPublished->getContent() : false);
		$this->tpl->assign('numPublished', $this->dgPublished->getNumResults());


		// published comments
		$this->dgModeration = new BackendDataGridDB('SELECT id, UNIX_TIMESTAMP(created_on) AS created_on, author, text FROM blog_comments WHERE status = ?;', 'unmoderated');

		$this->dgModeration->setHeaderLabels(array('created_on' => ucfirst(BL::getLabel('Date')), 'author' => ucfirst(BL::getLabel('Author')), 'text' => ucfirst(BL::getLabel('Comment'))));
		$this->dgModeration->setColumnFunction('nl2br', '[text]', 'text', true); // @todo write nl2p
		$this->dgModeration->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), '[created_on]', 'created_on', true);
		$this->dgModeration->setActiveTab('tabModeration');
		$this->dgModeration->setPagingLimit(5);

		// assign datagrid & num results
		$this->tpl->assign('dgModeration', ($this->dgModeration->getNumResults() != 0) ? $this->dgModeration->getContent() : false);
		$this->tpl->assign('numModeration', $this->dgModeration->getNumResults());





		$this->tpl->assign('numSpam', 0);

	}
}

?>