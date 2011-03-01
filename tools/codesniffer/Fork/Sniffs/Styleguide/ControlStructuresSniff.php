<?php

/**
 * Fork_Sniffs_Styleguide_ControlStructuresSniff
 * Check if all controlstructures are used like described in the styleguide
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
class Fork_Sniffs_Styleguide_ControlStructuresSniff implements PHP_CodeSniffer_Sniff
{
	public function register()
	{
		// register on all constrolstructures: if, else, elseif endif, switch, case, default, do, while, endwhile, for, endfor, foreach, endforeach, break, continue, declare, enddeclare, goto, try, catch, throw and use
		return array(T_IF, T_ELSE, T_ELSEIF, T_ENDIF, T_SWITCH, T_ENDSWITCH, T_CASE, T_DEFAULT, T_DO, T_WHILE, T_ENDWHILE, T_FOR, T_ENDFOR, T_FOREACH, T_ENDFOREACH, T_BREAK, T_CONTINUE, T_DECLARE, T_ENDDECLARE, T_GOTO, T_TRY, T_CATCH, T_THROW, T_USE);
	}


	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		// get the tokens
		$tokens = $phpcsFile->getTokens();
		$current = $tokens[$stackPtr];
		$next = $tokens[$stackPtr + 1];

		// handle all types
		switch($current['code'])
		{
			// break and continue statements should be followed by a semicolon or exactly one space
			case T_BREAK:
			case T_CONTINUE:
				if($next['code'] != T_SEMICOLON && $next['content'] != ' ') $phpcsFile->addError('After "break", "continue" we expect a semicolon or exactly one space.', $stackPtr);
			break;

			// a case statement should be folowed by a whitespace and the condition, the content should be indented inside the body and the break-statement should be at the same column as the case-statement
			case T_CASE:
				if($next['code'] != T_WHITESPACE || $next['content'] != ' ') $phpcsFile->addError('After "case" we expect exactly one space.', $stackPtr);
				if($tokens[$current['scope_opener'] + 1]['content'] != "\n") $phpcsFile->addError('After the colon we expect a newline.', $stackPtr);
				if($tokens[$current['scope_closer']]['code'] == T_BREAK && $tokens[$current['scope_closer']]['column'] != $current['column']) $phpcsFile->addError('"break" and "case" should be indented equaly.', $stackPtr);
				if($tokens[$current['scope_opener'] + 1]['content'] != "\n") $phpcsFile->addError('We expect the code to be on a new line.', $stackPtr);
			break;

			// a catch-statement should be followed by an opening parenthesis, inside the parenthesis there shouldn't be spaces. the opening and closing brackets should be at the same column of the catch statement and the body should be indented
			case T_CATCH:
				if($next['code'] != T_OPEN_PARENTHESIS) $phpcsFile->addError('Expecting ( after catch', $stackPtr);
				if($tokens[$stackPtr + 2]['code'] == T_WHITESPACE) $phpcsFile->addError('We don\'t allow whitespaces after the opening brace', $stackPtr);
				if(!isset($next['parenthesis_closer']) || $tokens[$next['parenthesis_closer'] - 1]['code'] == T_WHITESPACE) $phpcsFile->addError('We don\'t allow whitespaces before the closing brace.', $stackPtr);

				if(($tokens[$current['scope_opener']]['line'] - $current['line']) != 1) $phpcsFile->addError('The opening brace of a "catch" should be placed on the line below "catch".', $stackPtr);
				if($current['column'] != $tokens[$current['scope_opener']]['column']) $phpcsFile->addError('The opening brace should be indented equaly as "catch".', $stackPtr);
				if($current['column'] != $tokens[$current['scope_closer']]['column']) $phpcsFile->addError('The closing brace should be indented equaly as "catch".', $stackPtr);
				if($tokens[$current['scope_opener'] + 1]['content'] != "\n") $phpcsFile->addError('We expect the code to be on a new line.', $stackPtr);
				if($tokens[$current['scope_opener'] + 1]['column'] != $tokens[$current['scope_closer']]['column'] + 1) $phpcsFile->addError('The content should be indented inside the "catch"-block.', $stackPtr);
			break;

			// a default-statement should be followed directly by a colon
			case T_DEFAULT:
				if($next['code'] != T_COLON) $phpcsFile->addError('After "default" we expect a colon.', $stackPtr);
			break;

			case T_DO:
				if($next['code'] != T_WHITESPACE || $next['content'] != "\n") $phpcsFile->addError('After "do" we exepect a newline', $stackPtr);
				if(($tokens[$current['scope_opener']]['line'] - $current['line']) != 1) $phpcsFile->addError('The opening brace of a "do" should be placed on the line below "do".', $stackPtr);
				if($current['column'] != $tokens[$current['scope_opener']]['column']) $phpcsFile->addError('The opening brace should be indented equaly as "do".', $stackPtr);
				if($current['column'] != $tokens[$current['scope_closer']]['column']) $phpcsFile->addError('The closing brace should be indented equaly as "do".', $stackPtr);
				if($tokens[$current['scope_opener'] + 1]['content'] != "\n") $phpcsFile->addError('We expect the code to be on a new line.', $stackPtr);
				if($tokens[$current['scope_opener'] + 1]['column'] != $tokens[$current['scope_closer']]['column'] + 1) $phpcsFile->addError('The content should be indented inside the "do"-block.', $stackPtr);
			break;

			case T_ELSE:
				// no short syntax
				if(isset($current['scope_opener']))
				{
					if(($tokens[$current['scope_opener']]['line'] - $current['line']) != 1) $phpcsFile->addError('The opening brace of a "else" should be placed on the line below "else".', $stackPtr);
					if($current['column'] != $tokens[$current['scope_opener']]['column']) $phpcsFile->addError('The opening brace should be indented equaly as "else".', $stackPtr);
					if($current['column'] != $tokens[$current['scope_closer']]['column']) $phpcsFile->addError('The closing brace should be indented equaly as "else".', $stackPtr);
					if($tokens[$current['scope_opener'] + 1]['content'] != "\n") $phpcsFile->addError('We expect the code to be on a new line.', $stackPtr);
					if($tokens[$current['scope_opener'] + 1]['column'] != $tokens[$current['scope_closer']]['column'] + 1) $phpcsFile->addError('The content should be indented inside the "else"-block.', $stackPtr);
				}
				else
				{
					if($next['content'] != ' ') $phpcsFile->addError('After "else" (in shortsyntax) we expect exactly one space', $stackPtr);
				}
			break;

			// if, elseif and switch-statements should be followed directly by an opening parenthesis. Also the opening and closing brackets should live on the same column and the body has to be indented
			case T_ELSEIF:
			case T_IF:
			case T_SWITCH:
				if($next['code'] != T_OPEN_PARENTHESIS) $phpcsFile->addError('After "if", "elseif", "switch" we expect an opening brace.', $stackPtr);
				if($tokens[$stackPtr + 2]['code'] == T_WHITESPACE) $phpcsFile->addError('We don\'t allow whitespaces after the opening brace', $stackPtr);
				if(!isset($next['parenthesis_closer']) || $tokens[$next['parenthesis_closer'] - 1]['code'] == T_WHITESPACE) $phpcsFile->addError('We don\'t allow whitespaces before the closing brace.', $stackPtr);

				// no short syntax
				if(isset($current['scope_opener']))
				{
					if(($tokens[$current['scope_opener']]['line'] - $tokens[$current['parenthesis_closer']]['line']) != 1) $phpcsFile->addError('The opening brace of a "if", "elseif", "switch" should be placed on the line below "if", "elseif", "switch".', $stackPtr);
					if($current['column'] != $tokens[$current['scope_opener']]['column']) $phpcsFile->addError('The opening brace should be indented equaly as "if", "elseif", "switch".', $stackPtr);
					if($current['column'] != $tokens[$current['scope_closer']]['column']) $phpcsFile->addError('The closing brace should be indented equaly as "if", "elseif", "switch".', $stackPtr);
					if($tokens[$current['scope_opener'] + 1]['content'] != "\n") $phpcsFile->addError('We expect the code to be on a new line.', $stackPtr);
					if($tokens[$current['scope_opener'] + 1]['column'] != $tokens[$current['scope_closer']]['column'] + 1) $phpcsFile->addError('The content should be indented inside the "if", "elseif", "switch"-block.', $stackPtr);
				}
				elseif($tokens[$current['parenthesis_closer'] + 1]['content'] != ' ') $phpcsFile->addError('After the condition of an "if" (in shortsyntax) we expect exactly one space', $stackPtr);
			break;

			// we don't use these statements because they are confusings
			case T_ENDIF:
			case T_ENDSWITCH:
			case T_ENDFOR:
			case T_ENDFOREACH:
			case T_ENDWHILE:
			case T_ENDDECLARE:
				$phpcsFile->addWarning('We use culry braces instead of these end-words.', $stackPtr);
			break;

			// for, foreach, while should be followed directly by an opening parenthesis, inside the condition there shouldn't be spaces before the closing parenthesis nor after the opening one.
			case T_FOR:
			case T_FOREACH:
			case T_WHILE:
				if($next['code'] != T_OPEN_PARENTHESIS) $phpcsFile->addError('After "for", "foreach", "while" we expect an opening brace.', $stackPtr);
				if($tokens[$stackPtr + 2]['code'] == T_WHITESPACE) $phpcsFile->addError('We don\'t allow whitespaces after the opening brace', $stackPtr);
				if(!isset($next['parenthesis_closer']) || $tokens[$next['parenthesis_closer'] - 1]['code'] == T_WHITESPACE) $phpcsFile->addError('We don\'t allow whitespaces before the closing brace.', $stackPtr);

				// no short syntax
				if(isset($current['scope_opener']))
				{
					if(($tokens[$current['scope_opener']]['line'] - $current['line']) != 1) $phpcsFile->addError('The opening brace of a "for", "foreach", "while"  should be placed on the line below "for", "foreach", "while" .', $stackPtr);
					if($current['column'] != $tokens[$current['scope_opener']]['column']) $phpcsFile->addError('The opening brace should be indented equaly as "for", "foreach", "while" .', $stackPtr);
					if($current['column'] != $tokens[$current['scope_closer']]['column']) $phpcsFile->addError('The closing brace should be indented equaly as "for", "foreach", "while" .', $stackPtr);
					if($tokens[$current['scope_opener'] + 1]['content'] != "\n") $phpcsFile->addError('We expect the code to be on a new line.', $stackPtr);
					if($tokens[$current['scope_opener'] + 1]['column'] != $tokens[$current['scope_closer']]['column'] + 1) $phpcsFile->addError('The content should be indented inside the "for", "foreach", "while"-block.', $stackPtr);
				}
				elseif($tokens[$current['parenthesis_closer'] + 1]['content'] != ' ')
				{
					if($current['code'] == T_WHILE)
					{
						// search for a do-statement
						$var = $phpcsFile->findPrevious(T_DO, $stackPtr);

						// nothing found?
						if(!isset($tokens[$var])) $phpcsFile->addError('After the condition of an "for", "foreach", "while" (in shortsyntax) we expect exactly one space', $stackPtr);
					}

					else $phpcsFile->addError('After the condition of an "for", "foreach", "while" (in shortsyntax) we expect exactly one space', $stackPtr);
				}
			break;

			// throw statements are always followed by a space
			case T_THROW:
				if($next['code'] != T_WHITESPACE || $next['content'] != ' ') $phpcsFile->addError('After "throw" we expect exactly one space.', $stackPtr);
			break;

			// try-statements are followed by a newline, the opening and closing braces should be on the same level as the try-statement. The body has to be indented.
			case T_TRY:
				if(($tokens[$current['scope_opener']]['line'] - $current['line']) != 1) $phpcsFile->addError('The opening brace of a "catch" should be placed on the line below "try".', $stackPtr);
				if($current['column'] != $tokens[$current['scope_opener']]['column']) $phpcsFile->addError('The opening brace should be indented equaly as "try".', $stackPtr);
				if($current['column'] != $tokens[$current['scope_closer']]['column']) $phpcsFile->addError('The closing brace should be indented equaly as "try".', $stackPtr);
				if($tokens[$current['scope_opener'] + 1]['content'] != "\n") $phpcsFile->addError('We expect the code to be on a new line.', $stackPtr);
				if($tokens[$current['scope_opener'] + 1]['column'] != $tokens[$current['scope_closer']]['column'] + 1) $phpcsFile->addError('The content should be indented inside the "try"-block.', $stackPtr);
			break;
		}

		// cleanup
		unset($tokens);
		unset($current);
		unset($next);
	}
}

?>