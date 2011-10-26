<?php

/**
 * Checks if a file is formatted as described in the styleguide
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Fork_Sniffs_Styleguide_FilesSniff implements PHP_CodeSniffer_Sniff
{
	/**
	 * Processes the code.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile
	 * @param int $stackPtr
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		// check line endings
		if($phpcsFile->eolChar !== "\n")
		{
			$phpcsFile->addError('We expect lines to end with "\n".', $stackPtr);
		}

		// get the tokens
		$tokens = $phpcsFile->getTokens();

		// check if there is a newline after the opening tag
		if(T_OPEN_TAG === $tokens[$stackPtr]['code'])
		{
			// check if next line is empty
			if($tokens[$stackPtr + 1]['content'] != "\n")
			{
				$phpcsFile->addError('After "<?php" we expect a blank line.', $stackPtr);
			}
		}

		// check if there is a before after the closing tag
		if(T_CLOSE_TAG === $tokens[$stackPtr]['code'])
		{
			$phpcsFile->addError('We don\'t use the closing "?>" tag!', $stackPtr);
		}

		unset($tokens);
		unset($lines);
	}

	/**
	 * Registers on php opening/closing tags
	 *
	 * @return array
	 */
	public function register()
	{
		return array(T_OPEN_TAG, T_CLOSE_TAG);
	}
}
