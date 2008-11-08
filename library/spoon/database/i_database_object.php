<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package			database
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			1.0.0
 */


interface iSpoonDatabaseObject
{
	/**
	 * Constructor
	 *
	 * @return	void
	 * @param	string $hostname
	 * @param	string $username
	 * @param	string $password
	 * @param	string $database
	 * @param	string[optional] $charset
	 */
	public function __construct($hostname, $username, $password, $database, $charset = 'latin1');


	/**
	 * Destructor
	 *
	 * @return	void
	 */
	public function __destruct();


	/**
	 * Open a new connection to the MySQL server
	 *
	 * @return	void
	 */
	public function connect();


	/**
	 * Builds a query for deleting records & returns the affected rows
	 *
	 * @return	int
	 * @param	string $table
	 * @param	string $where
	 * @param	mixed[optional] $parameters
	 */
	public function delete($table, $where, $parameters = array());


	/**
	 * Closes a previously opened database connection
	 *
	 * @return	void
	 */
	public function disconnect();


	/**
	 * Drops one or more tables
	 *
	 * @return	void
	 * @param	mixed $tables
	 */
	public function drop($tables);


	/**
	 * Executes a query & returns the last inserted or the number of affected rows
	 *
	 * @return	int
	 * @param	string $query
	 * @param	mixed[optional] $parameters
	 */
	public function execute($query, $parameters = array());


	/**
	 * Gets a resultcolumn as an array
	 *
	 * @return	array
	 * @param	string $query
	 * @param	mixed[optional] $parameters
	 */
	public function getColumn($query, $parameters = array());


	/**
     * Retrieves the possible ENUM-values
     *
     * @return	array
     * @param	string $table
     * @param	string $field
     */
    public function getEnumValues($table, $field);


	/**
	 * Gets the number of rows in a result
	 *
	 * @return	int
	 * @param	string $query
	 */
	public function getNumRows($query);


	/**
	 * Gets the results as a key-value-pair
	 *
	 * @return	array
	 * @param	string $query
	 * @param	mixed[optional] $parameters
	 */
	public function getPairs($query, $parameters = array());


	/**
	 * Gets a single record
	 *
	 * @return	mixed
	 * @param	string $query
	 * @param	mixed[optiona] $parameters
	 */
	public function getRecord($query, $parameters = array());


	/**
	 * Alias for retrieve
	 *
	 * @return	mixed
	 * @param	string $query
	 * @param	array[optional] $parameters
	 * @param	string[optional] $key
	 */
	public function getRecords($query, $parameters = array(), $key = null);


	/**
	 * Gets a list with all tables for the currently selected database
	 *
	 * @return	array
	 */
	public function getTablesList();


	/**
	 * Returns the value for a specific field
	 *
	 * @return	mixed
	 * @param	string $query
	 * @param	mixed[optional] $parameters
	 */
	public function getVar($query, $parameters = array());


	/**
	 * Builds a query for inserting records
	 *
	 * @return	int
	 * @param	string $table
	 * @param	array $values
	 */
	public function insert($table, $values);


	/**
	 * Optimize one or more tables
	 *
	 * @return	mixed
	 * @param	mixed $tables
	 */
	public function optimize($tables);


	/**
	 * Retrieves an associative array and returns null if there were no results
	 *
	 * @return	mixed
	 * @param	string $query
	 * @param	mixed[optional] $parameters
	 * @param	string[optional] $key
	 */
	public function retrieve($query, $parameters = array(), $key = null);


	/**
	 * Truncate one or more tables
	 *
	 * @return	void
	 * @param	mixed $tables
	 */
	public function truncate($tables);


	/**
	 * Builds a query for updating records
	 *
	 * @return	int
	 * @param	string $table
	 * @param	array $values
	 * @param	string $where
	 * @param	mixed[optional] $parameters
	 */
	public function update($table, array $values, $where, $parameters = array());
}

?>