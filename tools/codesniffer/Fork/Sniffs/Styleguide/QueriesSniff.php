<?php

/**
 * Check if queries meet the standards
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Fork_Sniffs_Styleguide_QueriesSniff implements PHP_CodeSniffer_Sniff
{
	/**
	 * Processes the code.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile
	 * @param int $stackPtr
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		// get the tokens
		$tokens = $phpcsFile->getTokens();
		$current = $tokens[$stackPtr];
		$lines = file($phpcsFile->getFilename());
		$next = $tokens[$stackPtr + 1];

		// queries
		if(in_array($next['content'], array('delete', 'update')))
		{
			// don't doe anything
		}

		// queries
		if(in_array($next['content'], array('execute', 'getColumn', 'getNumRows', 'getPairs', 'getRecord', 'getRecords', 'getVar', 'retrieve')))
		{
			// getRecords is prefered
			if($next['content'] == 'retrieve') $phpcsFile->addError('We use getRecords instead of retrieve', $stackPtr);

			$stringStart = $phpcsFile->findNext(T_CONSTANT_ENCAPSED_STRING, $stackPtr);
			$stringEnd = $phpcsFile->findNext(T_CLOSE_PARENTHESIS, $stackPtr) - 1;

			if(strpos($lines[$tokens[$stringStart]['line'] -1], '->' . $next['content'] . '(\'') > 0)
			{
				// init var
				$query = '';

				// build query
				for($i = $tokens[$stringStart]['line'] -1; $i <= $tokens[$stringEnd]['line'] -1; $i++)
				{
					$query .= $lines[$i];
				}

				// find query
				$firstQuote = strpos($query, "'");

				// find the first quote
				if($firstQuote !== false)
				{
					// don't allow queries to end with ;
					if(strpos($query, ';\',', $firstQuote + 1) !== false)
					{
						$phpcsFile->addError('No ; allowed on end of query', $stackPtr);
					}

					$query = substr($query, $firstQuote + 1);
					$mysqlKeywords = array(
						'ACCESSIBLE', 'ADD', 'ALL', 'ALTER', 'ANALYZE', 'AND', 'AS', 'ASC',
						'ASENSITIVE', 'BEFORE', 'BETWEEN', 'BIGINT', 'BINARY', 'BLOB', 'BOTH', 'BY',
						'CALL', 'CASCADE', 'CASE', 'CHANGE', 'CHAR', 'CHARACTER', 'CHECK', 'COLLATE',
						'COLUMN', 'CONDITION', 'CONSTRAINT', 'CONTINUE', 'CONVERT', 'CREATE', 'CROSS',
						'CURRENT_DATE', 'CURRENT_TIME', 'CURRENT_TIMESTAMP', 'CURRENT_USER', 'CURSOR',
						'DATABASE', 'DATABASES', 'DAY_HOUR', 'DAY_MICROSECOND', 'DAY_MINUTE',
						'DAY_SECOND', 'DEC', 'DECIMAL', 'DECLARE', 'DEFAULT', 'DELAYED', 'DELETE',
						'DESC', 'DESCRIBE', 'DETERMINISTIC', 'DISTINCT', 'DISTINCTROW', 'DIV',
						'DOUBLE', 'DROP', 'DUAL', 'EACH', 'ELSE', 'ELSEIF', 'ENCLOSED', 'ESCAPED',
						'EXISTS', 'EXIT', 'EXPLAIN', 'FALSE', 'FETCH', 'FLOAT', 'FLOAT4', 'FLOAT8',
						'FOR', 'FORCE', 'FOREIGN', 'FROM', 'FULLTEXT', 'GOTO', 'GRANT', 'GROUP',
						'HAVING', 'HIGH_PRIORITY', 'HOUR_MICROSECOND', 'HOUR_MINUTE', 'HOUR_SECOND',
						'IF', 'IGNORE', 'IN', 'INDEX', 'INFILE', 'INNER', 'INOUT', 'INSENSITIVE',
						'INSERT', 'INT', 'INT1', 'INT2', 'INT3', 'INT4', 'INT8', 'INTEGER',
						'INTERVAL', 'INTO', 'IS', 'ITERATE', 'JOIN', 'KEY', 'KEYS', 'KILL', 'LEADING',
						'LEAVE', 'LEFT', 'LIKE', 'LIMIT', 'LINEAR', 'LINES', 'LOAD', 'LOCALTIME',
						'LOCALTIMESTAMP', 'LOCK', 'LONG', 'LONGBLOB', 'LONGTEXT', 'LOOP',
						'LOW_PRIORITY', 'MASTER_SSL_VERIFY_SERVER_CERT', 'MATCH', 'MEDIUMBLOB',
						'MEDIUMINT', 'MEDIUMTEXT', 'MIDDLEINT', 'MINUTE_MICROSECOND', 'MINUTE_SECOND',
						'MOD', 'MODIFIES', 'NATURAL', 'NOT', 'NO_WRITE_TO_BINLOG', 'NULL', 'NUMERIC',
						'ON', 'OPTIMIZE', 'OPTION', 'OPTIONALLY', 'OR', 'ORDER', 'OUT', 'OUTER',
						'OUTFILE', 'PRECISION', 'PRIMARY', 'PROCEDURE', 'PURGE', 'RANGE', 'READ',
						'READS', 'READ_ONLY', 'READ_WRITE', 'REAL', 'REFERENCES', 'REGEXP', 'RELEASE',
						'RENAME', 'REPEAT', 'REPLACE', 'REQUIRE', 'RESTRICT', 'RETURN', 'REVOKE',
						'RIGHT', 'RLIKE', 'SCHEMA', 'SCHEMAS', 'SECOND_MICROSECOND', 'SELECT',
						'SENSITIVE', 'SEPARATOR', 'SET', 'SHOW', 'SMALLINT', 'SPATIAL', 'SPECIFIC',
						'SQL', 'SQLEXCEPTION', 'SQLSTATE', 'SQLWARNING', 'SQL_BIG_RESULT',
						'SQL_CALC_FOUND_ROWS', 'SQL_SMALL_RESULT', 'SSL', 'STARTING', 'STRAIGHT_JOIN',
						'TABLE', 'TERMINATED', 'THEN', 'TINYBLOB', 'TINYINT', 'TINYTEXT', 'TO',
						'TRAILING', 'TRIGGER', 'TRUE', 'UNDO', 'UNION', 'UNIQUE', 'UNLOCK',
						'UNSIGNED', 'UPDATE', 'UPGRADE', 'USAGE', 'USE', 'USING', 'UTC_DATE',
						'UTC_TIME', 'UTC_TIMESTAMP', 'VALUES', 'VARBINARY', 'VARCHAR', 'VARCHARACTER',
						'VARYING', 'WHEN', 'WHERE', 'WHILE', 'WITH', 'WRITE', 'XOR', 'YEAR_MONTH',
						'ZEROFILL'
					);

					// loop keywords
					foreach($mysqlKeywords as $keyword)
					{
						// get position
						$pos = strpos($query, strtolower($keyword));

						// check if the join-type is defined
						if($keyword == 'JOIN' && strpos($query,$keyword) !== false && strpos($query, 'INNER JOIN') === false && strpos($query, 'LEFT OUTER JOIN') === false &&	strpos($query, 'RIGHT OUTER JOIN') === false)
						{
							$phpcsFile->addError('You need to specify the type of JOIN.', $stackPtr);
						}

						// anything found?
						if($pos !== false)
						{
							// get previous & next char
							$before = substr($query, $pos - 1, 1);
							$after = substr($query, $pos + strlen($keyword), 1);

							// validate
							if(!in_array($before, array('', ' ', "\n", "\t"))) continue;
							if(!in_array($after, array('', ' ', "\n", "\t"))) continue;

							// add warning
							$phpcsFile->addError($keyword . ' should be uppercase', $stackPtr);
						}
					}
				}
			}
		}

		unset($tokens);
		unset($current);
		unset($lines);
		unset($next);
	}

	/**
	 * Registers on objects.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(T_OBJECT_OPERATOR);
	}
}
