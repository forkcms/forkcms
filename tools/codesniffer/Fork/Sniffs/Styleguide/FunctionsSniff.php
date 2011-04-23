<?php

/**
 * Fork_Sniffs_Styleguide_FunctionsSniff
 * Check if functions/methods meet the standards
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
class Fork_Sniffs_Styleguide_FunctionsSniff implements PHP_CodeSniffer_Sniff
{


	/**
	 * Process the code
	 *
	 * @return	void
	 * @param 	PHP_CodeSniffer_File $phpcsFile	The file.
	 * @param 	unknown_type $stackPtr			The stackpointer.
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		// @todo	no empty lines after { and before }

		// get the tokens
		$tokens = $phpcsFile->getTokens();
		$current = $tokens[$stackPtr];
		$lines = file($phpcsFile->getFilename());
		$next = $tokens[$stackPtr + 1];

		if($next['content'] != ' ') $phpcsFile->addError('After "function" we expect exactly one space.', $stackPtr);

		if(isset($current['scope_opener']))
		{
			// check if { is on next line
			if($tokens[$current['scope_opener']]['line'] - $current['line'] != 1) $phpcsFile->addError('The opening brace of a function/method should be placed on the line below the signature.', $stackPtr);
			if($tokens[$current['scope_opener']]['column'] != $tokens[$current['scope_closer']]['column']) $phpcsFile->addError('The opening and closing brace should be indentend equaly.', $stackPtr);

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
					if(!($lines[$tokens[$current['scope_closer']]['line']] == "\n" && $lines[$tokens[$current['scope_closer']]['line'] + 1] == "\n" && trim($lines[$tokens[$current['scope_closer']]['line'] + 2]) != ''))
					{
						$phpcsFile->addError('After a function/method we expect 2 empty lines.', $stackPtr);
					}
				}
			}
		}

		// find comment
		if($phpcsFile->findPrevious(T_DOC_COMMENT, $stackPtr, $stackPtr, $stackPtr - 7) === false) $phpcsFile->addError('We expect a function/method to have PHPDoc-documentation.', $stackPtr);

		if(isset($tokens[$stackPtr - 2]) && in_array($tokens[$stackPtr - 2]['code'], array(T_PRIVATE, T_PUBLIC, T_PROTECTED, T_STATIC)))
		{
			// get comment
			$startComment = (int) $phpcsFile->findPrevious(T_DOC_COMMENT, $stackPtr, null, null, '/**'."\n");
			$endComment = (int) $phpcsFile->findPrevious(T_DOC_COMMENT, $stackPtr, null, null, '	 */');

			$parameters = $phpcsFile->getMethodParameters($stackPtr);

			$hasReturn = false;
			$returnFirst = false;
			$paramCounter = 0;

			for($i = $startComment; $i <= $endComment; $i++)
			{
				// package
				if(trim(substr($tokens[$i]['content'], 0, 11)) == '* @return')
				{
					// reset
					$hasReturn = true;

					// find part
					$content = trim(substr($tokens[$i]['content'], strrpos($tokens[$i]['content'], "\t")));

					// validate syntax
					if(substr($tokens[$i]['content'], 11, 1) != "\t") $phpcsFile->addError('After "@return" there should be at least one tab.', $i);

					$returnFirst = true;
				}

				if(trim(substr($tokens[$i]['content'], 0, 10)) == '* @param')
				{
					if(!$returnFirst && $phpcsFile->getDeclarationName($stackPtr) !== '__construct') $phpcsFile->addError('We expect "@return" before "@param".', $i);

					// find part
					$content = trim(substr($tokens[$i]['content'], strpos($tokens[$i]['content'], "\t", 2)));

					$type = substr($content, 0, strpos($content, ' '));
					$varName = trim(substr($content, strpos($content, ' ') + 1));
					if(substr_count($varName, "\t") > 0)
					{
						if(substr($varName, -1, 1) != '.' && substr($varName, -1, 1) != '?') $phpcsFile->addWarning('Shouldn\'t the description have a "." on the end?', $i);
						$varName = trim(substr($varName, 0, strpos($varName, "\t")));
					}
					else $phpcsFile->addWarning('Shouldn\'t the parameter be documented?', $i);

					// check if it match
					if(!preg_match('/(.*)\s\$(.*)(\t(.*))?$/', $content)) $phpcsFile->addError('Wrong sequence, we expect "@param" in the following way: @param[tab]<type>[space]<varname>([tab]<documentation>).', $i);

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

					// increment
					$paramCounter++;
				}

				if(trim(substr($tokens[$i]['content'], 0, 15)) == '* @deprecated')
				{
					// find part
					$content = trim(substr($tokens[$i]['content'], strpos($tokens[$i]['content'], "\t", 2)));
					if($content != '')
					{
						if(substr($content, -1, 1) != '.' && substr($content, -1, 1) != '?') $phpcsFile->addWarning('Shouldn\'t the explanation have a "." on the end?', $i);
					}
					else $phpcsFile->addWarning('Shouldn\'t there be an explanation?', $i);
				}
			}

			// incorrect number of parameters
			if($paramCounter != count($parameters)) $phpcsFile->addError('We expect all parameters to be in the PHPDoc.', $stackPtr);
		}

		// cleanup
		unset($tokens);
		unset($current);
		unset($lines);
		unset($next);
	}


	/**
	 * Register
	 *
	 * @return	void
	 */
	public function register()
	{
		return array(T_FUNCTION);
	}
}

?>
