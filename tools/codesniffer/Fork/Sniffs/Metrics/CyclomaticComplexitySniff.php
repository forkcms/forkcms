<?php

/**
 * Checks the cyclomatic complexity (McCabe) for functions.
 *
 * @author Greg Sherwood <gsherwood@squiz.net>
 * @author Marc McIntyre <mmcintyre@squiz.net>
 */
class Fork_Sniffs_Metrics_CyclomaticComplexitySniff implements PHP_CodeSniffer_Sniff
{
	/**
	 * A complexity higer than this value will throw an error.
	 *
	 * @todo needs to become 30 in the near future
	 *
	 * @var int
	 */
	protected $absoluteComplexity = 50;

	/**
	 * A complexity higher than this value will throw a warning.
	 *
	 * @todo needs to become 10 in the near future
	 *
	 * @var int
	 */
	protected $complexity = 30;

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile
	 * @param int $stackPtr
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		$this->currentFile = $phpcsFile;

		$tokens = $phpcsFile->getTokens();

		// ignore abstract methods.
		if(isset($tokens[$stackPtr]['scope_opener']) === false) return;

		// detect start and end of this function definition.
		$start = $tokens[$stackPtr]['scope_opener'];
		$end = $tokens[$stackPtr]['scope_closer'];

		// predicate nodes for PHP.
		$find = array(
			'T_CASE',
			'T_DEFAULT',
			'T_CATCH',
			'T_IF',
			'T_FOR',
			'T_FOREACH',
			'T_WHILE',
			'T_DO',
			'T_ELSEIF'
		);

		$complexity = 1;

		// iterate from start to end and count predicate nodes.
		for($i = ($start + 1); $i < $end; $i++)
		{
			if(in_array($tokens[$i]['type'], $find) === true)
			{
				$complexity++;
			}
		}

		if($complexity > $this->complexity)
		{
			$warning = "Function's cyclomatic complexity ($complexity) exceeds ". $this->complexity .'; consider refactoring the function';
			$phpcsFile->addWarning($warning, $stackPtr);
		}

		return;
	}

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(T_FUNCTION);
	}
}
