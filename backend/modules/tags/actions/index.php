<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the index-action, it will display the overview of tags
 *
 * @author Dave Lens <dave.lens@netlash.com>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendTagsIndex extends BackendBaseActionIndex
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->loadDataGrid();
		$this->parse();
		$this->display();
	}

	/**
	 * Loads the datagrids
	 */
	private function loadDataGrid()
	{
		// create datagrid
		$this->dataGrid = new BackendDataGridDB(BackendTagsModel::QRY_DATAGRID_BROWSE, BL::getWorkingLanguage());

		// header labels
		$this->dataGrid->setHeaderLabels(array('tag' => ucfirst(BL::lbl('Name')), 'num_tags' => ucfirst(BL::lbl('Amount'))));

		// sorting columns
		$this->dataGrid->setSortingColumns(array('tag', 'num_tags'), 'num_tags');
		$this->dataGrid->setSortParameter('desc');

		// add the multicheckbox column
		$this->dataGrid->setMassActionCheckboxes('checkbox', '[id]');

		// add mass action dropdown
		$ddmMassAction = new SpoonFormDropdown('action', array('delete' => BL::lbl('Delete')), 'delete');
		$ddmMassAction->setOptionAttributes('delete', array('message-id' => 'confirmDelete'));
		$this->dataGrid->setMassAction($ddmMassAction);

		// add column
		$this->dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::lbl('Edit'));

		// add attributes, so the inline editing has all the needed data
		$this->dataGrid->setColumnAttributes('tag', array('data-id' => '{id:[id]}'));
	}

	/**
	 * Parse & display the page
	 */
	private function parse()
	{
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
	}
}
