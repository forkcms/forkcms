<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package			database
 * @subpackage		mysqli
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

/** interface */
require_once 'spoon/database/i_database_object.php';


/**
 * This class provides most of the base methods implemented by almost
 * every database system
 *
 * @package			database
 * @subpackage		mysqli
 *
 *
 * @author 			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			1.0.0
 */
class SpoonDatabaseMysqli implements iSpoonDatabaseObject
{
	/**
	 * All executed queries
	 *
	 * @var	array
	 */
	public $queries = array();


	/**
	 * Default charset
	 *
	 * @var	string
	 */
	private $charset = 'latin1';


	/**
	 * Database name
	 *
	 * @var	string
	 */
	private $database;


	/**
	 * Debugging
	 *
	 * @var	bool
	 */
	private $debug = false;


	/**
	 * Handler
	 *
	 * @var	resource
	 */
	private $handler;


	/**
	 * Hostname
	 *
	 * @var	string
	 */
	private $hostname;


	/**
	 * Last executed  query
	 *
	 * @var	string
	 */
	public $lastQuery;


	/**
	 * Number of executed queries
	 *
	 * @var	int
	 */
	public $numQueries = 0;


	/**
	 * Password
	 *
	 * @var	string
	 */
	private $password;


	/**
	 * Username
	 *
	 * @var	string
	 */
	private $username;


	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	string $hostname
	 * @param	string $username
	 * @param	string $password
	 * @param	string $database
	 * @param	string[optional] $charset
	 */
	public function __construct($hostname, $username, $password, $database, $charset = 'latin1')
	{
		$this->backend = 'mysqli';
		$this->hostname = (string) $hostname;
		$this->username = (string) $username;
		$this->password = (string) $password;
		$this->database = (string )$database;
		$this->charset = (string) $charset;
	}


	/**
	 * Class destructor.
	 *
	 * @return	void
	 */
	public function __destruct()
	{
		$this->disconnect();
	}


	/**
	 * Open a new connection to the MySQL server
	 *
	 * @return	void
	 */
	public function connect()
	{
		// create handler
		$this->handler = @mysqli_connect($this->hostname, $this->username, $this->password, $this->database);

		// connection failed
		if($this->handler === false) throw new SpoonDatabaseException('Could not connect.<br />'. mysqli_connect_error(), 0, $this->password);

		// set charset
		if(@mysqli_set_charset($this->handler, $this->charset) === false)
		{
			throw new SpoonDatabaseException('Could not set the charset "'. $this->charset .'" for the database connection.', 0, $this->password);
		}
	}


	/**
	 * Builds a query for deleting records
	 *
	 * @return	int
	 * @param	string $table
	 * @param	string $where
	 * @param	mixed[optional] $parameters
	 */
	public function delete($table, $where, $parameters = array())
	{
		// build query
		$query = 'DELETE FROM '. (string) $table;
		if($where != '') $query .=' WHERE '. (string) $where;
		$query .= ';';

		// execute query and return affected rows
		return $this->execute($query, $parameters);
	}


	/**
	 * Closes a previously opened database connection
	 *
	 * @return	void
	 */
	public function disconnect()
	{
		// validate the handler, close it if possible
		if(is_resource($this->handler)) @mysqli_close($this->handler);

		// reset the handler
		$this->handler = null;
	}


	/**
	 * Drops one or more tables
	 *
	 * @return	void
	 * @param	mixed $tables
	 */
	public function drop($tables)
	{
		// redefine var
		$table = (array) $table;

		// build query
		$query = 'DROP TABLE '. implode(', ', $tables) .';';

		// execute query
		$this->execute($query);
	}


	/**
	 * Executes a query & returns the last inserted or the number of affected rows
	 *
	 * @return	int
	 * @param	string $query
	 * @param	mixed[optional] $parameters
	 */
	public function execute($query, $parameters = array())
	{
		// connect if needed
		if(!$this->handler) $this->connect();

		// create query
		$query = $this->prepareQuery($query, $parameters);

		// execute query
		$result = @mysqli_query($this->handler, $query);
		if($result === false) throw new SpoonDatabaseException(mysqli_error($this->handler));
		if($result !== true) @mysqli_free_result($result);

		// return last insertId
		if(strtoupper(substr($query, 0, 6)) == 'INSERT') return (int) mysqli_insert_id($this->handler);

		// return affected rows
		return (int) mysqli_affected_rows($this->handler);
	}


	/**
	 * Gets a resultcolumn as an array
	 *
	 * @return	array
	 * @param	string $query
	 * @param	mixed[optional] $parameters
	 */
	public function getColumn($query, $parameters = array())
    {
    	// init var
    	$data = array();

    	// get values
    	$result = (array) $this->retrieve($query, $parameters);

    	// check if there are items
		if(empty($result)) return $data;

		// get keys
		$keys = array_keys($result[0]);
        foreach($result as $row) $data[] = $row[$keys[0]];

        // return data
        return $data;
    }


