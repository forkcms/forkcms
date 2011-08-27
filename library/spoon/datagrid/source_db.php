<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package		spoon
 * @subpackage	datagrid
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		1.0.0
 */


/**
 * This class is used for datagrids based on database sources
 *
 * @package		spoon
 * @subpackage	datagrid
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		1.0.0
 */
class SpoonDatagridSourceDB extends SpoonDatagridSource
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
	 * @param	SpoonDatabase $dbConnection			The database connection.
	 * @param	string $query						The query to execute.
	 * @param	string[optional] $numResultsQuery	The query to use to retrieve the number of results.
	 */
	public function __construct(SpoonDatabase $dbConnection, $query, $numResultsQuery = null)
	{
		// database connection
		$this->db = $dbConnection;

		// set queries
		$this->setQuery($query, $numResultsQuery);
	}


	/**
	 * Get the list of columns.
	 *
	 * @return	array
	 */
	public function getColumns()
	{
		// has results
		if($this->numResults != 0)
		{
			// build query
			switch($this->db->getDriver())
			{
				case 'mysql':
					$query = (substr_count($this->query, 'LIMIT ') > 0) ? $this->query : $this->query . ' LIMIT 1';
				break;

				default:
					throw new SpoonDataGridException('No datagrid support has been written for this database backend (' . $this->db->getDriver() . ')');
				break;
			}

			// fetch record
			$record = $this->db->getRecord($query, $this->queryParameters);

			// fetch columns
			return array_keys($record);
		}
	}


	/**
	 * Fetch the data as an array.
	 *
	 * @return	array
	 * @param	int[optional] $offset		The offset to start from.
	 * @param	int[optional] $limit		The maximum number of items to retrieve.
	 * @param	string[optional] $order		The column to order on.
	 * @param	string[optional] $sort		The sorting method.
	 */
	public function getData($offset = null, $limit = null, $order = null, $sort = null)
	{
		// fetch query
		$query = $this->query;

		// order & sort defined
		if($order !== null && $sort !== null) $query .= ' ORDER BY ' . $order . ' ' . $sort;

		// offset & limit defined
		if($offset !== null && $limit !== null) $query .= ' LIMIT ' . $offset . ', ' . $limit;

		// fetch data
		return (array) $this->db->getRecords($query, $this->queryParameters);
	}


	/**
	 * Set the number of results.
	 */
	private function setNumResults()
	{
		// based on resultsQuery
		if($this->numResultsQuery != '') $this->numResults = (int) $this->db->getVar($this->numResultsQuery, $this->numResultsQueryParameters);

		// based on regular query
		else $this->numResults = (int) $this->db->getNumRows($this->query, $this->queryParameters);
	}


	/**
	 * Set the queries.
	 *
	 * @param	string $query						The query to execute.
	 * @param	string[optional] $numResultsQuery	The query to use to retrieve the number of results.
	 */
	private function setQuery($query, $numResultsQuery = null)
	{
		// query with parameters
		if(is_array($query) && count($query) > 1 && isset($query[0]) && isset($query[1]))
		{
			// remove the trailing semicolon(s) to enable adding "ORDER BY" etc.
			$this->query = preg_replace('/;+\s*$/', '', (string) $query[0]);
			$this->queryParameters = (array) $query[1];
		}

		// no parameters
		else $this->query = preg_replace('/;+\s*$/', '', (string) $query);

		// numResults query with parameters
		if(is_array($numResultsQuery) && count($numResultsQuery) > 1 && isset($numResultsQuery[0]) && isset($numResultsQuery[1]))
		{
			$this->numResultsQuery = preg_replace('/;+\s*$/', '', (string) $numResultsQuery[0]);
			$this->numResultsQueryParameters = (array) $numResultsQuery[1];
		}

		// no paramters
		else $this->numResultsQuery = (string) $numResultsQuery;

		// set num results
		$this->setNumResults();
	}
}
