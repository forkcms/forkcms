<?php

/**
 * Check if functions/methods meet the standards
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class Fork_Sniffs_Styleguide_FunctionsSniff implements PHP_CodeSniffer_Sniff
{
	/**
	 * Process the code
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile
	 * @param unknown_type $stackPtr
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		$tokens = $phpcsFile->getTokens();
		$current = $tokens[$stackPtr];
		$lines = file($phpcsFile->getFilename());
		$next = $tokens[$stackPtr + 1];

		// function whitespaces
		if($next['content'] != ' ')
		{
			$phpcsFile->addError('After "function" we expect exactly one space.', $stackPtr);
		}

		if(isset($current['scope_opener']))
		{
			// check if { is on next line
			if($tokens[$current['scope_opener']]['line'] - $current['line'] != 1)
			{
				$phpcsFile->addError(
					'The opening brace of a function/method should be placed on the line below the signature.',
					$stackPtr
				);
			}

			// braces indentation
			if($tokens[$current['scope_opener']]['column'] != $tokens[$current['scope_closer']]['column'])
			{
				$phpcsFile->addError('The opening and closing brace should be indentend equally.', $stackPtr);
			}

			// find class
			$class = $phpcsFile->findPrevious(T_CLASS, $stackPtr);

			// class found?
			if($class != false)
			{
				// get next function inside the class
				$nextFunction = $phpcsFile->findNext(T_FUNCTION, $stackPtr + 1, $tokens[$class]['scope_closer']);

				// not last method?
				if($nextFunction != false && isset($tokens[$stackPtr - 2]) && in_array($tokens[$stackPtr - 2]['code'], array(T_PRIVATE, T_PUBLIC, T_PROTECTED, T_STATIC)))
				{
					if(!($lines[$tokens[$current['scope_closer']]['line']] == "\n" && trim($lines[$tokens[$current['scope_closer']]['line'] + 1]) != ''))
					{
						$phpcsFile->addError('After a function/method we expect 1 blank line.', $stackPtr);
					}
				}
			}
		}

		// find comment
		if($phpcsFile->findPrevious(T_DOC_COMMENT, $stackPtr, $stackPtr, $stackPtr - 7) === false)
		{
			$phpcsFile->addError('We expect a function/method to have PHPDoc-documentation.', $stackPtr);
		}

		if(isset($tokens[$stackPtr - 2]) && in_array($tokens[$stackPtr - 2]['code'], array(T_PRIVATE, T_PUBLIC, T_PROTECTED, T_STATIC)))
		{
			// get comment
			$startComment = (int) $phpcsFile->findPrevious(T_DOC_COMMENT, $stackPtr, null, null, '/**'."\n");
			$endComment = (int) $phpcsFile->findPrevious(T_DOC_COMMENT, $stackPtr, null, null, '	 */');

			$parameters = $phpcsFile->getMethodParameters($stackPtr);

			$hasReturn = false;
			$paramCounter = 0;

			for($i = $startComment; $i <= $endComment; $i++)
			{
				if(trim(substr($tokens[$i]['content'], 0, 11)) == '* @return')
				{
					$hasReturn = true;

					// find part
					$content = trim(substr($tokens[$i]['content'], strrpos($tokens[$i]['content'], ' ')));

					// validate syntax
					if(substr($tokens[$i]['content'], 11, 1) != ' ')
					{
						$phpcsFile->addError('After "@return" there should be exactly one space.', $i);
					}

					// @return should be last
					if($paramCounter == 0 && count($parameters) != 0)
					{
						$phpcsFile->addError('"@return" needs to be placed after the last "@param"', $i);
					}
				}

				if(trim(substr($tokens[$i]['content'], 0, 10)) == '* @param')
				{
					// find part
					$content =  trim(substr($tokens[$i]['content'], strpos($tokens[$i]['content'], '* ') + 2));
					$pieces = explode(' ', trim(str_replace('@param', '', $content)), 4);

					$type = $pieces[0];
					$variable = $pieces[1];
					$description = isset($pieces[2]) ? $pieces[2] : null;

					// validate type
					if($type != trim($type))
					{
						$phpcsFile->addError('Something wrong with the type in the PHPDoc. We expect @param[space]<type>...', $i);
					}

					// validate variable
					if($variable !== trim($variable))
					{
						$phpcsFile->addError('Something wrong with the variable in the PHPDoc. We expect @param[space]<type>[space]<variable>...', $i);
					}

					/*
					 * @todo add validation:
					 * - description ends with a dot (if filled in)
					 * - compare parameters to method
					 * - optional parameters should be like that in the method and phpdoc
					 */

					// check if it match
					/*if(!preg_match('/(.*)\s\$(.*)(\t(.*))?$/', $content)) $phpcsFile->addError('Wrong sequence, we expect "@param" in the following way: @param[tab]<type>[space]<varname>([tab]<documentation>).', $i);

					if(!isset($parameters[$paramCounter]['name']) || $parameters[$paramCounter]['name'] != $varName)
					{
						$phpcsFile->addError('We expect the variablename used in the PHPDoc to be the same as the one used in the parameterlist.', $i);
					}

					if(isset($parameters[$paramCounter]['default']) && $parameters[$paramCounter]['default'] != '')
					{
						if(substr_count($type, '[optional]') == 0) $phpcsFile->addError('We expect optional parameters to have "[optional]" just after the type.', $i);
						if($parameters[$paramCounter]['type_hint'] != '' && $type != $parameters[$paramCounter]['type_hint'] .'[optional]') $phpcsFile->addError('The type in the PHPDoc doesn\'t match the typehinting in the signature.', $i);
					}
					elseif(isset($parameters[$paramCounter]['type_hint']) && $parameters[$paramCounter]['type_hint'] != '' && $type != $parameters[$paramCounter]['type_hint']) $phpcsFile->addError('The type in the PHPDoc doesn\'t match the typehinting in the signature.', $i);
					*/

					// increment
					$paramCounter++;
				}
			}

			// incorrect number of parameters
			if($paramCounter != count($parameters))
			{
				$phpcsFile->addError('We expect all parameters to be in the PHPDoc.', $stackPtr);
			}
		}

		unset($tokens);
		unset($current);
		unset($lines);
		unset($next);
	}


	/**
	 * Register on functions.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(T_FUNCTION);
	}
}
