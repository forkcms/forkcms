<?php

/**
 * Deze file gaat alle mogelijke resources requiren.
 * per source worden de afhaneklijkheden ook gerquired
 * vb database
 */

/** SpoonDatabase class */
require_once 'spoon/database/database.php';


class SpoonDataGridSource
{
	/**
	 * Final data
	 *
	 * @var	array
	 */
	protected $data = array();


	/**
	 * Number of results
	 *
	 * @var	int
	 */
	protected $numResults = 0;


	/**
	 * Fetch the number of results
	 *
	 * @return	int
	 */
	public function getNumResults()
	{
		return $this->numResults;
	}
}



class SpoonDataGridSourceDB extends SpoonDataGridSource
{
	/**
	 * SpoonDatabase instance
	 *
	 * @var	SpoonDatabase
	 */
	private $db;


	/**
	 * Query to calculate the number of results
	 *
	 * @var	string
	 */
	private $numResultsQuery;


	/**
	 * Query to fetch the results
	 *
	 * @var	string
	 */
	private $query;


	/**
	 * Class construtor.
	 *
	 * @return	void
	 * @param	SpoonDatabase $dbConnection
	 * @param	string $query
	 * @param	string[optional] $numResultsQuery
	 *
	 * @todo	Davy - Hoe zit het met de parameters ? ? ?
	 */
	public function __construct(SpoonDatabase $dbConnection, $query, $numResultsQuery = null)
	{
		// database connection
		$this->db = $dbConnection;

		// set queries
		$this->setQuery($query, $numResultsQuery);
	}


	/**
	 * Get the list of columns
	 *
	 * @return	array
	 */
	public function getColumns()
	{
		// has results
		if($this->numResults != 0)
		{
			// build query
			switch($this->db->getBackend())
			{
				case 'mysql':
				case 'mysqli':
					$query = $this->query .' LIMIT 1';
				break;

				default:
					throw new SpoonDataGridException('No datagrid support has been written for this database backend ('. $this->db->getBackend() .')');
				break;
			}

			// fetch record
			$aRecord = $this->db->getRecord($query);

			// fetch columns
			foreach($aRecord as $label => $value) $this->columns[] = $label;

			// fetch em
			return $this->columns;
		}
	}



	/**
	 * Fetch the data as an array
	 *
	 * @return	array
	 * @param	int[optional] $offset
	 * @param	int[optional] $limit
	 * @param	string[optional] $order
	 * @param	string[optional] $sort
	 */
	public function getData($offset = null, $limit = null, $order = null, $sort = null)
	{
		// fetch query
		$query = $this->query;

		// order & sort defined
		if($order !== null && $sort !== null) $query .= " ORDER BY $order $sort";

		// offset & limit defined
		if($offset !== null && $limit !== null) $query .= " LIMIT $offset, $limit";

		// fetch data
		return (array) $this->db->getRecords($query);
	}


	/**
	 * Set the number of results
	 *
	 * @return	void
	 */
	private function setNumResults()
	{
		// based on resultsQuery
		if($this->numResultsQuery != null) $this->numResults = (int) $this->db->getVar($this->numResultsQuery);

		// based on regular query
		else $this->numResults = (int) $this->db->getNumRows($this->query);
	}


	/**
	 * Set the queries
	 *
	 * @return	void
	 * @param	string $query
	 * @param	string[optional] $numResultsQuery
	 */
	private function setQuery($query, $numResultsQuery = null)
	{
		// set both queries
		$this->query = rtrim((string) $query, ';');
		$this->numResultsQuery = rtrim((string) $numResultsQuery, ';');

		// set num results
		$this->setNumResults();
	}
}






// @todo: Davy - Eerst zien dat de rest werkt.
class SpoonDataGridSourceArray
{
	private $data = array();

	private $numResults = 0;

	public function __construct($array)
	{
		 // set data
		 $this->data = (array) $array;

		// set number of results
		$this->setNumResults();
	}


	public function getColumns()
	{
		// vb array
		$aRecord['id'] = 1;
		$aRecord['name'] = 'Erik Bauffman';
		$aRecord['email'] = 'info@erikbauffman.be';

		// return values
		return array('id', 'name', 'email');
	}


	public function getData()
	{
		// array_slice is je vriend!
		return $this->data;
	}


	public function getNumResults()
	{
		return $this->numResults;
	}


	private function setNumResults()
	{
		$this->numResults = (int) count($this->data);
	}
}

?>