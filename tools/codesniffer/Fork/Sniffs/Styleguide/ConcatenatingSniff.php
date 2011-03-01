<?php

/**
 * Fork_Sniffs_Styleguide_ConcatenatingSniff
 * Checks if contactenating is used like described in the styleguide
 *
 * @author	Davy Hellemans <davy@spoon-library.com>
 */
class Fork_Sniffs_Styleguide_ConcatenatingSniff implements PHP_CodeSniffer_Sniff
{
	public function register()
	{
		// register on (array), (bool), ...
		return array(T_STRING_CONCAT);
	}


	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		// get the tokens
		$tokens = $phpcsFile->getTokens();
		$current = $tokens[$stackPtr];
		$previous = $tokens[$stackPtr - 1];
		$next = $tokens[$stackPtr + 1];

		// space before and after
		if($previous['content'] != ' ' || $next['content'] != ' ') $phpcsFile->addError('Concat operator must be surrounded by spaces', $stackPtr);

		// cleanup
		unset($tokens);
		unset($current);
		unset($next);
		unset($previous);
	}
}

?>