<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the data-action it will display the overview of sent data
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendFormBuilderData extends BackendBaseActionIndex
{
	/**
	 * Filter variables
	 *
	 * @var	array
	 */
	private $filter;

	/**
	 * Form id.
	 *
	 * @var	int
	 */
	private $id;

	/**
	 * Builds the query for this datagrid
	 *
	 * @return array An array with two arguments containing the query and its parameters.
	 */
	private function buildQuery()
	{
		$parameters = array($this->id);

		// start query, as you can see this query is build in the wrong place, because of the filter it is a special case
		// wherin we allow the query to be in the actionfile itself
		$query =
			'SELECT i.id, UNIX_TIMESTAMP(i.sent_on) AS sent_on
			 FROM forms_data AS i
			 WHERE i.form_id = ?';

		// add start date
		if($this->filter['start_date'] !== '')
		{
			// explode date parts
			$chunks = explode('/', $this->filter['start_date']);

			// add condition
			$query .= ' AND i.sent_on >= ?';
			$parameters[] = BackendModel::getUTCDate(null, gmmktime(23, 59, 59, $chunks[1], $chunks[0], $chunks[2]));
		}

		// add end date
		if($this->filter['end_date'] !== '')
		{
			// explode date parts
			$chunks = explode('/', $this->filter['end_date']);

			// add condition
			$query .= ' AND i.sent_on <= ?';
			$parameters[] = BackendModel::getUTCDate(null, gmmktime(23, 59, 59, $chunks[1], $chunks[0], $chunks[2]));
		}

		// new query
		return array($query, $parameters);
	}

	/**
	 * Execute the action
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && BackendFormBuilderModel::exists($this->id))
		{
			parent::execute();
			$this->setFilter();
			$this->loadForm();
			$this->getData();
			$this->loadDataGrid();
			$this->parse();
			$this->display();
		}

		// no item found, throw an exceptions, because somebody is fucking with our url
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}

	/**
	 * Get the data
	 */
	private function getData()
	{
		$this->record = BackendFormBuilderModel::get($this->id);
	}

	/**
	 * Load the datagrids
	 */
	private function loadDataGrid()
	{
		list($query, $parameters) = $this->buildQuery();

		// create datagrid
		$this->dataGrid = new BackendDataGridDB($query, $parameters);

		// overrule default URL
		$this->dataGrid->setURL(BackendModel::createURLForAction(null, null, null, array('offset' => '[offset]', 'order' => '[order]', 'sort' => '[sort]', 'start_date' => $this->filter['start_date'], 'end_date' => $this->filter['end_date']), false) . '&amp;id=' . $this->id);

		// sorting columns
		$this->dataGrid->setSortingColumns(array('sent_on'), 'sent_on');
		$this->dataGrid->setSortParameter('desc');

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('data_details'))
		{
			// set colum URLs
			$this->dataGrid->setColumnURL('sent_on', BackendModel::createURLForAction('data_details', null, null, array('start_date' => $this->filter['start_date'], 'end_date' => $this->filter['end_date']), false) . '&amp;id=[id]');

			// add edit column
			$this->dataGrid->addColumn('details', null, BL::getLabel('Details'), BackendModel::createURLForAction('data_details', null, null, array('start_date' => $this->filter['start_date'], 'end_date' => $this->filter['end_date'])) . '&amp;id=[id]', BL::getLabel('Details'));
		}

		// date
		$this->dataGrid->setColumnFunction(array('BackendFormBuilderModel', 'calculateTimeAgo'), '[sent_on]', 'sent_on', false);
		$this->dataGrid->setColumnFunction('ucfirst', '[sent_on]', 'sent_on', false);

		// add the multicheckbox column
		$this->dataGrid->setMassActionCheckboxes('checkbox', '[id]');

		// mass action
		$ddmMassAction = new SpoonFormDropdown('action', array('delete' => BL::getLabel('Delete')), 'delete');
		$ddmMassAction->setOptionAttributes('delete', array('data-message-id' => 'confirmDelete'));
		$this->dataGrid->setMassAction($ddmMassAction);
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		$this->frm = new BackendForm('filter', BackendModel::createURLForAction() . '&amp;id=' . $this->id, 'get');
		$this->frm->addDate('start_date', $this->filter['start_date']);
		$this->frm->addDate('end_date', $this->filter['end_date']);

		// manually parse fields
		$this->frm->parse($this->tpl);
	}

	/**
	 * Parse the datagrid and the reports
	 */
	protected function parse()
	{
		parent::parse();

		// datagrid
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);

		// form info
		$this->tpl->assign('name', $this->record['name']);
		$this->tpl->assign('id', $this->record['id']);
		$this->tpl->assignArray($this->filter);
	}

	/**
	 * Sets the filter based on the $_GET array.
	 */
	private function setFilter()
	{
		// start date is set
		if(isset($_GET['start_date']) && $_GET['start_date'] != '')
		{
			// redefine
			$startDate = (string) $_GET['start_date'];

			// explode date parts
			$chunks = explode('/', $startDate);

			// valid date
			if(count($chunks) == 3 && checkdate((int) $chunks[1], (int) $chunks[0], (int) $chunks[2])) $this->filter['start_date'] = $startDate;

			// invalid date
			else $this->filter['start_date'] = '';
		}

		// not set
		else $this->filter['start_date'] = '';

		// end date is set
		if(isset($_GET['end_date']) && $_GET['end_date'] != '')
		{
			// redefine
			$endDate = (string) $_GET['end_date'];

			// explode date parts
			$chunks = explode('/', $endDate);

			// valid date
			if(count($chunks) == 3 && checkdate((int) $chunks[1], (int) $chunks[0], (int) $chunks[2])) $this->filter['end_date'] = $endDate;

			// invalid date
			else $this->filter['end_date'] = '';
		}

		// not set
		else $this->filter['end_date'] = '';
	}
}
