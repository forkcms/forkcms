<?php

/**
 * Fork_Sniffs_Styleguide_CommentsSniff
 * Checks if comments are formated as described in the styleguide
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
class Fork_Sniffs_Styleguide_CommentsSniff implements PHP_CodeSniffer_Sniff
{
	public function register()
	{
		// register on open en clsong PHP-tag
		return array(T_COMMENT);
	}


	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		// get the tokens
		$tokens = $phpcsFile->getTokens();

		// check if there is a newline after the opening tag
		if( substr($tokens[$stackPtr]['content'], 0, 2) == '//' && substr($tokens[$stackPtr]['content'], 0, 3) !== '// ') $phpcsFile->addError('After "//" we expect a space.', $stackPtr);

		// cleanup
		unset($tokens);
		unset($lines);
	}
}

?>