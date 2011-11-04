<?php

/**
 * Discourages the use of assigning the return value of new by reference
 *
 * @author Wim Godden <wim.godden@cu.be>
 */
class Fork_Sniffs_PHP_DeprecatedNewReferenceSniff implements PHP_CodeSniffer_Sniff
{
	/**
	 * If true, an error will be thrown; otherwise a warning.
	 *
	 * @var bool
	 */
	protected $error = true;

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile
	 * @param int $stackPtr
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		$tokens = $phpcsFile->getTokens();
		if($tokens[$stackPtr - 1]['type'] == 'T_BITWISE_AND' || $tokens[$stackPtr - 2]['type'] == 'T_BITWISE_AND')
		{
			$error = 'Assigning the return value of new by reference is deprecated in PHP 5.3';
			$phpcsFile->addError($error, $stackPtr);
		}
	}

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(T_NEW);
	}
}