    /**
     * Get the debug-status
     *
     * @return	bool
     */
    public function getDebug()
    {
    	return $this->debug;
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
    	if(!isset($row['Type'])) throw new SpoonDatabaseException('There is no type-information available about this field', 0, $this->password);

    	// has a type but it's not an enum
    	if(strtolower(substr($row['Type'], 0, 4) != 'enum')) throw new SpoonDatabaseException('This field "'. (string) $field .'" isn\'t an enum field.', 0, $this->password);

    	// process values
    	$aSearch = array('enum', '(', ')', '\'');
    	$types = str_replace($aSearch, '', $row['Type']);

    	// return
    	return (array) explode(',', $types);
    }


	/**
	 * Gets the number of rows in a result
	 *
	 * @return	int
	 * @param	string $query
	 * @param 	mixed[optional] $parameters
	 */
	public function getNumRows($query, $parameters = array())
	{
		// init var
		$numRows = 0;

		// connect if needed
		if(!$this->handler) $status = $this->connect();

		// no connection could be made & strict = disabled (prevent infinite loops!)
		if($status === false) return false;

		// create query
		$query = $this->prepareQuery($query, $parameters);
		$numRows = @mysqli_num_rows(mysqli_query($this->handler, $query));

		// catch error
		if(mysqli_error($this->handler) != '') throw new SpoonDatabaseException(mysqli_error($this->handler), 0, $this->password);

		// free the result
		@mysqli_free_result($numRows);

		// return
		return (int) $numRows;
	}


	/**
	 * Gets the results as a key-value-pair
	 *
	 * @return	array
	 * @param	string $query
	 * @param	mixed[optional] $parameters
	 */
	public function getPairs($query, $parameters = array())
	{
    	// init var
    	$data = array();

    	// get values
    	$result = (array) $this->retrieve($query, $parameters);

    	// check if there are items
		if(empty($result)) return $data;

		// get keys
		$keys = array_keys($result[0]);
        foreach($result as $row) $data[$row[$keys[0]]] = $row[$keys[1]];

        // return data
        return $data;
	}


	/**
	 * Gets a single record
	 *
	 * @return	mixed
	 * @param	string $query
	 * @param	mixed[optiona] $parameters
	 */
	public function getRecord($query, $parameters = array())
	{
		// init var
		$data = null;

		// connect if needed
		if(!$this->handler) $this->connect();

		// create query
		$query = $this->prepareQuery($query, $parameters);
		if($result = @mysqli_query($this->handler, $query))
		{
			// fetch data
			$data = @mysqli_fetch_assoc($result);

			// free
			@mysqli_free_result($result);
		}

		// problem with the query
		else throw new SpoonDatabaseException(mysqli_error($this->handler), 0, $this->password);

		// return
		return $data;
	}


	/**
	 * An alias for retrieve
	 *
	 * @return	mixed
	 * @param	string $query
	 * @param	array[optional] $parameters
	 * @param	string[optional] $key
	 */
	public function getRecords($query, $parameters = array(), $key = null)
	{
		return $this->retrieve($query, $parameters, $key);
	}


	/**
	 * Gets a list with all tables
	 *
	 * @return	array
	 */
	public function getTablesList()
	{
		// init var
		$query = 'SHOW TABLES;';

		// get data
		return (array) $this->getColumn($query);
	}


	/**
	 * Returns a field
	 *
	 * @return	mixed
	 * @param	string $query
	 * @param	mixed[optional] $parameters
	 */
	public function getVar($query, $parameters = array())
	{
		// init var
		$result = (array) $this->getRecord($query, $parameters);

		// no result
		if(empty($result)) return null;

		// get keys
		$keys = array_keys($result);

		// return data
		return $result[$keys[0]];
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
		// validate
		if(empty($values)) throw new SpoonException('You need to provide values for an insert query.', 0, $this->password);

		// redefine var
		$values = (array) $values;

		// init vars
		$query = 'INSERT INTO '. (string) $table .' (';
		$valuesKeys = array_keys($values);
		$valuesValues = array_values($values);

		// count fields
		$iFields = count($values);

		// check if this is a multidim-array
		if(is_array($valuesValues[0]))
		{
			// init vars
			$valuesKeys = array_keys($values[0]);
			$iFields = count($valuesKeys);
			$parameters = array();

			// build query
			$query .= implode(', ', $valuesKeys);
			$query .= ') VALUES ';

			// init var
			$i = 0;
			$iValues = count($values);

			// loop rows
			foreach ($values as $row)
			{
				// check array
				$rowKeys = array_keys($row);
				$diff = array_diff_key($valuesKeys, $rowKeys);
				if(!empty($diff)) throw new SpoonDatabaseException('The keys have to be the same.', 0, $this->password);

				// build query
				$query .= '(';
				for ($t=0; $t<$iFields; $t++)
				{
					$query .= '?';
					if($t != $iFields - 1) $query .= ', ';
				}

				// cleanup
				if($i != $iValues - 1) $query .= '), ';

				// merge parameters
				$parameters = array_merge($parameters, array_values($row));

				// increment
				$i++;
			}
			// cleanup
			$query .= ');';
		}

		// singular array
		else
		{
			// build query
			$query .= implode(', ', $valuesKeys);
			$query .= ') VALUES (';
			for ($i=0; $i<$iFields; $i++)
			{
				$query .= '?';
				if($i != $iFields - 1) $query .= ', ';
			}

			// cleanup
			$query .= ');';

			// set parameters
			$parameters = $valuesValues;
		}

		// execute query
		return $this->execute($query, $parameters);
	}


