<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the index-action (default), it will display the overview
 *
 * @author Lester Lievens <lester@netlash.com>
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
	 */
	public function execute()
	{
		parent::execute();
		$this->loadDataGrids();
		$this->parse();
		$this->display();
	}

	/**
	 * Load the datagrids
	 */
	private function loadDataGrids()
	{
		$categories = BackendFaqModel::getCategories();

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
			$this->dataGrids[] = array(
				'id' => $category['id'],
				'name' => $category['name'],
				'content' => $dataGrid->getContent()
			);
		}
	}

	/**
	 * Parse the datagrids and the reports
	 */
	private function parse()
	{
		if(!empty($this->dataGrids))
		{
			$this->tpl->assign('dataGrids', $this->dataGrids);
		}
	}
}
