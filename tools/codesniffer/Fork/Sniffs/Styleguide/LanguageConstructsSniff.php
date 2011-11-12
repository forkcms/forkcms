<?php

/**
 * Checks if all language constructs are used like described in the styleguide
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Fork_Sniffs_Styleguide_LanguageConstructsSniff implements PHP_CodeSniffer_Sniff
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
		$next = $tokens[$stackPtr + 1];

		// handle all types
		switch($current['code'])
		{
			// echo and print should have a whitespace after the keyword and no parenthesis
			case T_ECHO:
			case T_PRINT:
				if($next['content'] != ' ')
				{
					$phpcsFile->addError('After "echo", "print" we expect exactly one space.', $stackPtr);
				}
				break;

			// eval is evil, just throw a warning when it is used, it should be avoided at all times.
			case T_EVAL:
				$phpcsFile->addWarning('Are you really sure you need eval?', $stackPtr);
				break;

			/*
			 * exit and die, should have a semicolon or a parenthesis after the keyword, when a
			 * parenthesis is used no spaces should appear after the opening parenthesis nor before
			 * the closing one.
			 */
			case T_EXIT:
				if($next['code'] != T_SEMICOLON && $next['code'] != T_OPEN_PARENTHESIS)
				{
					$phpcsFile->addError('semicolon or opening parenthesis is missing', $stackPtr);
				}
				if($next['code'] == T_OPEN_PARENTHESIS)
				{
					// opening brace whitespaces
					if($tokens[$stackPtr + 2]['code'] == T_WHITESPACE && substr($tokens[$stackPtr + 2]['content'], 0, 1) != "\n")
					{
						$phpcsFile->addError('We don\'t allow whitespaces after the opening brace', $stackPtr);
					}

					// no whitespaces before )
					if(!isset($next['parenthesis_closer']) || ($tokens[$next['parenthesis_closer'] - 1]['code'] == T_WHITESPACE && substr($tokens[$stackPtr + 2]['content'], 0, 1) != "\n"))
					{
						$phpcsFile->addError('We don\'t allow whitespaces before the closing brace.', $stackPtr);
					}
				}
				break;

			// include* and require* should not use parenthesis
			case T_INCLUDE:
			case T_INCLUDE_ONCE:
			case T_REQUIRE:
			case T_REQUIRE_ONCE:
				if($next['code'] != T_WHITESPACE || $next['content'] != ' ')
				{
					$phpcsFile->addError('After "include", "include_once", "require", "require_once" we expect exactly one space.', $stackPtr);
				}
				break;

			/*
			 * isset and unset need parenthesis according to styleguide, no spaces should appear
			 * after the opening parenthesis nor before the closing one.
			 */
			case T_ISSET:
			case T_UNSET:
				// isset opening brace
				if($next['code'] != T_OPEN_PARENTHESIS)
				{
					$phpcsFile->addError('After "isset" we expect an opening brace.', $stackPtr);
				}

				// opening brace whitespaces
				if($tokens[$stackPtr + 2]['code'] == T_WHITESPACE && substr($tokens[$stackPtr + 2]['content'], 0, 1) != "\n")
				{
					$phpcsFile->addError('We don\'t allow whitespaces after the opening brace', $stackPtr);
				}

				// no whitespaces before )
				if(!isset($next['parenthesis_closer']) || ($tokens[$next['parenthesis_closer'] - 1]['code'] == T_WHITESPACE && substr($tokens[$stackPtr + 2]['content'], 0, 1) != "\n"))
				{
					$phpcsFile->addError('We don\'t allow whitespaces before the closing brace.', $stackPtr);
				}
				break;

			/*
			 * list needs parenthesis according to styleguide, no spaces should appear after the
			 * opening parenthesis nor before the closing one.
			 */
			case T_LIST:
				// list opening brace
				if($next['code'] != T_OPEN_PARENTHESIS)
				{
					$phpcsFile->addError('Expecting ( after list', $stackPtr);
				}

				// opening brace whitespaces
				if($tokens[$stackPtr + 2]['code'] == T_WHITESPACE && substr($tokens[$stackPtr + 2]['content'], 0, 1) != "\n")
				{
					$phpcsFile->addError('We don\'t allow whitespaces after the opening brace', $stackPtr);
				}

				// no whitespaces before )
				if(!isset($next['parenthesis_closer']) || ($tokens[$next['parenthesis_closer'] - 1]['code'] == T_WHITESPACE && substr($tokens[$stackPtr + 2]['content'], 0, 1) != "\n"))
				{
					$phpcsFile->addError('We don\'t allow whitespaces before the closing brace.', $stackPtr);
				}
				break;

			// return should not use parenthesis.
			case T_RETURN:
				if($next['code'] != T_WHITESPACE && $next['code'] != T_SEMICOLON)
				{
					$phpcsFile->addError('After return we expect exactly one space or a semicolon.', $stackPtr);
				}
				break;
		}

		// cleanup
		unset($tokens);
		unset($current);
		unset($next);
	}

	/**
	 * Registers on all language constructs.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(
			T_EXIT,
			T_ECHO,
			T_PRINT,
			T_EVAL,
			T_INCLUDE,
			T_INCLUDE_ONCE,
			T_REQUIRE,
			T_REQUIRE_ONCE,
			T_ISSET,
			T_LIST,
			T_RETURN,
			T_UNSET
		);
	}
}
