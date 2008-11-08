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
 * @author 			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			1.0.0
 */


/** Spoon class */
require_once 'spoon/spoon.php';

/** SpoonDatabaseException class */
require_once 'spoon/database/exception.php';

/** SpoonFile class */
require_once 'spoon/filesystem/file.php';


/**
 * This class provides most of the base methods implemented by almost
 * every database system
 *
 * @package			database
 *
 *
 * @author 			Tijs Verkoyen <tijs@spoon-library.be>
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			1.0.0
 */
class SpoonDatabase
{
	/**
	 * Database type
	 *
	 * @var	string
	 */
	private $backend;


	/**
	 * The connection charset
	 *
	 * @var	string
	 */
	private $charset;


	/**
	 * Database name
	 *
	 * @var	string
	 */
	private $database;


	/**
	 * Backend-object
	 *
	 * @var	mixed
	 */
	private $databaseObject;


	/**
	 * Database hostname
	 *
	 * @var	string
	 */
	private $hostname;


	/**
	 * Database password
	 *
	 * @var	string
	 */
	private $password;


	/**
	 * Database username
	 *
	 * @var	string
	 */
	private $username;


	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	string $backend
	 * @param	string $hostname
	 * @param	string $username
	 * @param	string $password
	 * @param	string $database
	 * @param	string[optional] $charset
	 */
	public function __construct($backend, $hostname, $username, $password, $database, $charset = 'latin1')
	{
		// set properties
		$this->setBackend($backend);
		$this->setDatabase($database);
		$this->setHostname($hostname);
		$this->setPassword($password);
		$this->setUsername($username);
		$this->setCharset($charset);

		// require class
		require_once 'spoon/database/'. $this->backend .'.php';

		// create databaseObject
		$objectName = 'SpoonDatabase'. $this->backend;
		$this->databaseObject = new $objectName($this->hostname, $this->username, $this->password, $this->database, $charset);
	}


	/**
	 * Class destructor.
	 *
	 * @return	void
	 */
	public function __destruct()
	{
		$this->databaseObject->disconnect();
	}


	/**
	 * Builds a query for deleting records & returns the affected rows
	 *
	 * @return	int
	 * @param	string $table
	 * @param	string $where
	 * @param	mixed[optional] $parameters
	 */
	public function delete($table, $where, $parameters = array())
	{
		return (int) $this->databaseObject->delete($table, $where, $parameters);
	}


	/**
	 * Drops one or more tables
	 *
	 * @return	void
	 * @param	mixed $tables
	 */
	public function drop($tables)
	{
		$this->databaseObject->drop($tables);
	}


	/**
	 * Executes a query and returns the last inserted id or the number of affected rows
	 *
	 * @return	int
	 * @param	string $query
	 * @param	mixed[optional] $parameters
	 */
	public function execute($query, $parameters = array())
	{
		return (int) $this->databaseObject->execute($query, $parameters);
	}


	/**
	 * Get all executed queries
	 *
	 * @return	array
	 */
	public function getQueries()
	{
		return (array) $this->databaseObject->queries;
	}


	/**
	 * Get a single column
	 *
	 * @return	array
	 * @param	string $query
	 * @param	mixed[optional] $parameters
	 */
	public function getColumn($query, $parameters = array())
    {
    	return (array) $this->databaseObject->getColumn($query, $parameters);
    }


    /**
     * Get the debug status
     *
     * @return	bool
     */
    public function getDebug()
    {
    	return (bool) $this->databaseObject->getDebug();
    }


    /**
     * Retrieves the possible ENUM-values
     *
     * @return	array
     * @param	string $table
     * @param	string $field
     */
    public function getEnumValues($table, $field)
    {
    	return (array) $this->databaseObject->getEnumValues($table, $field);
    }


	/**
	 * Get the last executed query
	 *
	 * @return	string
	 */
	public function getLastExecutedQuery()
	{
		return (string) $this->databaseObject->lastQuery;
	}


	/**
	 * Get number of executed queries
	 *
	 * @return	int
	 */
	public function getNumberOfExecutedQueries()
	{
		return (int) $this->databaseObject->numQueries;
	}


	/**
	 * Gets the number of rows in a result
	 *
	 * @return	int
	 * @param	string $query
	 * @param	mixed[optional] $parameters
	 */
	public function getNumRows($query, $parameters = array())
	{
		return (int) $this->databaseObject->getNumRows($query, $parameters);
	}


