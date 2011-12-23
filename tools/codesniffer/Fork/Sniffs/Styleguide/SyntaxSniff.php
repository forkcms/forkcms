<?php

/**
 * Checks if some elements are meeting the standards
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Fork_Sniffs_Styleguide_SyntaxSniff implements PHP_CodeSniffer_Sniff
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
		$previous = $tokens[$stackPtr - 1];
		$next = $tokens[$stackPtr + 1];

		// handle all types
		switch($current['code'])
		{
			// there should be exactly one space before and after
			case T_AS:

				// one space before
				if($previous['content'] != ' ')
				{
					$phpcsFile->addError('Before "as" we expect exactly one space.', $stackPtr);
				}

				// one space after
				if($next['content'] != ' ')
				{
					$phpcsFile->addError('After "as" we expect exactly one space.', $stackPtr);
				}
				break;

			// there should be exactly one space before and after
			case T_DOUBLE_ARROW:

				// one space before
				if($previous['content'] != ' ')
				{
					$phpcsFile->addError('Before "=>" we expect exactly one space.', $stackPtr);
				}

				// one space after
				if($next['content'] != ' ')
				{
					$phpcsFile->addError('After "=>" we expect exactly one space.', $stackPtr);
				}
				break;

			// no whitespace before/after ::, ->
			case T_DOUBLE_COLON:
			case T_PAAMAYIM_NEKUDOTAYIM:
			case T_OBJECT_OPERATOR:

				// no spaces before
				if($previous['code'] == T_WHITESPACE)
				{
					$phpcsFile->addError('Before "::" or "->" we don\'t allow whitespaces.', $stackPtr);
				}

				// no spaces after
				if($next['code'] == T_WHITESPACE)
				{
					$phpcsFile->addError('After "::" or "->" we don\'t allow whitespaces.', $stackPtr);
				}
				break;

			// there should be exactly one space after
			case T_NEW:
				if($next['content'] != ' ')
				{
					$phpcsFile->addError('After "new" we expect exactly one space.', $stackPtr);
				}
				break;
		}

		unset($tokens);
		unset($current);
		unset($next);
		unset($previous);
	}

	/**
	 * Register on some general tokens.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(
			T_AS,
			T_NEW,
			T_DOUBLE_ARROW,
			T_DOUBLE_COLON,
			T_OBJECT_OPERATOR,
			T_PAAMAYIM_NEKUDOTAYIM
		);
	}
}
