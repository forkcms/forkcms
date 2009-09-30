<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package		database
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @author 		Tijs Verkoyen <tijs@spoon-library.be>
 * @author		Dave Lens <dave@spoon-library.be>
 * @since		1.1.0
 */


/**
 * This exception is used to handle database related exceptions.
 *
 * @package		database
 *
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.be>
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		1.1.0
 */
class SpoonDatabaseException extends SpoonException {}


/**
 * This class provides most of the base methods implemented by almost
 * every database system
 *
 * @package		database
 *
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.be>
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		1.1.0
 */
class SpoonDatabase
{
	/**
	 * Database name
	 *
	 * @var	string
	 */
	private $database;


	/**
	 * Debug setting. Queries are logged when enabled
	 *
	 * @var	bool
	 */
	private $debug = false;


	/**
	 * Database driver
	 *
	 * @var	string
	 */
	private $driver;


	/**
	 * Database handler object
	 *
	 * @var	PDO
	 */
	private $handler;


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
	 * List of all executed queries and their parameters
	 *
	 * @var	array
	 */
	private $queries = array();


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
	 * @param	string $driver
	 * @param	string $hostname
	 * @param	string $username
	 * @param	string $password
	 * @param	string $database
	 */
	public function __construct($driver, $hostname, $username, $password, $database)
	{
		$this->setDriver($driver);
		$this->setHostname($hostname);
		$this->setUsername($username);
		$this->setPassword($password);
		$this->setDatabase($database);
	}


	/**
	 * Creates a new database connection if it was not yet made
	 *
	 * @return	void
	 */
	private function connect()
	{
		// not yet connected
		if(!$this->handler)
		{
			try
			{
				$this->handler = new PDO($this->driver .':host='. $this->hostname .';dbname='. $this->database, $this->username, $this->password);
			}

			catch(PDOException $e)
			{
				throw new SpoonDatabaseException('A database connection could not be established.', 0, $this->password);
			}
		}
	}


	/**
	 * Query to delete records, which returns the number of affected rows
	 *
	 * @return	int
	 * @param	string $table
	 * @param	string $where
	 * @param	mixed[optional] $parameters
	 */
	public function delete($table, $where, $parameters = array())
	{
		// create connection
		if(!$this->handler) $this->connect();

		// build query
		$query = 'DELETE FROM '. (string) $table;

		// add where class
		$query = ($where != '') ? $query .' WHERE '. (string) $where .';' : $query .';';

		// set parameters
		$parameters = ($parameters != array()) ? (array) $parameters : array();

		// create statement
		$statement = $this->handler->prepare($query);

		// has parameters
		foreach($parameters as $label => $value)
		{
			// bind values
			$statement->bindValue((is_int($label) ? $label + 1 : (string) $label), $value, $this->getType($value));
		}

		// execute statement
		$statement->execute();

		// has errors
		if($statement->errorCode() != 0)
		{
			$aError = $statement->errorInfo();
			throw new SpoonDatabaseException($aError[2]);
		}

		// debug enabled
		if($this->debug) $this->queries[] = array('query' => $query, 'parameters' => $parameters);

		// number of affected rows
		return (int) $statement->rowCount();
	}


	/**
	 * Drops one or more tables
	 *
	 * @return	void
	 * @param	mixed $tables
	 */
	public function drop($tables)
	{
		$this->execute('DROP TABLE '. implode(', ', (array) $tables) .';');
	}


	/**
	 * Exectues a query
	 *
	 * @return	void
	 * @param	string $query
	 * @param	mixed[optional] $parameters
	 */
	public function execute($query, $parameters = array())
	{
		// create connection
		if(!$this->handler) $this->connect();

		// init vars
		$query = (string) $query;
		$parameters = ($parameters != array()) ? (array) $parameters : array();

		// create statement
		$statement = $this->handler->prepare($query);

		// has parameters
		foreach($parameters as $label => $value)
		{
			// bind values
			$statement->bindValue((is_int($label) ? $label + 1 : (string) $label), $value, $this->getType($value));
		}

		// execute statement
		$statement->execute();

		// has errors
		if($statement->errorCode() != 0)
		{
			$aError = $statement->errorInfo();
			throw new SpoonDatabaseException($aError[2]);
		}

		// debug enabled
		if($this->debug) $this->queries[] = array('query' => $query, 'parameters' => $parameters);
	}


