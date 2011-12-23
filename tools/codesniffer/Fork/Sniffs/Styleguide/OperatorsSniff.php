<?php

/**
 * Checks if operators are used like described in the styleguide
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Fork_Sniffs_Styleguide_OperatorsSniff implements PHP_CodeSniffer_Sniff
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
			case T_LOGICAL_AND:
			case T_LOGICAL_OR:
			case T_LOGICAL_XOR:
				// no "and", "or", ...
				$phpcsFile->addWarning('We use &&, ||, !=', $stackPtr);

				// one space before an operator
				if($previous['content'] != ' ')
				{
					$phpcsFile->addError('Before an operator we expect exactly one space.', $stackPtr);
				}

				// one space after an operator
				if($next['content'] != ' ')
				{
					$phpcsFile->addError('After an operator we expect exactly one space.', $stackPtr);
				}
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

				// one space before
				if($previous['content'] != ' ')
				{
					$phpcsFile->addError('Before an operator(&&, || instanceof, ==, >=, ===, !==, <=, +=, .=, -=, *=) we expect exactly one space.', $stackPtr);
				}

				// one space after
				if($next['content'] != ' ')
				{
					$phpcsFile->addError('After an operator(&&, || instanceof, ==, >=, ===, !==, <=, +=, .=, -=, *=) we expect exactly one space.', $stackPtr);
				}
				break;

			// we don't care about increments
			case T_DEC:
			case T_INC:
				break;

			case T_MINUS:
			case T_PLUS:

				// one space before
				if($previous['content'] != ' ')
				{
					$phpcsFile->addError('Before an operator(+, -, *, /, %) we expect exactly one space.', $stackPtr);
				}

				// one space after
				if($next['content'] != ' ' && !is_numeric($next['content']))
				{
					$phpcsFile->addError('After an operator(+, -, *, /, %) we expect exactly one space.', $stackPtr);
				}
				break;

			case T_MULTIPLY:
			case T_DIVIDE:
			case T_MODULUS:

				// one space before
				if($previous['content'] != ' ')
				{
					$phpcsFile->addError('Before an operator(+, -, *, /, %) we expect exactly one space.', $stackPtr);
				}

				// one space after
				if($next['content'] != ' ')
				{
					$phpcsFile->addError('After an operator(+, -, *, /, %) we expect exactly one space.', $stackPtr);
				}
			break;
		}

		unset($tokens);
		unset($current);
		unset($next);
		unset($previous);
	}

	/**
	 * Register on operators.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(
			T_LOGICAL_OR,
			T_LOGICAL_XOR,
			T_BOOLEAN_AND,
			T_BOOLEAN_OR,
			T_INSTANCEOF,
			T_IS_EQUAL,
			T_IS_GREATER_OR_EQUAL,
			T_IS_IDENTICAL,
			T_IS_NOT_EQUAL,
			T_IS_NOT_IDENTICAL,
			T_IS_SMALLER_OR_EQUAL,
			T_OR_EQUAL,
			T_AND_EQUAL,
			T_PLUS_EQUAL,
			T_SL_EQUAL,
			T_SR_EQUAL,
			T_XOR_EQUAL,
			T_DIV_EQUAL,
			T_MINUS_EQUAL,
			T_MOD_EQUAL,
			T_MUL_EQUAL,
			T_SL, T_SR,
			T_DEC, T_INC,
			T_PLUS,
			T_MINUS,
			T_MULTIPLY,
			T_DIVIDE,
			T_MODULUS
		);
	}
}
