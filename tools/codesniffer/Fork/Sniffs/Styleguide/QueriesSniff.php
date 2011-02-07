<?php

/**
 * Fork_Sniffs_Styleguide_QueriesSniff
 * Check if queries meet the standards
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
class Fork_Sniffs_Styleguide_QueriesSniff implements PHP_CodeSniffer_Sniff
{
	public function register()
	{
		return array(T_OBJECT_OPERATOR);
	}


	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		// @todo	no ; on end of the queries?

		// get the tokens
		$tokens = $phpcsFile->getTokens();
		$current = $tokens[$stackPtr];
		$lines = file($phpcsFile->getFilename());
		$next = $tokens[$stackPtr + 1];

		// queries
		if(in_array($next['content'], array('delete', 'update')))
		{
//			var_dump($next);
//			exit;
		}

		// queries
		if(in_array($next['content'], array('execute', 'getColumn', 'getNumRows', 'getPairs', 'getRecord', 'getRecords', 'getVar', 'retrieve')))
		{
			// getRecords is prefered
			if($next['content'] == 'retrieve') $phpcsFile->addError('We use getRecords instead of retrieve', $stackPtr);

			$stringStart = $phpcsFile->findNext(T_CONSTANT_ENCAPSED_STRING, $stackPtr);
			$stringEnd = $phpcsFile->findNext(T_CLOSE_PARENTHESIS, $stackPtr) - 1;

			// init var
			$query = '';

			// build query
			for($i = $tokens[$stringStart]['line'] -1; $i <= $tokens[$stringEnd]['line'] -1; $i++)
			{
				$query .= $lines[$i];
			}

			// find query
			$firstQuote = strpos($query, "'");

			// find the first quote
			if($firstQuote !== false)
			{
				// search for ; on end of query
				if(strpos($query, ';\',', $firstQuote + 1) !== false) $phpcsFile->addError('No ; allowed on end of query', $stackPtr);
			}
		}

		// cleanup
		unset($tokens);
		unset($current);
		unset($lines);
		unset($next);
	}
}

?>