	/**
	 * Retrieve a single column
	 *
	 * @return	array
	 * @param	string $query
	 * @param	mixed[optional] $parameters
	 */
	public function getColumn($query, $parameters = array())
    {
    	// create connection
		if(!$this->handler) $this->connect();

		// init vars
		$query = (string) $query;
		$parameters = ($parameters != array()) ? (array) $parameters : array();

		// create statement
		$statement = $this->handler->prepare($query);

		// has parameters
		foreach($parameters as $label => $value)
		{
			// bind values
			$statement->bindValue((is_int($label) ? $label + 1 : (string) $label), $value, $this->getType($value));
		}

		// execute statement
		$statement->execute();

		// has errors
		if($statement->errorCode() != 0)
		{
			$aError = $statement->errorInfo();
			throw new SpoonDatabaseException($aError[2]);
		}

		// debug enabled
		if($this->debug) $this->queries[] = array('query' => $query, 'parameters' => $parameters);

		// retrieve column data
		return $statement->fetchAll(PDO::FETCH_COLUMN);
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
    	// build query
    	$query = 'SHOW COLUMNS FROM '. (string) $table .' LIKE ?;';

    	// get information
    	$row = $this->getRecord($query, (string) $field);

    	// has no type, so NOT an enum field
    	if(!isset($row['Type'])) throw new SpoonDatabaseException('There is no type information available about this field', 0, $this->password);

    	// has a type but it's not an enum
    	if(strtolower(substr($row['Type'], 0, 4) != 'enum')) throw new SpoonDatabaseException('This field "'. (string) $field .'" is not an enum field.', 0, $this->password);

    	// process values
    	$aSearch = array('enum', '(', ')', '\'');
    	$types = str_replace($aSearch, '', $row['Type']);

    	// return
    	return (array) explode(',', $types);
    }


	/**
	 * Retrieve the debug setting
	 *
	 * @return	bool
	 */
	public function getDebug()
	{
		return $this->debug;
	}


	/**
	 * Fetch the name of the database driver
	 *
	 * @return	string
	 */
	public function getDriver()
	{
		return $this->driver;
	}


	/**
	 * Retrieve the raw database instance (PDO object)
	 *
	 * @return	PDO
	 */
	public function getHandler()
	{
		return $this->handler;
	}


	/**
	 * Retrieve the number of rows in a result set
	 *
	 * @return	int
	 * @param	string $query
	 * @param	mixed[optional] $parameters
	 */
	public function getNumRows($query, $parameters = array())
	{
		// create connection
		if(!$this->handler) $this->connect();

		// init vars
		$query = (string) $query;
		$parameters = ($parameters != array()) ? (array) $parameters : array();

		// create statement
		$statement = $this->handler->prepare($query);

		// has parameters
		foreach($parameters as $label => $value)
		{
			// bind values
			$statement->bindValue((is_int($label) ? $label + 1 : (string) $label), $value, $this->getType($value));
		}

		// execute statement
		$statement->execute();

		// has errors
		if($statement->errorCode() != 0)
		{
			$aError = $statement->errorInfo();
			throw new SpoonDatabaseException($aError[2]);
		}

		// debug enabled
		if($this->debug) $this->queries[] = array('query' => $query, 'parameters' => $parameters);

		// number of results
		return count($statement->fetchAll(PDO::FETCH_COLUMN));
	}


	/**
	 * Retrieve the results of 2 columns as an array key-value pair
	 *
	 * @return	array
	 * @param	string $query
	 * @param	mixed[optional] $parameters
	 */
	public function getPairs($query, $parameters = array())
	{
		// create connection
		if(!$this->handler) $this->connect();

		// init vars
		$query = (string) $query;
		$parameters = ($parameters != array()) ? (array) $parameters : array();

		// create statement
		$statement = $this->handler->prepare($query);

		// has parameters
		foreach($parameters as $label => $value)
		{
			// bind values
			$statement->bindValue((is_int($label) ? $label + 1 : (string) $label), $value, $this->getType($value));
		}

		// execute statement
		$statement->execute();

		// has errors
		if($statement->errorCode() != 0)
		{
			$aError = $statement->errorInfo();
			throw new SpoonDatabaseException($aError[2]);
		}

		// debug enabled
		if($this->debug) $this->queries[] = array('query' => $query, 'parameters' => $parameters);

		// fetch the keys
		return (array) $statement->fetchAll(PDO::FETCH_KEY_PAIR);
	}


	/**
	 * Fetch the executed queries
	 *
	 * @return	array
	 */
	public function getQueries()
	{
		return $this->queries;
	}


