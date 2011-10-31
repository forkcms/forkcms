<?php

/**
 * Check if constants meet the standards.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Fork_Sniffs_Styleguide_ConstantsSniff implements PHP_CodeSniffer_Sniff
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
		$next = $tokens[$stackPtr + 1];

		// spaces after const
		if($next['content'] != ' ')
		{
			$phpcsFile->addError('After "const" we expect exactly one space.', $stackPtr);
		}

		// space after constant name
		if($tokens[$stackPtr + 3]['content'] != ' ')
		{
			$phpcsFile->addError('After the constant-name we expect exactly one space.', $stackPtr);
		}

		// space after equal sign
		if($tokens[$stackPtr + 4]['code'] != T_EQUAL)
		{
			$phpcsFile->addError('After the constant-name we expect a space and the equal sign.', $stackPtr);
		}

		// get name
		$constantName = $tokens[$stackPtr + 2]['content'];

		// check case
		if(strcmp($constantName, mb_strtoupper($constantName)) !== 0)
		{
			$phpcsFile->addError('A constant should use uppercase characters.', $stackPtr);
		}

		// cleanup
		unset($tokens);
		unset($next);
	}

	/**
	 * Registers on constant definitions.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(T_CONST);
	}
}
