<?php

/**
 * This is the index-action (default), it will display the overview
 *
 * @package		backend
 * @subpackage	faq
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @since		2.1
 */
class BackendFaqIndex extends BackendBaseActionIndex
{
	/**
	 * The datagrids
	 *
	 * @var	array
	 */
	private $dataGrids;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load the datagrids
		$this->loadDataGrids();

		// parse the datagrids
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the datagrids
	 *
	 * @return	void
	 */
	private function loadDataGrids()
	{
		// load all categories
		$categories = BackendFaqModel::getCategories();

		// run over categories and create datagrid for each one
		foreach($categories as $category)
		{
			// create datagrid
			$dataGrid = new BackendDataGridDB(BackendFaqModel::QRY_DATAGRID_BROWSE, array(BL::getWorkingLanguage(), $category['id']));

			// set attributes
			$dataGrid->setAttributes(array('class' => 'dataGrid sequenceByDragAndDrop'));

			// disable paging
			$dataGrid->setPaging(false);

			// set colum URLs
			$dataGrid->setColumnURL('question', BackendModel::createURLForAction('edit') . '&amp;id=[id]');

			// set colums hidden
			$dataGrid->setColumnsHidden(array('category_id', 'sequence'));

			// add edit column
			$dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::lbl('Edit'));

			// add a column for the handle, so users have something to hold while draging
			$dataGrid->addColumn('dragAndDropHandle', null, '<span>' . BL::lbl('Move') . '</span>');

			// make sure the column with the handler is the first one
			$dataGrid->setColumnsSequence('dragAndDropHandle');

			// add a class on the handler column, so JS knows this is just a handler
			$dataGrid->setColumnAttributes('dragAndDropHandle', array('class' => 'dragAndDropHandle'));

			// our JS needs to know an id, so we can send the new order
			$dataGrid->setRowAttributes(array('id' => '[id]'));

			// add datagrid to list
			$this->dataGrids[] = array('id' => $category['id'],
									   'name' => $category['name'],
									   'content' => $dataGrid->getContent());
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
		if(!empty($this->dataGrids)) $this->tpl->assign('dataGrids', $this->dataGrids);
	}
}

?>