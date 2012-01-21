<?php

/**
 * @author Wim Godden <wim.godden@cu.be>
 */
class Fork_Sniffs_PHP_DeprecatedFunctionsSniff extends Generic_Sniffs_PHP_ForbiddenFunctionsSniff
{
	/**
	 * If true, an error will be thrown; otherwise a warning.
	 *
	 * @var bool
	 */
	public $error = false;

	/**
	 * A list of forbidden functions with their alternatives.
	 *
	 * The value is NULL if no alternative exists. IE, the
	 * function should just not be used.
	 *
	 * @var array(string => string|null)
	 */
	protected $forbiddenFunctions = array(
		'call_user_method' => 'call_user_func',
		'call_user_method_array' => 'call_user_func_array',
		'copy' => 'SpoonDirectory::copy',
		'define_syslog_variables' => null,
		'die' => 'exit',
		'dl' => null,
		'ereg' => 'preg_match',
		'ereg_replace' => 'preg_replace',
		'eregi' => 'preg_match',
		'eregi_replace' => 'preg_replace',
		'eval' => null,
		'exec' => null,
		'is_uploaded_file' => null,
		'magic_quotes_runtime' => null,
		'move_uploaded_file' => null,
		'mysql_connect' => null,
		'mysql_db_query' => null,
		'mysql_error' => null,
		'mysql_escape_string' => 'mysql_real_escape_string',
		'mysql_query' => null,
		'print_r' => 'Spoon::dump',
		'session_is_registered' => 'use $_SESSION',
		'session_register' => 'use $_SESSION',
		'session_unregister' => 'use $_SESSION',
		'set_magic_quotes_runtime' => null,
		'set_socket_blocking' => 'stream_set_blocking',
		'split' => 'preg_split',
		'spliti' => 'preg_split',
		'sql_regcase' => null,
		'unlink' => 'SpoonFile::delete'
	);
}
