<?php
/**
 * Fork_Sniffs_PHP_DeprecatedIniDirectivesSniff.
 *
 * PHP version 5.3
 *
 * @category  PHP
 * @package   Fork
 * @author    Wim Godden <wim.godden@cu.be>
 * @copyright 2010 Cu.be Solutions bvba
 */

/**
 * Fork_Sniffs_PHP_DeprecatedIniDirectivesSniff.
 *
 * Discourages the use of deprecated INI directives through ini_set() or ini_get().
 *
 * @category  PHP
 * @package   Fork
 * @author    Wim Godden <wim.godden@cu.be>
 * @copyright 2010 Cu.be Solutions bvba
 */
class Fork_Sniffs_PHP_DeprecatedIniDirectivesSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * A list of deprecated INI directives
     *
     * @var array(string)
     */
    protected $deprecatedIniDirectives = array(
        'define_syslog_variables',
        'register_globals',
        'register_long_arrays',
//        'safe_mode',	// I know safe_mode is deprecated, but in our files it is just a check, nothing else
        'magic_quotes_gpc',
        'magic_quotes_runtime',
        'magic_quotes_sybase',
    );

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_STRING);

    }//end register()

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $ignore = array(
                   T_DOUBLE_COLON,
                   T_OBJECT_OPERATOR,
                   T_FUNCTION,
                   T_CONST,
                  );

        $prevToken = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
        if (in_array($tokens[$prevToken]['code'], $ignore) === true) {
            // Not a call to a PHP function.
            return;
        }

        $function = strtolower($tokens[$stackPtr]['content']);
        if ($function != 'ini_get' && $function != 'ini_set') {
            return;
        }
        $iniToken = $phpcsFile->findNext(T_CONSTANT_ENCAPSED_STRING, $stackPtr, null);
        if (in_array(str_replace("'", "", $tokens[$iniToken]['content']), $this->deprecatedIniDirectives) === false) {
            return;
        }
        $error = "INI directive " . $tokens[$iniToken]['content'] . " is deprecated.";

        $phpcsFile->addWarning($error, $stackPtr);

    }//end process()


}//end class