	/**
	 * Optimize on or more tables
	 *
	 * @return	mixed
	 * @param	mixed $tables
	 */
	public function optimize($tables)
	{
		// redefine var
		$tables = (array) $tables;

		// build query
		$query = 'OPTIMIZE TABLE '. implode(', ', $tables) .';';

		return $this->retrieve($query);
	}


	/**
	 * Prepares the query
	 *
	 * @return	string
	 * @param	string $query
	 * @param	mixed[optional]	$parameters
	 */
	private function prepareQuery($query, $parameters)
	{
		// redefine var
		$parameters = (array) $parameters;

		// no parameters provided
		if(!empty($parameters))
		{
			// replace questionmarks
			$search = array('"?"', '\'?\'', '?');
			$query = str_replace($search, '%s', (string) $query, $countQuestionmarks);

			// count parameters
			$countParameters = count($parameters);

			// validate
			if($countQuestionmarks != $countParameters) throw new SpoonDatabaseException('The ammount of questionmarks need to be the same as number of parameters.', $this->password);

			// build replacement array
			foreach($parameters as $parameter)
			{
				switch(gettype($parameter))
				{
					// types that don't need encapsulation
					case 'boolean':
					case 'integer':
					case 'double':
						$aReplace[] = $parameter;
					break;
					case 'NULL':
						$aReplace[] = 'NULL';
					break;

					// unsupported types
					case 'array':
					case 'object':
					case 'resource':
						throw new SpoonDatabaseException('Unsupported parameter type "'. $parameter .'"', 0, $this->password);

					// handle it like a string
					default:
						$aReplace[] = '\''.mysqli_real_escape_string($this->handler, $parameter).'\'';
				}
			}

			// replace ? with real values
			$query = vsprintf($query, $aReplace);
		}

		// should we gather debug information
		if($this->debug)
		{
			$this->lastQuery = 	$query;
			$this->queries[] = $query;
			$this->numQueries++;
		}

		return $query;
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
		// init var
		$data = null;

		// connect if needed
		if(!$this->handler) $this->connect();

		// create query
		$query = $this->prepareQuery($query, $parameters);
		if($result = @mysqli_query($this->handler, $query))
		{
			// fetch data
			while($row = @mysqli_fetch_assoc($result))
			{
				// custom key
				if($key !== null && isset($row[(string) $key])) $data[$row[(string) $key]] = $row;

				// default value
				else $data[] = $row;
			}

			// free
			@mysqli_free_result($result);
		}

		// problems with query
		else throw new SpoonDatabaseException(mysqli_error($this->handler), 0, $this->password);

		return $data;
	}


	/**
	 * Set the debug-status
	 *
	 * @return	void
	 * @param	bool[optional] $on
	 */
	public function setDebug($on = true)
	{
		$this->debug = (bool) $on;
	}


	/**
	 * Truncate one or more tables
	 *
	 * @return	void
	 * @param	mixed $tables
	 */
	public function truncate($tables)
	{
		// redefine var
		$tables = (array) $tables;

		// loop table(s) and truncate
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
		// redefine vars
		$parameters = (array) $parameters;

		// validate
		if(empty($values)) throw new SpoonException('No values were given.', 0, $this->password);

		// init vars
		$tempParameters = array();
		$iValues = count($values);
		$i = 0;
		$query = 'UPDATE '. (string) $table .' SET ';

		// loop values
		foreach ($values as $key => $value)
		{
			// build query
			$query .= $key . ' = ?';
			if($i != $iValues - 1) $query .= ', ';

			// add parameters
			$tempParameters[] = $value;

			// increment
			$i++;
		}

		// build query
		if($where != '') $query .=' WHERE '. $where;
		$query .= ';';

		// merge parameters
		$parameters = array_merge($tempParameters, $parameters);

		// execute query
		return $this->execute($query, $parameters);
	}
}

?>