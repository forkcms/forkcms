<?php

/**
 * Checks if modifiers are used as described in the styleguide
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Fork_Sniffs_Styleguide_ModifiersSniff implements PHP_CodeSniffer_Sniff
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
		$next = $tokens[$stackPtr + 1];

		// handle all types
		switch($current['code'])
		{
			// there should be exactly one space before and one after
			case T_GLOBAL:
				$phpcsFile->addWarning('Are you really sure you need this variable to be global?', $stackPtr);
				break;

			case T_ABSTRACT:
			case T_FINAL:
			case T_PRIVATE:
			case T_PROTECTED:
			case T_PUBLIC:
			case T_STATIC:

				// one space after
				if($next['content'] != ' ')
				{
					$phpcsFile->addError('After "abstract", "final", "private", "protected", "public", "static" we expect exactly one space.', $stackPtr);
				}
				break;
		}

		unset($tokens);
		unset($current);
		unset($next);
	}

	/**
	 * Register on modifiers.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(T_ABSTRACT, T_FINAL, T_GLOBAL, T_PRIVATE, T_PUBLIC, T_PROTECTED, T_STATIC);
	}
}
