<?php

/**
 * This is the categories-action, it will display the overview of blog categories
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
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
	 * @return	void
	 */
	private function loadDataGrid()
	{
		// create datagrid
		$this->dataGrid = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_CATEGORIES, array('active', BL::getWorkingLanguage()));

		// set headers
		$this->dataGrid->setHeaderLabels(array('num_items' => ucfirst(BL::lbl('Amount'))));

		// sorting columns
		$this->dataGrid->setSortingColumns(array('title', 'num_items'), 'title');

		// set column URLs
		$this->dataGrid->setColumnURL('title', BackendModel::createURLForAction('edit_category') . '&amp;id=[id]');

		// convert the count into a readable and clickable one
		$this->dataGrid->setColumnFunction(array(__CLASS__, 'setClickableCount'), array('[num_items]', BackendModel::createURLForAction('index') . '&amp;category=[id]'), 'num_items', true);

		// add column
		$this->dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit_category') . '&amp;id=[id]', BL::lbl('Edit'));

		// disable paging
		$this->dataGrid->setPaging(false);

		// add attributes, so the inline editing has all the needed data
		$this->dataGrid->setColumnAttributes('title', array('data-id' => '{id:[id]}'));
	}


	/**
	 * Parse & display the page
	 *
	 * @return	void
	 */
	private function parse()
	{
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
	}


	/**
	 * Convert the count in a human readable one.
	 *
	 * @return	string
	 * @param	int $count		The count.
	 * @param	string $link	The link for the count.
	 */
	public static function setClickableCount($count, $link)
	{
		// redefine
		$count = (int) $count;
		$link = (string) $link;
		$return = '';

		if($count > 1) $return = '<a href="' . $link . '">' . $count . ' ' . BL::getLabel('Articles') . '</a>';
		elseif($count == 1) $return = '<a href="' . $link . '">' . $count . ' ' . BL::getLabel('Article') . '</a>';

		return $return;
	}
}

?>