<?php

/**
 * BackendBlogIndex
 *
 * This is the index-action (default), it will display the overview of blog posts
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author 		Davy Hellemans <davy@netlash.com>
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


	private function loadDataGrids()
	{
		$this->dgRecent = new BackendDataGridDB('SELECT * FROM blog_posts ORDER BY edited_on DESC LIMIT 4;');

		$this->dgPosts = new BackendDataGridDB('SELECT * FROM blog_posts;');
	}

	private function parse()
	{
		$this->tpl->assign('dgRecent', ($this->dgRecent->getNumResults() != 0) ? $this->dgRecent->getContent() : false);
		$this->tpl->assign('dgPosts', ($this->dgRecent->getNumResults() != 0) ? $this->dgPosts->getContent() : false);
	}
}

?>