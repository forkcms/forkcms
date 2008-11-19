<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package			html
 * @subpackage		datagrid
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			1.0.0
 */


/** SpoonDatabase class */
require_once 'spoon/database/database.php';


/**
 * This class is the base class for sources used with datagrids
 *
 * @package			html
 * @subpackage		datagrid
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			1.0.0
 */
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


/**
 * This class is used for datagrids based on array sources
 *
 * @package			html
 * @subpackage		datagrid
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			1.0.0
 */
class SpoonDataGridSourceArray extends SpoonDataGridSource
{
	/**
	 * Static ordering (for compare method)
	 *
	 * @var	string
	 */
	public static $order;


	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	array $array
	 */
	public function __construct(array $array)
	{
		 // set data
		 $this->data = (array) $array;

		// set number of results
		$this->setNumResults();
	}


	/**
	 * Apply the sorting method
	 *
	 * @return	int
	 * @param	array $firstArray
	 * @param	array $secondArray
	 */
	public static function applySorting($firstArray, $secondArray)
	{
		if($firstArray[self::$order] < $secondArray[self::$order]) return -1;
		elseif($firstArray[self::$order] > $secondArray[self::$order]) return 1;
		else return 0;
	}


	/**
	 * Retrieve the columns
	 *
	 * @return	array
	 */
	public function getColumns()
	{
		if($this->numResults != 0) return array_keys($this->data[0]);
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
		// sorting ?
		if($order !== null)
		{
			// static shizzle
			self::$order = $order;

			// apply sorting
			uasort($this->data, array('SpoonDataGridSourceArray', 'applySorting'));

			// reverse if needed?
			if($sort !== null && $sort == 'desc') $this->data = array_reverse($this->data, true);
		}

		// offset & limit
		if($offset !== null && $limit !== null)
		{
			$this->data = array_slice($this->data, $offset, $limit);
		}

		return $this->data;
	}


	/**
	 * Get the number of results
	 *
	 * @return	int
	 */
	public function getNumResults()
	{
		return $this->numResults;
	}


	/**
	 * Sets the number of results
	 *
	 * @return	void
	 */
	private function setNumResults()
	{
		$this->numResults = (int) count($this->data);
	}
}


/**
 * This class is used for datagrids based on database sources
 *
 * @package			html
 * @subpackage		datagrid
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			1.0.0
 */
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
	 * Custom parameters for the numResults query
	 *
	 * @var	array
	 */
	private $numResultsQueryParameters = array();


	/**
	 * Query to fetch the results
	 *
	 * @var	string
	 */
	private $query;


	/**
	 * Custom parameters for the query
	 *
	 * @var	array
	 */
	private $queryParameters = array();


	/**
	 * Class construtor.
	 *
	 * @return	void
	 * @param	SpoonDatabase $dbConnection
	 * @param	mixed $query
	 * @param	mixed[optional] $numResultsQuery
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
			$aRecord = $this->db->getRecord($query, $this->queryParameters);

			// fetch columns
			return array_keys($aRecord);
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
		return (array) $this->db->getRecords($query, $this->queryParameters);
	}


	/**
	 * Set the number of results
	 *
	 * @return	void
	 */
	private function setNumResults()
	{
		// based on resultsQuery
		if($this->numResultsQuery != '') $this->numResults = (int) $this->db->getVar($this->numResultsQuery, $this->numResultsQueryParameters);

		// based on regular query
		else $this->numResults = (int) $this->db->getNumRows($this->query, $this->queryParameters);
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
		// query with parameters
		if(is_array($query) && count($query) > 1 && isset($query[0]) && isset($query[1]))
		{
			$this->query = str_replace(';', '', (string) $query[0]);
			$this->queryParameters = (array) $query[1];
		}

		// no paramters
		else $this->query = str_replace(';', '', (string) $query);

		// numResults query with parameters
		if(is_array($numResultsQuery) && count($numResultsQuery) > 1 && isset($numResultsQuery[0]) && isset($numResultsQuery[1]))
		{
			$this->numResultsQuery = str_replace(';', '', (string) $numResultsQuery[0]);
			$this->numResultsQueryParameters = (array) $numResultsQuery[1];
		}

		// no paramters
		else $this->numResultsQuery = (string) $numResultsQuery;

		// set num results
		$this->setNumResults();
	}
}

?>