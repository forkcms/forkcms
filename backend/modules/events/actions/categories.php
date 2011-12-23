<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the categories-action, it will display the overview of events categories
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendEventsCategories extends BackendBaseActionIndex
{
	/**
	 * Execute the action
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
	 */
	private function loadDataGrid()
	{
		// create datagrid
		$this->datagrid = new BackendDataGridDB(BackendEventsModel::QRY_DATAGRID_BROWSE_CATEGORIES, BL::getWorkingLanguage());

		// sorting columns
		$this->datagrid->setSortingColumns(array('title'), 'title');

		// add column
		$this->datagrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit_category') . '&amp;id=[id]', BL::lbl('Edit'));

		// row function
		$this->datagrid->setRowFunction(array('BackendEventsCategories', 'setDefault'), array('[id]'));

		// disable paging
		$this->datagrid->setPaging(false);

		// add attributes, so the inline editing has all the needed data
		$this->datagrid->setColumnAttributes('title', array('data-id' => '{id:[id]}'));
	}

	/**
	 * Parse & display the page
	 */
	private function parse()
	{
		$this->tpl->assign('datagrid', ($this->datagrid->getNumResults() != 0) ? $this->datagrid->getContent() : false);
	}

	/**
	 * Set class on row with the default class
	 *
	 * @param int $id The id of the category.
	 * @param array $rowAttributes The current row attributes.
	 * @return array
	 */
	public static function setDefault($id, $rowAttributes)
	{
		// is this the default category?
		if(BackendModel::getModuleSetting('events', 'default_category_' . BL::getWorkingLanguage(), null) == $id)
		{
			// class already defined?
			if(isset($rowAttributes['class'])) $rowAttributes['class'] .= ' isDefault';

			// set class
			else $rowAttributes['class'] = 'isDefault';

			// return
			return $rowAttributes;
		}
	}
}
