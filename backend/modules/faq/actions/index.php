<?php

/**
 * This is the index-action (default), it will display the overview
 *
 * @package		backend
 * @subpackage	faq
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @author		Davy Van Vooren <davy.vanvooren@netlash.com>
 * @since		2.1
 */
class BackendFaqIndex extends BackendBaseActionIndex
{
	/**
	 * The dataGrids
	 *
	 * @var	array
	 */
	private $dataGrids;
	private $emptyDatagrid;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load dataGrids
		$this->loadDatagrids();

		// parse page
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Loads the dataGrids
	 *
	 * @return	void
	 */
	private function loadDatagrids()
	{
		// load all categories
		$categories = BackendFaqModel::getCategories(true);

		// loop categories and create a dataGrid for each one
		foreach($categories as $categoryId => $categoryTitle)
		{
			// create dataGrid
			$dataGrid = new BackendDataGridDB(BackendFaqModel::QRY_DATAGRID_BROWSE, array(BL::getWorkingLanguage(), $categoryId));

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
			$dataGrid->setColumnAttributes('question', array('class' => 'title'));
			$dataGrid->setColumnAttributes('dragAndDropHandle', array('class' => 'dragAndDropHandle'));

			// our JS needs to know an id, so we can send the new order
			$dataGrid->setRowAttributes(array('id' => '[id]'));

			// add dataGrid to list
			$this->dataGrids[] = array('id' => $categoryId,
									   'title' => $categoryTitle,
									   'content' => $dataGrid->getContent());
		}

		// set empty datagrid
		$this->emptyDatagrid = new BackendDataGridArray(array(array('dragAndDropHandle' => '', 'question' => BL::msg('NoQuestionInCategory'), 'edit' => '')));
		$this->emptyDatagrid->setAttributes(array('class' => 'dataGrid sequenceByDragAndDrop emptyGrid'));
		$this->emptyDatagrid->setHeaderLabels(array('edit' => null, 'dragAndDropHandle' => null));
	}


	/**
	 * Parse the dataGrids and the reports
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse dataGrids
		if(!empty($this->dataGrids)) $this->tpl->assign('dataGrids', $this->dataGrids);
		$this->tpl->assign('emptyDatagrid', $this->emptyDatagrid->getContent());
	}
}

?>