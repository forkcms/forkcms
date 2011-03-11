<?php

/**
 * Fork_Sniffs_Styleguide_OperatorsSniff
 * Checks if operators are used like described in the styleguide
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
class Fork_Sniffs_Styleguide_OperatorsSniff implements PHP_CodeSniffer_Sniff
{
	public function register()
	{
		// register on (array), (bool), ...
		return array(T_BOOLEAN_AND, T_BOOLEAN_OR, T_INSTANCEOF, T_IS_EQUAL, T_IS_GREATER_OR_EQUAL, T_IS_IDENTICAL, T_IS_NOT_EQUAL, T_IS_NOT_IDENTICAL, T_IS_SMALLER_OR_EQUAL, T_LOGICAL_AND, T_LOGICAL_OR, T_LOGICAL_XOR, T_DEC, T_INC);
	}


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
			case T_LOGICAL_AND:
			case T_LOGICAL_OR:
			case T_LOGICAL_XOR:
				$phpcsFile->addWarning('We use &&, ||, !=.', $stackPtr);
				if($previous['content'] != ' ') $phpcsFile->addError('Before an operator we expect exactly one space.', $stackPtr);
				if($next['content'] != ' ') $phpcsFile->addError('After an operator we expect exactly one space.', $stackPtr);
			break;

			// there should be exactly one space before and one after
			case T_BOOLEAN_AND:
			case T_BOOLEAN_OR:
			case T_INSTANCEOF:
			case T_IS_EQUAL:
			case T_IS_GREATER_OR_EQUAL:
			case T_IS_IDENTICAL:
			case T_IS_NOT_EQUAL:
			case T_IS_NOT_IDENTICAL:
			case T_IS_SMALLER_OR_EQUAL:
			case T_OR_EQUAL:
			case T_AND_EQUAL:
			case T_PLUS_EQUAL:
			case T_SL_EQUAL:
			case T_SR_EQUAL:
			case T_XOR_EQUAL:
			case T_DIV_EQUAL:
			case T_MINUS_EQUAL:
			case T_MOD_EQUAL:
			case T_MUL_EQUAL:
			case T_SL:
			case T_SR:
				if($previous['content'] != ' ') $phpcsFile->addError('Before an operator(&&, || instanceof, ==, >=, ===, !==, <=, +=, .=, -=, *=) we expect exactly one space.', $stackPtr);
				if($next['content'] != ' ') $phpcsFile->addError('After an operator(&&, || instanceof, ==, >=, ===, !==, <=, +=, .=, -=, *=) we expect exactly one space.', $stackPtr);
			break;

			// for increments we don't care
			case T_DEC:
			case T_INC:
			break;
		}

		// cleanup
		unset($tokens);
		unset($current);
		unset($next);
		unset($previous);
	}
}

?>