	/**
	 * Get the results as a key-value-pair
	 *
	 * @return	array
	 * @param	string $query
	 * @param	mixed[optional] $parameters
	 */
	public function getPairs($query, $parameters = array())
	{
		return (array) $this->databaseObject->getPairs($query, $parameters);
	}


	/**
	 * Gets a single record
	 *
	 * @return	mixed
	 * @param	string $query
	 * @param	mixed[optional] $parameters
	 */
	public function getRecord($query, $parameters = array())
	{
		return $this->databaseObject->getRecord($query, $parameters);
	}


	/**
	 * Alias for retrieve
	 *
	 * @return	mixed
	 * @param	string $query
	 * @param	mixed[optional] $parameters
	 * @param	string[optional] $key
	 */
	public function getRecords($query, $parameters = array(), $key = null)
	{
		return $this->databaseObject->retrieve($query, $parameters, $key);
	}


	/**
	 * Gets a list with all tables for the currently selected database
	 *
	 * @return	array
	 */
	public function getTablesList()
	{
		return (array) $this->databaseObject->getTablesList();
	}


	/**
	 * Retrieve the backend
	 *
	 * @return	string
	 */
	public function getBackend()
	{
		return $this->backend;
	}


	/**
	 * Get the value of a specific field
	 *
	 * @return	mixed
	 * @param	string $query
	 * @param	mixed[optional] $parameters
	 */
	public function getVar($query, $parameters = array())
	{
		return $this->databaseObject->getVar($query, $parameters);
	}


	/**
	 * Builds a query for inserting records
	 *
	 * @return	int
	 * @param	string $table
	 * @param	array $values
	 */
	public function insert($table, $values)
	{
		return (int) $this->databaseObject->insert($table, $values);
	}


	/**
	 * Optimize one or more tables
	 *
	 * @return	mixed
	 * @param	mixed $tables
	 */
	public function optimize($tables)
	{
		return $this->databaseObject->optimize($tables);
	}


	/**
	 * Retrieves an associative array and returns null if there were no results
	 *
	 * @return	mixed
	 * @param	string $query
	 * @param	mixed[optional] $parameters
	 */
	public function retrieve($query, $parameters = array(), $key = null)
	{
		return $this->databaseObject->retrieve($query, $parameters, $key);
	}


	/**
	 * Set backend type
	 *
	 * @return	void
	 * @param	string $backend
	 */
	private function setBackend($backend)
	{
		// validate backend
		if(SpoonFilter::getValue($backend, array('mysqli'), 'invalid') == 'invalid') throw new SpoonDatabaseException('Invalid backend, only mysqli is allowed .');

		// set property
		$this->backend = (string) strtolower($backend);
	}


	/**
	 * Set the connection charset
	 *
	 * @return	void
	 * @param	string $charset
	 */
	private function setCharset($charset)
	{
		$this->charset = (string) $charset;
	}


	/**
	 * Set database name
	 *
	 * @return	void
	 * @param	string $database
	 */
	private function setDatabase($database)
	{
		$this->database = (string) $database;
	}


	/**
	 * Set the debug status
	 *
	 * @return	void
	 * @param	bool[optional} $on
	 */
	public function setDebug($on = true)
	{
		$this->databaseObject->setDebug($on);
	}


	/**
	 * Set hostname
	 *
	 * @return	void
	 * @param	string $hostname
	 */
	private function setHostname($hostname)
	{
		$this->hostname = (string) $hostname;
	}


	/**
	 * Set password
	 *
	 * @return	void
	 * @param	string $password
	 */
	private function setPassword($password)
	{
		$this->password = (string) $password;
	}


	/**
	 * Set username
	 *
	 * @return	void
	 * @param	string $username
	 */
	private function setUsername($username)
	{
		$this->username = (string) $username;
	}


	/**
	 * Truncate one or more tables
	 *
	 * @return	void
	 * @param	mixed $tables
	 */
	public function truncate($tables)
	{
		$this->databaseObject->truncate($tables);
	}


	/**
	 * Builds a query for updating records
	 *
	 * @return	int
	 * @param	string $table
	 * @param	array $values
	 * @param	string $where
	 * @param	mixed[optional] $parameters
	 */
	public function update($table, $values, $where, $parameters = array())
	{
		return (int) $this->databaseObject->update($table, $values, $where, $parameters);
	}
}

?>