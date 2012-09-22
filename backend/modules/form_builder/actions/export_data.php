<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action is used to export submissions of a form.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendFormBuilderExportData extends BackendBaseAction
{
	/**
	 * CSV column headers.
	 *
	 * @var	array
	 */
	private $columnHeaders = array();

	/**
	 * The filter.
	 *
	 * @var	array
	 */
	private $filter;

	/**
	 * CSV rows.
	 *
	 * @var	array
	 */
	private $rows = array();

	/**
	 * Builds the query for this datagrid.
	 *
	 * @return array		An array with two arguments containing the query and its parameters.
	 */
	private function buildQuery()
	{
		// init var
		$parameters = array($this->id);

		/*
		 * Start query, as you can see this query is build in the wrong place, because of the filter
		 * it is a special case wherin we allow the query to be in the actionfile itself
		 */
		$query =
			'SELECT i.*, UNIX_TIMESTAMP(i.sent_on) AS sent_on, d.*
			 FROM forms_data AS i
			 INNER JOIN forms_data_fields AS d ON i.id = d.data_id
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

		return array($query, $parameters);
	}

	/**
	 * Execute the action.
	 */
	public function execute()
	{
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && BackendFormBuilderModel::exists($this->id))
		{
			parent::execute();
			$this->setFilter();
			$this->setItems();
			BackendCSV::outputCSV(date('Ymd_His') . '.csv', $this->rows, $this->columnHeaders);
		}

		// no item found, redirect to index, because somebody is fucking with our url
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
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

	/**
	 * Fetch data for this form from the database and reformat to csv rows.
	 */
	private function setItems()
	{
		// init header labels
		$lblSessionId = SpoonFilter::ucfirst(BL::lbl('SessionId'));
		$lblSentOn = SpoonFilter::ucfirst(BL::lbl('SentOn'));
		$this->columnHeaders = array($lblSessionId, $lblSentOn);

		// fetch query and parameters
		list($query, $parameters) = $this->buildQuery();

		// get the data
		$records = (array) BackendModel::getDB()->getRecords($query, $parameters);
		$data = array();

		// reformat data
		foreach($records as $row)
		{
			// first row of a submission
			if(!isset($data[$row['data_id']]))
			{
				$data[$row['data_id']][$lblSessionId] = $row['session_id'];
				$data[$row['data_id']][$lblSentOn] = SpoonDate::getDate('Y-m-d H:i:s', $row['sent_on'], BackendLanguage::getWorkingLanguage());
			}

			// value is serialized
			$value = unserialize($row['value']);

			// flatten arrays
			if(is_array($value)) $value = implode(', ', $value);

			// group submissions
			$data[$row['data_id']][$row['label']] = SpoonFilter::htmlentitiesDecode($value, null, ENT_QUOTES);

			// add into headers if not yet added
			if(!in_array($row['label'], $this->columnHeaders)) $this->columnHeaders[] = $row['label'];
		}

		// reorder data so they are in the correct column
		foreach($data as $id => $row)
		{
			foreach($this->columnHeaders as $header)
			{
				// submission has this field so add it
				if(isset($row[$header])) $this->rows[$id][] = $row[$header];

				// submission does not have this field so add a placeholder
				else $this->rows[$id][] = '';
			}
		}

		// remove the keys
		$this->rows = array_values($this->rows);
	}
}
