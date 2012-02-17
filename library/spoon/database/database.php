<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package		spoon
 * @subpackage	database
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		1.1.0
 */


/**
 * This class provides most of the base methods implemented by almost
 * every database system
 *
 * @package		spoon
 * @subpackage	database
 *
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @author		Davy Hellemans <davy@spoon-library.com>
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
	 * Database port
	 *
	 * @var int
	 */
	private $port;


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
	 * Creates a database connection instance.
	 *
	 * @param	string $driver			The driver to use. Available drivers depend on your server configuration.
	 * @param	string $hostname		The host or IP of your database server.
	 * @param	string $username		The username to authenticate on your database server.
	 * @param	string $password		The password to authenticate on your database server.
	 * @param	string $database		The name of the database to use.
	 * @param	int[optional] $port		The port to connect on.
	 */
	public function __construct($driver, $hostname, $username, $password, $database, $port = null)
	{
		$this->setDriver($driver);
		$this->setHostname($hostname);
		$this->setUsername($username);
		$this->setPassword($password);
		$this->setDatabase($database);
		$this->setPort($port);
	}


	/**
	 * Creates a new database connection if it was not yet made.
	 */
	private function connect()
	{
		// not yet connected
		if(!$this->handler)
		{
			try
			{
				$dsn = $this->driver;
				$dsn .= ':host=' . $this->hostname;
				$dsn .= ';dbname=' . $this->database;
				$dsn .= ';user=' . $this->username;
				$dsn .= ';password=' . $this->password;

				if($this->port !== null)
				{
					$dsn .= ';port=' . $this->port;
				}

				// create handler
				$this->handler = new PDO($dsn, $this->username, $this->password);
				$this->handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				// mysql only option
				if($this->driver == 'mysql')
				{
					$this->handler->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
				}
			}

			catch(PDOException $e)
			{
				throw new SpoonDatabaseException('A database connection could not be established.', 0, $this->password);
			}
		}
	}


	/**
	 * Query to delete records, which returns the number of affected rows.
	 *
	 * @return	int								The number of affected rows.
	 * @param	string $table					The table to perform the delete-statement on.
	 * @param	string[optional] $where			The WHERE-clause.
	 * @param	mixed[optional] $parameters		The parameters that will be used in the query.
	 */
	public function delete($table, $where = null, $parameters = array())
	{
		// create connection
		if(!$this->handler) $this->connect();

		// build query
		$query = 'DELETE FROM ' . $this->quoteName((string) $table);

		// add where class
		$query = ($where != '') ? $query . ' WHERE ' . (string) $where : $query;

		// set parameters
		$parameters = (array) $parameters;

		// create statement
		$statement = $this->handler->prepare($query);

		// validate statement
		if($statement === false)
		{
			// get error
			$errorInfo = $this->handler->errorInfo();

			// throw exceptions
			throw new SpoonDatabaseException($errorInfo[2]);
		}

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
	 * Drops one or more tables.
	 *
	 * @param	mixed $tables		The table(s) to drop.
	 */
	public function drop($tables)
	{
		$this->execute('DROP TABLE ' . implode(', ', array_map(array($this, 'quoteName'), (array) $tables)));
	}


	/**
	 * Executes a query.
	 *
	 * @param	string $query					The query to execute, only use with queries that don't return a result.
	 * @param	mixed[optional] $parameters		The parameters that will be used in the query.
	 */
	public function execute($query, $parameters = array())
	{
		// create connection
		if(!$this->handler) $this->connect();

		// init vars
		$query = (string) $query;
		$parameters = (array) $parameters;

		// create statement
		$statement = $this->handler->prepare($query);

		// validate statement
		if($statement === false)
		{
			// get error
			$errorInfo = $this->handler->errorInfo();

			// throw exceptions
			throw new SpoonDatabaseException($errorInfo[2]);
		}

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
	 * Retrieve a single column.
	 *
	 * @return	array							An array with the values from a single column
	 * @param	string $query					The query, specify maximum one field in the SELECT-statement.
	 * @param	mixed[optional] $parameters		The parameters that will be used in the query.
	 */
	public function getColumn($query, $parameters = array())
	{
		// create connection
		if(!$this->handler) $this->connect();

		// init vars
		$query = (string) $query;
		$parameters = (array) $parameters;

		// create statement
		$statement = $this->handler->prepare($query);

		// validate statement
		if($statement === false)
		{
			// get error
			$errorInfo = $this->handler->errorInfo();

			// throw exceptions
			throw new SpoonDatabaseException($errorInfo[2]);
		}

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
	 * Retrieve the selected database.
	 *
	 * @return	string The name of the database.
	 */
	public function getDatabase()
	{
		return $this->database;
	}


	/**
	 * Retrieve the debug setting
	 *
	 * @return	bool	true if debug is enabled, false if not.
	 */
	public function getDebug()
	{
		return $this->debug;
	}


	/**
	 * Fetch the name of the database driver
	 *
	 * @return	string	The name of the driver that is used.
	 */
	public function getDriver()
	{
		return $this->driver;
	}


	/**
	 * Retrieves the possible ENUM values
	 *
	 * @return	array			An array with all the possible ENUM values.
	 * @param	string $table	The table that contains the ENUM field.
	 * @param	string $field	The name of the field.
	 */
	public function getEnumValues($table, $field)
	{
		// redefine vars
		$table = $this->quoteName((string) $table);
		$field = (string) $field;

		// build query
		$query = 'SHOW COLUMNS FROM ' . $table . ' LIKE "' . $field . '"';

		// get information
		$row = $this->getRecord($query);

		// has no type, so NOT an enum field
		if(!isset($row['Type'])) throw new SpoonDatabaseException('There is no type information available about this field', 0, $this->password);

		// has a type but it's not an enum
		if(strtolower(substr($row['Type'], 0, 4) != 'enum')) throw new SpoonDatabaseException('This field "' . $field . '" is not an enum field.', 0, $this->password);

		// process values
		$aSearch = array('enum', '(', ')', '\'');
		$types = str_replace($aSearch, '', $row['Type']);

		// return
		return (array) explode(',', $types);
	}


	/**
	 * Retrieve the raw database instance (PDO object)
	 *
	 * @return	PDO	The PDO-instance.
	 */
	public function getHandler()
	{
		return $this->handler;
	}


	/**
	 * Retrieve the hostname.
	 *
	 * @return	string The hostname.
	 */
	public function getHostname()
	{
		return $this->hostname;
	}


	/**
	 * Retrieve the number of rows in a result set
	 *
	 * @return	int								The number of rows in the result-set.
	 * @param	string $query					Teh query to perform.
	 * @param	mixed[optional] $parameters		The parameters that will be used in the query.
	 */
	public function getNumRows($query, $parameters = array())
	{
		// create connection
		if(!$this->handler) $this->connect();

		// init vars
		$query = (string) $query;
		$parameters = (array) $parameters;

		// create statement
		$statement = $this->handler->prepare($query);

		// validate statement
		if($statement === false)
		{
			// get error
			$errorInfo = $this->handler->errorInfo();

			// throw exceptions
			throw new SpoonDatabaseException($errorInfo[2]);
		}

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
	 * @return	array							An array with the first column as key, the second column as the values.
	 * @param	string $query					The query to perform.
	 * @param	mixed[optional] $parameters		The parameters that will be used in the query.
	 */
	public function getPairs($query, $parameters = array())
	{
		// create connection
		if(!$this->handler) $this->connect();

		// init vars
		$query = (string) $query;
		$parameters = (array) $parameters;

		// init var
		$results = array();
		$keys = null;

		// fetch results
		$tmpResults = (array) self::getRecords($query, $parameters);

		// loop results
		foreach($tmpResults as $result)
		{
			// fetch keys
			if(!isset($keys))
			{
				// fetch keys
				$keys = array_keys($tmpResults[0]);

				// needs to be 2 elements
				if(count($keys) != 2) throw new SpoonDatabaseException('You have to fetch 2 columns when using getPairs.');
			}

			// add to list
			$results[$result[$keys[0]]] = $result[$keys[1]];
		}

		return $results;
	}


	/**
	 * Retrieve the password.
	 *
	 * @return	string	The password.
	 */
	public function getPassword()
	{
		return $this->password;
	}


	/**
	 * Fetch the executed queries
	 *
	 * @return	array	An array with all the executed queries, will only be filled in debug-mode.
	 */
	public function getQueries()
	{
		return $this->queries;
	}


	/**
	 * Retrieve a single record
	 *
	 * @return	mixed							An array containing one record. Keys are the column-names.
	 * @param	string $query					The query to perform. If multiple rows are selected only the first row will be returned.
	 * @param	mixed[optional] $parameters		The parameters that will be used in the query.
	 */
	public function getRecord($query, $parameters = array())
	{
		// create connection
		if(!$this->handler) $this->connect();

		// init vars
		$query = (string) $query;
		$parameters = (array) $parameters;

		// create statement
		$statement = $this->handler->prepare($query);

		// validate statement
		if($statement === false)
		{
			// get error
			$errorInfo = $this->handler->errorInfo();

			// throw exceptions
			throw new SpoonDatabaseException($errorInfo[2]);
		}

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
	 * @return	mixed							An array containing arrays which represent a record.
	 * @param	string $query					The query to perform.
	 * @param	mixed[optional] $parameters		The parameters that will be used in the query.
	 * @param	string[optional] $key			The field that should be used as key, make sure this is unique for each row.
	 */
	public function getRecords($query, $parameters = array(), $key = null)
	{
		// create connection
		if(!$this->handler) $this->connect();

		// init vars
		$query = (string) $query;
		$parameters = (array) $parameters;

		// create statement
		$statement = $this->handler->prepare($query);

		// validate statement
		if($statement === false)
		{
			// get error
			$errorInfo = $this->handler->errorInfo();

			// throw exceptions
			throw new SpoonDatabaseException($errorInfo[2]);
		}

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
	 * @return	array	An array containg a list of all available tables.
	 */
	public function getTables()
	{
		return (array) $this->getColumn('SHOW TABLES');
	}


	/**
	 * Retrieve the type for this value
	 *
	 * @return	int
	 * @param	mixed $value		The value to retrieve the type for.
	 */
	private function getType($value)
	{
		if($value === null) return PDO::PARAM_NULL;
		elseif(is_int($value) || is_float($value)) return PDO::PARAM_INT;
		return PDO::PARAM_STR;
	}


	/**
	 * Retrieve the username.
	 *
	 * @return	string	The username.
	 */
	public function getUsername()
	{
		return $this->username;
	}


	/**
	 * Fetch a single var
	 *
	 * @return	string							The value as a string
	 * @param	string $query					The query to perform.
	 * @param	mixed[optional] $parameters		The parameters that will be used in the query.
	 */
	public function getVar($query, $parameters = array())
	{
		// create connection
		if(!$this->handler) $this->connect();

		// init vars
		$query = (string) $query;
		$parameters = (array) $parameters;

		// create statement
		$statement = $this->handler->prepare($query);

		// validate statement
		if($statement === false)
		{
			// get error
			$errorInfo = $this->handler->errorInfo();

			// throw exceptions
			throw new SpoonDatabaseException($errorInfo[2]);
		}

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
	 * @return	int				The last inserted ID.
	 * @param	string $table	The table wherein the record will be inserted.
	 * @param	array $values	The values for the record to insert, keys of this array should match the column names.
	 */
	public function insert($table, array $values)
	{
		// create connection
		if(!$this->handler) $this->connect();

		// array has values
		if(count($values) == 0) throw new SpoonDatabaseException('You need to provide values for an insert query.', 0, $this->password);

		// init vars
		$query = 'INSERT INTO ' . $this->quoteName((string) $table) . ' (';
		$keys = array_keys($values);
		$actualValues = array_values($values);
		$parameters = array();

		// multidimensional array
		if(is_array($actualValues[0]))
		{
			// num values/keys
			$numRecords = count($values);
			$numFields = count($actualValues[0]);

			// fetch keys
			$subKeys = array_keys($actualValues[0]);

			// prefix with table name
			array_walk($subKeys, array($this, 'prefixTableNames'), $table);

			// build query
			$query .= implode(', ', $subKeys) . ') VALUES ';

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
					$query .= '?, ';
				}

				// remove trailing comma
				if($numFields) $query = substr($query, 0, -2);

				// add closing brackets
				if($i != $numRecords) $query .= '), ';

				// merge parameters
				$parameters = array_merge($parameters, array_values($aRow));

				// update counter
				$i++;
			}

			// finish query
			$query .= ')';
		}

		// single array
		else
		{
			// number of fields
			$numFields = count($actualValues);

			// prefix with table name
			array_walk($keys, array($this, 'prefixTableNames'), $table);

			// build query
			$query .= implode(', ', $keys) . ') VALUES (';

			// add parameters
			for($i = 0; $i < count($actualValues); $i++)
			{
				// add parameter marker
				$query .= '?, ';
			}

			// remove trailing comma
			if($numFields) $query = substr($query, 0, -2);

			// end query
			$query .= ')';

			// set parameters
			$parameters = $actualValues;
		}

		// create statement
		$statement = $this->handler->prepare($query);

		// validate statement
		if($statement === false)
		{
			// get error
			$errorInfo = $this->handler->errorInfo();

			// throw exceptions
			throw new SpoonDatabaseException($errorInfo[2]);
		}

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
	 * @param	mixed $tables	An array containing the name(s) of the tables to optimize.
	 */
	public function optimize($tables)
	{
		// one parameter
		$tables = (func_num_args() == 1) ? (array) $tables : func_get_args();

		// build & execute query
		return $this->getRecords('OPTIMIZE TABLE ' . implode(', ', array_map(array($this, 'quoteName'), $tables)));
	}


	/**
	 * Prefix table names.
	 *
	 * @param string $key The key to be prefixed.
	 * @param string $value The value
	 * @param string $table The table name to prefix the key with.
	 * @return void
	 */
	protected function prefixTableNames(&$key, $value, $table)
	{
		$key = $this->quoteName($table) . '.' . $key;
	}


	/**
	 * Quote the name of a table or column.
	 * Note: for now this will only put backticks around the name (mysql).
	 *
	 * @return	string			The quoted name.
	 * @param	string $name	The name of a column or table to quote.
	 */
	protected function quoteName($name)
	{
		return '`' . $name . '`';
	}


	/**
	 * Retrieves an associative array or returns null if there were no results
	 * This is an alias for getRecords
	 *
	 * @return	mixed							An array containing arrays which represent a record.
	 * @param	string $query					The query to perform.
	 * @param	mixed[optional] $parameters		The parameters that will be used in the query.
	 * @param	string[optional] $key			The field that should be used as key, make sure this is unique for each row.
	 */
	public function retrieve($query, $parameters = array(), $key = null)
	{
		return $this->getRecords($query, $parameters, $key);
	}


	/**
	 * Set database name
	 *
	 * @param	string $database	The name of the database.
	 */
	private function setDatabase($database)
	{
		$this->database = (string) $database;
	}


	/**
	 * Set the debug status
	 *
	 * @param	bool[optional] $on	Should debug-mode be activated. Be carefull, this will use a lot of resources (Memory and CPU).
	 */
	public function setDebug($on = true)
	{
		$this->debug = (bool) $on;
	}


	/**
	 * Set driver type
	 *
	 * @param	string $driver	The driver to use. Available drivers depend on your server configuration.
	 */
	private function setDriver($driver)
	{
		// init var
		$driver = (string) $driver;

		// validate backend
		if(!in_array($driver, PDO::getAvailableDrivers())) throw new SpoonDatabaseException('The PDO database driver "' . (string) $driver . '" is not found. Only ' . implode(', ', PDO::getAvailableDrivers()) . ' are currently installed.');

		// set property
		$this->driver = $driver;
	}


	/**
	 * Set hostname
	 *
	 * @param	string $hostname	The host or IP of your database-server.
	 */
	private function setHostname($hostname)
	{
		$this->hostname = (string) $hostname;
	}


	/**
	 * Set password
	 *
	 * @param	string $password	The password to authenticate on your database-server.
	 */
	private function setPassword($password)
	{
		$this->password = (string) $password;
	}


	/**
	 * Set port
	 *
	 * @param	int $port	The port to connect on.
	 */
	private function setPort($port)
	{
		$this->port = (int) $port;
	}


	/**
	 * Set username
	 *
	 * @param	string $username	The username to authenticate on your database-server.
	 */
	private function setUsername($username)
	{
		$this->username = (string) $username;
	}


	/**
	 * Truncate on or more tables
	 *
	 * @param	mixed $tables	A string or array containing the list of tables to truncate.
	 */
	public function truncate($tables)
	{
		// one parameter
		$tables = (func_num_args() == 1) ? (array) $tables : func_get_args();

		// loop & truncate
		foreach($tables as $table) $this->execute('TRUNCATE TABLE ' . $this->quoteName($table));
	}


	/**
	 * Builds a query for updating records
	 *
	 * @return	int								The number of affected rows.
	 * @param	string $table					The table to run the UPDATE-statement on.
	 * @param	array $values					The values to update, use the key(s) as columnnames.
	 * @param	string[optional] $where			The WHERE-clause.
	 * @param	mixed[optional] $parameters		The parameters that will be used in the query.
	 */
	public function update($table, array $values, $where = null, $parameters = array())
	{
		// create connection
		if(!$this->handler) $this->connect();

		// init vars
		$table = $this->quoteName((string) $table);
		$parameters = (array) $parameters;
		$namedParameters = false;

		// values check
		if(count($values) == 0) throw new SpoonDatabaseException('No values provided.', 0, $this->password);

		// init vars
		$i = 1;
		$iValues = count($values);
		$query = 'UPDATE ' . (string) $table . ' SET ';

		// has parameters
		if(count($parameters))
		{
			// loop parameters
			foreach($parameters as $key => $value)
			{
				// key such as ':id' starting
				if(substr($key, 0, 1) == ':')
				{
					$namedParameters = true;
					break;
				}
			}
		}

		// loop values
		foreach($values as $key => $value)
		{
			// named parameters
			if(!$namedParameters)
			{
				$query .= $table . '.' . $key . ' = ?, ';
				$aTmpParameters[] = $value;
			}

			// positional parameters
			else
			{
				$query .= $table . '.' . $key . ' = :' . $key . ', ';
				$aTmpParameters[':' . $key] = $value;
			}

			// counter
			$i++;
		}

		// remove trailing comma
		if($iValues) $query = substr($query, 0, -2);

		// add where clause
		if($where != '') $query .= ' WHERE ' . (string) $where;

		// update parameters
		$parameters = array_merge($aTmpParameters, $parameters);

		// create statement
		$statement = $this->handler->prepare($query);

		// validate statement
		if($statement === false)
		{
			// get error
			$errorInfo = $this->handler->errorInfo();

			// throw exceptions
			throw new SpoonDatabaseException($errorInfo[2]);
		}

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


/**
 * This exception is used to handle database related exceptions.
 *
 * @package		spoon
 * @subpackage	database
 *
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		1.1.0
 */
class SpoonDatabaseException extends SpoonException {}
