<?php

/**
 * This action is used to export statistics by mailing ID
 *
 * @package		backend
 * @subpackage	form_builder
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class BackendFormBuilderExportData extends BackendBaseAction
{
	/**
	 * The filter
	 *
	 * @var	array
	 */
	private $filter;


	/**
	 * Builds the query for this datagrid
	 *
	 * @return	array		An array with two arguments containing the query and its parameters.
	 */
	private function buildQuery()
	{
		// init var
		$parameters = array($this->id);

		// start query, as you can see this query is build in the wrong place, because of the filter it is a special case
		// wherin we allow the query to be in the actionfile itself
		$query = 'SELECT i.*, UNIX_TIMESTAMP(i.sent_on) AS sent_on, d.*
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

		// new query
		return array($query, $parameters);
	}


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && BackendFormBuilderModel::exists($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// set filter
			$this->setFilter();

			// fetch query and parameters
			list($query, $parameters) = $this->buildQuery();

			// get the data
			$data = BackendModel::getDB()->getRecords($query, $parameters);
			$columnHeaders = array();
			$rows = array();

			// reformat data
			foreach($data as $row)
			{
				if(!isset($rows[$row['sent_on']]))
				{
					$rows[$row['sent_on']]['session_id'] = $row['session_id'];
					$rows[$row['sent_on']]['sent_on'] = SpoonDate::getDate('Y-m-d H:i:s', $row['sent_on'], BackendLanguage::getInterfaceLanguage());
				}

				// add
				$rows[$row['sent_on']][$row['label']] = unserialize($row['value']);

				// add into headers if needed
				if(!in_array($row['label'], $columnHeaders)) $columnHeaders[] = $row['label'];
			}

			// sort, so oldest items are below
			ksort($rows);

			// we should loop all the rows to see if all columsn are available
			foreach($rows as &$row)
			{
				foreach($columnHeaders as $header)
				{
					if(!isset($row[$header]))
					{
						$row[$header] = null;
					}
				}
			}

			// remove the keys
			$rows = array_values($rows);

			// set headers for download
			$headers[] = 'Content-type: application/csv; charset=utf-8';
			$headers[] = 'Content-Disposition: attachment; filename="' . date('Ymd_His') . '.csv"';
			$headers[] = 'Pragma: no-cache';

			// overwrite the headers
			SpoonHTTP::setHeaders($headers);

			// output
			if(!empty($rows)) echo SpoonFileCSV::arrayToString($rows);

			// exit here
			exit;
		}

		// no item found, throw an exceptions, because somebody is fucking with our url
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}


	/**
	 * Sets the filter based on the $_GET array.
	 *
	 * @return	void
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

?>