<?php

/**
 * This is the index-action (default), it will display the overview
 *
 * @package		backend
 * @subpackage	faq
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.1
 */
class BackendFaqIndex extends BackendBaseActionIndex
{
	/**
	 * The datagrids
	 *
	 * @var	array
	 */
	private $datagrids;


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
		$this->loadDatagrids();

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
	private function loadDatagrids()
	{
		// load all categories
		$categories = BackendFaqModel::getCategories(true);

		// loop categories and create a datagrid for each one
		foreach($categories as $categoryId => $categoryTitle)
		{
			// create datagrid
			$datagrid = new BackendDataGridDB(BackendFaqModel::QRY_DATAGRID_BROWSE, array(BL::getWorkingLanguage(), $categoryId));

			// set attributes
			$datagrid->setAttributes(array('class' => 'datagrid sequenceByDragAndDrop'));

			// disable paging
			$datagrid->setPaging(false);

			// set colum URLs
			$datagrid->setColumnURL('question', BackendModel::createURLForAction('edit') . '&amp;id=[id]');

			// set colums hidden
			$datagrid->setColumnsHidden(array('category_id', 'sequence'));

			// add edit column
			$datagrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::lbl('Edit'));

			// add a column for the handle, so users have something to hold while draging
			$datagrid->addColumn('dragAndDropHandle', null, '<span>' . BL::lbl('Move') . '</span>');

			// make sure the column with the handler is the first one
			$datagrid->setColumnsSequence('dragAndDropHandle');

			// add a class on the handler column, so JS knows this is just a handler
			$datagrid->setColumnAttributes('dragAndDropHandle', array('class' => 'dragAndDropHandle'));

			// our JS needs to know an id, so we can send the new order
			$datagrid->setRowAttributes(array('id' => '[id]'));

			// add datagrid to list
			$this->datagrids[] = array('id' => $categoryId,
									   'title' => $categoryTitle,
									   'content' => $datagrid->getContent());
		}
	}


	/**
	 * Parse the datagrids and the reports
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse datagrids
		if(!empty($this->datagrids)) $this->tpl->assign('datagrids', $this->datagrids);
	}
}

?>