	/**
	 * Retrieve a single record
	 *
	 * @return	mixed
	 * @param	string $query
	 * @param	mixed[optional] $parameters
	 */
	public function getRecord($query, $parameters = array())
	{
		// create connection
		if(!$this->handler) $this->connect();

		// init vars
		$query = (string) $query;
		$parameters = ($parameters != array()) ? (array) $parameters : array();

		// create statement
		$statement = $this->handler->prepare($query);

		// has parameters
		foreach($parameters as $label => $value)
		{
			// bind values
			$statement->bindValue((is_int($label) ? $label + 1 : (string) $label), $value, $this->getType($value));
		}

		// execute statement
		$statement->execute();

		// has errors
		if($statement->errorCode() != 0)
		{
			$aError = $statement->errorInfo();
			throw new SpoonDatabaseException($aError[2]);
		}

		// debug enabled
		if($this->debug) $this->queries[] = array('query' => $query, 'parameters' => $parameters);

		// fetch the keys
		$aRecord = $statement->fetch(PDO::FETCH_ASSOC);

		// null when no data found
		return ($aRecord === false) ? null : $aRecord;
	}


	/**
	 * Retrieves an associative array or returns null if there were no results
	 *
	 * @return	mixed
	 * @param	string $query
	 * @param	mixed[optional] $parameters
	 * @param	string[optional] $key
	 */
	public function getRecords($query, $parameters = array(), $key = null)
	{
		// create connection
		if(!$this->handler) $this->connect();

		// init vars
		$query = (string) $query;
		$parameters = ($parameters != array()) ? (array) $parameters : array();

		// create statement
		$statement = $this->handler->prepare($query);

		// has parameters
		foreach($parameters as $label => $value)
		{
			// bind values
			$statement->bindValue((is_int($label) ? $label + 1 : (string) $label), $value, $this->getType($value));
		}

		// execute statement
		$statement->execute();

		// has errors
		if($statement->errorCode() != 0)
		{
			$aError = $statement->errorInfo();
			throw new SpoonDatabaseException($aError[2]);
		}

		// debug enabled
		if($this->debug) $this->queries[] = array('query' => $query, 'parameters' => $parameters);

		// fetch the keys
		$aRecords = (array) $statement->fetchAll(PDO::FETCH_ASSOC);

		// specific key
		if($key !== null)
		{
			// loop records
			foreach($aRecords as $aRecord)
			{
				// key exists
				if(isset($aRecord[(string) $key])) $aData[$aRecord[(string) $key]] = $aRecord;
			}

			// data or no data
			return (isset($aData)) ? $aData : null;
		}

		// has results
		return (count($aRecords) != 0) ? $aRecords : null;
	}


	/**
	 * Retrieve the tables in the current database
	 *
	 * @return	array
	 */
	public function getTables()
	{
		return (array) $this->getColumn('SHOW TABLES;');
	}


	/**
	 * Retrieve the type for this value
	 *
	 * @return	int
	 * @param	mixed $value
	 */
	private function getType($value)
	{
		if($value === null) return PDO::PARAM_NULL;
		elseif(is_int($value) || is_float($value)) return PDO::PARAM_INT;
		return PDO::PARAM_STR;
	}


	/**
	 * Fetch a single var
	 *
	 * @return	string
	 * @param	string $query
	 * @param	mixed[optional] $parameters
	 */
	public function getVar($query, $parameters = array())
	{
		// create connection
		if(!$this->handler) $this->connect();

		// init vars
		$query = (string) $query;
		$parameters = ($parameters != array()) ? (array) $parameters : array();

		// create statement
		$statement = $this->handler->prepare($query);

		// has parameters
		foreach($parameters as $label => $value)
		{
			// bind values
			$statement->bindValue((is_int($label) ? $label + 1 : (string) $label), $value, $this->getType($value));
		}

		// execute statement
		$statement->execute();

		// has errors
		if($statement->errorCode() != 0)
		{
			$aError = $statement->errorInfo();
			throw new SpoonDatabaseException($aError[2]);
		}

		// debug enabled
		if($this->debug) $this->queries[] = array('query' => $query, 'parameters' => $parameters);

		// fetch the var
		return $statement->fetchColumn();
	}


