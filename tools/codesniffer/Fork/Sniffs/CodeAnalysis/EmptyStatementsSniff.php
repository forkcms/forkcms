<?php

/**
 * This sniff class detected empty statement.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Fork_Sniffs_CodeAnalysis_EmptyStatementsSniff implements PHP_CodeSniffer_Sniff
{
	/**
	 * Process the code.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile
	 * @param int $stackPtr
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		$tokens = $phpcsFile->getTokens();
		$current = $tokens[$stackPtr];

		// skip for-statements without body.
		if(isset($current['scope_opener']) === false) return;

		// get next
		$next = ++$current['scope_opener'];
		$end = --$current['scope_closer'];

		$emptyBody = true;
		for(; $next <= $end; ++$next)
		{
			if(in_array($tokens[$next]['code'], array(T_WHITESPACE)) === false)
			{
				$emptyBody = false;
				break;
			}
		}

		if($emptyBody === true)
		{
			$name = $phpcsFile->getTokensAsString($stackPtr, 1);
			$error = sprintf('Empty %s statement detected', strtoupper($name));
			$phpcsFile->addWarning($error, $stackPtr);
		}

		// cleanup
		unset($tokens);
		unset($current);
		unset($next);
		unset($end);
	}

	/**
	 * Register on control structures.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(T_CATCH, T_DO, T_ELSE, T_ELSEIF, T_FOR, T_FOREACH, T_IF, T_SWITCH, T_TRY, T_WHILE);
	}
}
