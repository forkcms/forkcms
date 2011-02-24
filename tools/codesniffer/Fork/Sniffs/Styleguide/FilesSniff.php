<?php

/**
 * Fork_Sniffs_Styleguide_FilesSniff
 * Checks if a file is formated as described in the styleguide
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
class Fork_Sniffs_Styleguide_FilesSniff implements PHP_CodeSniffer_Sniff
{
	public function register()
	{
		// register on open en clsong PHP-tag
		return array(T_OPEN_TAG, T_CLOSE_TAG);
	}


	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		// check line endings
		if($phpcsFile->eolChar !== "\n") $phpcsFile->addError('We expect lines to end with "\n".', $stackPtr);

		// get the tokens
		$tokens = $phpcsFile->getTokens();

		// check if there is a newline after the opening tag
		if(T_OPEN_TAG === $tokens[$stackPtr]['code'])
		{
			// check if next line is empty
			if($tokens[$stackPtr + 1]['content'] != "\n") $phpcsFile->addError('After "<?php" we expect an empty line.', $stackPtr);
		}

		// check if there is a before after the closing tag
		if(T_CLOSE_TAG === $tokens[$stackPtr]['code'])
		{
			// get all lines
			$lines = file($phpcsFile->getFilename());

			// check if line before tag is empty
			if(isset($lines[$tokens[$stackPtr]['line'] - 2]) && $lines[$tokens[$stackPtr]['line'] - 2] != "\n") $phpcsFile->addError('Before "?>" we expect an empty line.', $stackPtr);
		}

		// cleanup
		unset($tokens);
		unset($lines);
	}
}

?>