	/**
	 * Inserts one or more records
	 *
	 * @return	int
	 * @param	string $table
	 * @param	array $values
	 */
	public function insert($table, array $values)
	{
		// create connection
		if(!$this->handler) $this->connect();

		// array has values
		if(count($values) == 0) throw new SpoonDatabaseException('You need to provide values for an insert query.', 0, $this->password);

		// init vars
		$query = 'INSERT INTO '. (string) $table .' (';
		$aKeys = array_keys($values);
		$aValues = array_values($values);
		$parameters = array();

		// multidimensional array
		if(is_array($aValues[0]))
		{
			// num values/keys
			$numRecords = count($values);
			$numFields = count($aValues[0]);

			// build query
			$query .= implode(', ', array_keys($aValues[0])) .') VALUES ';

			// init counter
			$i = 1;

			// loop rows
			foreach($values as $aRow)
			{
				// number of keys is not consistent
				if(count($aRow) != $numFields) throw new SpoonDatabaseException('Each record of this array should contain the same number of keys.', 0, $this->password);

				// build query
				$query .= '(';

				// loop keys
				for($t = 0; $t < $numFields; $t++)
				{
					// add parameter marker
					$query .= '?';

					// add comma, unless this is the last
					if($t != ($numFields - 1)) $query .= ', ';
				}

				// add closing brackets
				if($i != $numRecords) $query .= '), ';

				// merge parameters
				$parameters = array_merge($parameters, array_values($aRow));

				// update counter
				$i++;
			}

			// finish query
			$query .= ');';
		}

		// single array
		else
		{
			// number of fields
			$numFields = count($aValues);

			// build query
			$query .= implode(', ', $aKeys) .') VALUES (';

			// add parameters
			for($i = 0; $i < count($aValues); $i++)
			{
				// add parameter marker
				$query .= '?';

				// add comma, unless this is the last
				if($i != ($numFields - 1)) $query .= ', ';
			}

			// end query
			$query .= ');';

			// set parameters
			$parameters = $aValues;
		}

		// create statement
		$statement = $this->handler->prepare($query);

		// execute statement
		$statement->execute((array) $parameters);

		// has errors
		if($statement->errorCode() != 0)
		{
			$aError = $statement->errorInfo();
			throw new SpoonDatabaseException($aError[2]);
		}

		// debug enabled
		if($this->debug) $this->queries[] = array('query' => $query, 'parameters' => $parameters);

		// fetch the keys
		return (int) $this->handler->lastInsertId();
	}


	/**
	 * Optimize one or more tables
	 *
	 * @return	void
	 * @param	mixed $tables
	 */
	public function optimize($tables)
	{
		// one parameter
		$tables = (func_num_args() == 1) ? (array) $tables : func_get_args();

		// build & execute query
		return $this->getRecords('OPTIMIZE TABLE '. implode(', ', $tables) .';');
	}

	
	/**
	 * Retrieves an associative array or returns null if there were no results
	 *
	 * @return	mixed
	 * @param	string $query
	 * @param	mixed[optional] $parameters
	 * @param	string[optional] $key
	 */
	public function retrieve($query, $parameters = array(), $key = null)
	{
		return $this->getRecords($query, $parameters, $key);
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
	 * @param	bool[optional] $on
	 */
	public function setDebug($on = true)
	{
		$this->debug = (bool) $on;
	}


	/**
	 * Set driver type
	 *
	 * @return	void
	 * @param	string $driver
	 */
	private function setDriver($driver)
	{
		// validate backend
		if(!in_array($driver, PDO::getAvailableDrivers())) throw new SpoonDatabaseException('The driver "'. (string) $driver .'" is not supported. Only '. implode(', ', PDO::getAvailableDrivers()) .' are supported');

		// set property
		$this->driver = (string) $driver;
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
	 * Truncate on or more tables
	 *
	 * @return	void
	 * @param	mixed $tables
	 */
	public function truncate($tables)
	{
		// one parameter
		$tables = (func_num_args() == 1) ? (array) $tables : func_get_args();

		// loop & truncate
		foreach($tables as $table) $this->execute('TRUNCATE TABLE '. $table .';');
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
	public function update($table, array $values, $where, $parameters = array())
	{
		// create connection
		if(!$this->handler) $this->connect();

		// init vars
		$table = (string) $table;
		$where = (string) $where;
		$parameters = ($parameters != array()) ? (array) $parameters : array();

		// values check
		if(count($values) == 0) throw new SpoonDatabaseException('No values provided.', 0, $this->password);

		// init vars
		$i = 1;
		$iValues = count($values);
		$query = 'UPDATE '. (string) $table .' SET ';

		// loop values
		foreach($values as $key => $value)
		{
			$query .= $key .' = ?';
			if($i != $iValues) $query .= ', ';
			$aTmpParameters[] = $value;
			$i++;
		}

		// add where clause
		if($where != '') $query .= ' WHERE '. $where;

		// finalize query
		$query .= ';';

		// update parameters
		$parameters = array_merge($aTmpParameters, $parameters);

		// create statement
		$statement = $this->handler->prepare($query);

		// has parameters
		foreach($parameters as $label => $value)
		{
			// bind values
			$statement->bindValue((is_int($label) ? $label + 1 : (string) $label), $value, $this->getType($value));
		}

		// execute statement
		$statement->execute();

		// has errors
		if($statement->errorCode() != 0)
		{
			$aError = $statement->errorInfo();
			throw new SpoonDatabaseException($aError[2]);
		}

		// debug enabled
		if($this->debug) $this->queries[] = array('query' => $query, 'parameters' => $parameters);

		// number of results
		return (int) $statement->rowCount();
	}
}

?>