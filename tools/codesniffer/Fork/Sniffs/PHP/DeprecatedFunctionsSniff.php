<?php
/**
 * Fork_Sniffs_PHP_DeprecatedFunctionsSniff.
 *
 * PHP version 5.3
 *
 * @category  PHP
 * @package   Fork
 * @author    Wim Godden <wim.godden@cu.be>
 * @copyright 2010 Cu.be Solutions bvba
 */

/**
 * Fork_Sniffs_PHP_DeprecatedFunctionsSniff.
 *
 * @category  PHP
 * @package   Fork
 * @author    Wim Godden <wim.godden@cu.be>
 * @version   1.0.0
 * @copyright 2010 Cu.be Solutions bvba
 */
class Fork_Sniffs_PHP_DeprecatedFunctionsSniff extends Generic_Sniffs_PHP_ForbiddenFunctionsSniff
{

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
                                        'define_syslog_variables' => null,
                                        'dl' => null,
                                        'ereg' => 'preg_match',
                                        'ereg_replace' => 'preg_replace',
                                        'eregi' => 'preg_match',
                                        'eregi_replace' => 'preg_replace',
                                        'set_magic_quotes_runtime' => null,
                                        'magic_quotes_runtime' => null,
                                        'session_register' => 'use $_SESSION',
                                        'session_unregister' => 'use $_SESSION',
                                        'session_is_registered' => 'use $_SESSION',
                                        'set_socket_blocking' => 'stream_set_blocking',
                                        'split' => 'preg_split',
                                        'spliti' => 'preg_split',
                                        'sql_regcase' => null,
                                        'mysql_db_query' => 'mysql_select_db and mysql_query',
                                        'mysql_escape_string' => 'mysql_real_escape_string',
                                    );

    /**
     * If true, an error will be thrown; otherwise a warning.
     *
     * @var bool
     */
    public $error = false;

}//end class
