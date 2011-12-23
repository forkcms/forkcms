<?php

/**
 * Checks if comments are formatted as described in the styleguide
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
class Fork_Sniffs_Styleguide_CommentsSniff implements PHP_CodeSniffer_Sniff
{
	/**
	 * Processes the code.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile
	 * @param unknown_type $stackPtr
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		// get the tokens
		$tokens = $phpcsFile->getTokens();

		// check if there is a newline after the opening tag
		if(substr($tokens[$stackPtr]['content'], 0, 2) == '//' && substr($tokens[$stackPtr]['content'], 0, 3) !== '// ')
		{
			$phpcsFile->addError('After "//" we expect one space.', $stackPtr);
		}

		unset($tokens);
		unset($lines);
	}

	/**
	 * Register on comments.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(T_COMMENT);
	}
}
