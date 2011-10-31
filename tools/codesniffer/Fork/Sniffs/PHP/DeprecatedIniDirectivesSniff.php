<?php

/**
 * Discourages the use of deprecated INI directives through ini_set() or ini_get().
 *
 * @author Wim Godden <wim.godden@cu.be>
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
		'magic_quotes_gpc',
		'magic_quotes_runtime',
		'magic_quotes_sybase'
	);

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile
	 * @param int $stackPtr
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		$tokens = $phpcsFile->getTokens();
		$ignore = array(
			T_DOUBLE_COLON,
			T_OBJECT_OPERATOR,
			T_FUNCTION,
			T_CONST
		);

		$prevToken = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
		if(in_array($tokens[$prevToken]['code'], $ignore) === true) return;

		$function = strtolower($tokens[$stackPtr]['content']);
		if($function != 'ini_get' && $function != 'ini_set')
		{
			return;
		}

		$iniToken = $phpcsFile->findNext(T_CONSTANT_ENCAPSED_STRING, $stackPtr, null);
		if(in_array(str_replace("'", "", $tokens[$iniToken]['content']), $this->deprecatedIniDirectives) === false)
		{
			return;
		}

		$error = "INI directive " . $tokens[$iniToken]['content'] . " is deprecated.";
		$phpcsFile->addWarning($error, $stackPtr);
	}

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(T_STRING);
	}
}
