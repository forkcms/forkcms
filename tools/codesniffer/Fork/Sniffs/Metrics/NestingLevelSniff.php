<?php

/**
 * Checks the nesting level for methods.

 * @author Greg Sherwood <gsherwood@squiz.net>
 * @author Marc McIntyre <mmcintyre@squiz.net>
 */
class Fork_Sniffs_Metrics_NestingLevelSniff implements PHP_CodeSniffer_Sniff
{
	/**
	 * A nesting level greater than this value will throw an error.
	 *
	 * @var int
	 */
	protected $absoluteNestingLevel = 10;

	/**
	 * A nesting level greater than this value will throw a warning.
	 *
	 * @var int
	 */
	protected $nestingLevel = 5;

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile
	 * @param int $stackPtr
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		$tokens = $phpcsFile->getTokens();

		// ignore abstract methods.
		if(isset($tokens[$stackPtr]['scope_opener']) === false) return;

		// detect start and end of this function definition.
		$start = $tokens[$stackPtr]['scope_opener'];
		$end = $tokens[$stackPtr]['scope_closer'];

		$nestingLevel = 0;

		// find the maximum nesting level of any token in the function.
		for($i = ($start + 1); $i < $end; $i++)
		{
			$level = $tokens[$i]['level'];
			if($nestingLevel < $level)
			{
				$nestingLevel = $level;
			}
		}

		// we subtract the nesting level of the function itself.
		$nestingLevel = ($nestingLevel - $tokens[$stackPtr]['level'] - 1);

		if($nestingLevel > $this->absoluteNestingLevel)
		{
			$error = "Function's nesting level ($nestingLevel) exceeds allowed maximum of " . $this->absoluteNestingLevel;
			$phpcsFile->addError($error, $stackPtr);
		}

		elseif($nestingLevel > $this->nestingLevel)
		{
			$warning = "Function's nesting level ($nestingLevel) exceeds " . $this->nestingLevel.'; consider refactoring the function';
			$phpcsFile->addWarning($warning, $stackPtr);
		}
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
