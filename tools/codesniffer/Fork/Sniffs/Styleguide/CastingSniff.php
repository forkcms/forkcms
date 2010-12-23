<?php

/**
 * Fork_Sniffs_Styleguide_CastingSniff
 * Checks if casting is as described in the styleguide
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
class Fork_Sniffs_Styleguide_CastingSniff implements PHP_CodeSniffer_Sniff
{
	public function register()
	{
		// register on (array), (bool), ...
		return array(T_ARRAY_CAST, T_BOOL_CAST, T_DOUBLE_CAST, T_INT_CAST, T_OBJECT_CAST, T_STRING_CAST, T_UNSET_CAST);
	}


	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		// get the tokens
		$tokens = $phpcsFile->getTokens();
		$current = $tokens[$stackPtr];

		// get all lines
		$lines = file($phpcsFile->getFilename());

		// get previous token
		$previous = $tokens[$stackPtr - 1];
		// get nex token
		$next = $tokens[$stackPtr + 1];

		// handle all types
		switch($current['code'])
		{
			// there should be exactly one space after the closing brace
			case T_ARRAY_CAST:
			case T_BOOL_CAST:
			case T_DOUBLE_CAST:
			case T_INT_CAST:
			case T_OBJECT_CAST:
			case T_STRING_CAST:
			case T_UNSET_CAST:
				if($next['content'] != ' ') $phpcsFile->addWarning('Space excpected after cast', $stackPtr);
			break;
		}

		// cleanup
		unset($tokens);
		unset($current);
		unset($lines);
		unset($next);
		unset($previous);
	}
}

?>