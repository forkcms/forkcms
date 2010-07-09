<?php

/**
 * BackendBlogCategories
 * This is the categories-action, it will display the overview of blog categories
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendBlogCategories extends BackendBaseActionIndex
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

		// load datagrids
		$this->loadDataGrid();

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
	private function loadDataGrid()
	{
		// create datagrid
		$this->datagrid = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_CATEGORIES, BL::getWorkingLanguage());

		// sorting columns
		$this->datagrid->setSortingColumns(array('name'), 'name');

		// add column
		$this->datagrid->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit_category') .'&amp;id=[id]', BL::getLabel('Edit'));

		// disable paging
		$this->datagrid->setPaging(false);

		// add attributes, so the inline editing has all the needed data
		$this->datagrid->setColumnAttributes('name', array('rel' => '{id:[id]}'));
	}


	/**
	 * Parse & display the page
	 *
	 * @return	void
	 */
	private function parse()
	{
		$this->tpl->assign('datagrid', ($this->datagrid->getNumResults() != 0) ? $this->datagrid->getContent() : false);
	}
}

?>