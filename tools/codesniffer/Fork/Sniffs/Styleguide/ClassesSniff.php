<?php

/**
 * Checks if classes are meeting the styleguide
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
class Fork_Sniffs_Styleguide_ClassesSniff implements PHP_CodeSniffer_Sniff
{
	/**
	 * @var array
	 */
	protected static $classesWithErrors = array();

	/**
	 * @var array
	 */
	protected static $functions = array();

	/**
	 * Process the code
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile
	 * @param int $stackPtr
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		// get the tokens
		$tokens = $phpcsFile->getTokens();
		$current = $tokens[$stackPtr];
		$lines = file($phpcsFile->getFilename());
		$previous = $tokens[$stackPtr - 1];
		$next = $tokens[$stackPtr + 1];

		// handle all types
		switch($current['code'])
		{
			case T_CLASS:
			case T_INTERFACE:
				$nextClass = false;
				if(isset($current['scope_closer'])) $nextClass = $phpcsFile->findNext(T_CLASS, $current['scope_closer']);

				// multiple classes in one file
				if($nextClass !== false)
				{
					if(!($lines[$tokens[$current['scope_closer']]['line']] == "\n" && trim($lines[$tokens[$current['scope_closer']]['line'] + 1]) != ''))
					{
						$phpcsFile->addError('Expected 1 blank line after a class.', $stackPtr);
					}
				}

				if($next['code'] != T_WHITESPACE)
				{
					$phpcsFile->addError('Space expected after class, interface', $stackPtr);
				}

				// find comment
				if($phpcsFile->findPrevious(T_DOC_COMMENT, $stackPtr, $stackPtr - 4) === false) $phpcsFile->addError('PHPDoc expected before class', $stackPtr);

				// get classname
				$className = trim(str_replace('class', '', $lines[$current['line'] - 1]));

				// exceptions may have brackets on the same line
				if(substr_count($className, 'Exception') == 0)
				{
					if(($tokens[$current['scope_opener']]['line'] - $current['line']) != 1) $phpcsFile->addError('Opening bracket should be at the next line', $stackPtr);
					if($tokens[$current['scope_opener']]['column'] != $tokens[$current['scope_closer']]['column']) $phpcsFile->addError('Closing brace should be at same column as opening brace.', $stackPtr);
					if($tokens[$current['scope_opener'] + 1]['content'] != "\n") $phpcsFile->addError('Content should be on a new line.', $stackPtr);
					if($tokens[$current['scope_opener'] + 1]['column'] != $tokens[$current['scope_closer']]['column'] + 1) $phpcsFile->addError('Content should be indented correctly', $stackPtr);
				}

				// is it a fork class?
				if(substr_count($className, 'Frontend') > 0 || substr_count($className, 'Backend') > 0 || substr_count($className, 'Api') > 0 || substr_count($className, 'Installer') > 0)
				{
					$folder = substr($phpcsFile->getFilename(), strpos($phpcsFile->getFilename(), DIRECTORY_SEPARATOR . 'default_www') + 1);
					$chunks = explode(DIRECTORY_SEPARATOR, $folder);
					$correctPackage = $chunks[1];
					$correctSubPackage = $chunks[2];

					if($correctSubPackage == 'modules') $correctSubPackage = $chunks[3];
					if(isset($chunks[4]) && $chunks[4] == 'installer') $correctPackage = 'installer';
					if($chunks[1] == 'install')
					{
						$correctPackage = 'install';
						$correctSubPackage = 'installer';
					}

					// get comment
					$startComment = (int) $phpcsFile->findPrevious(T_DOC_COMMENT, $stackPtr, null, null, '/**'."\n");
					$endComment = (int) $phpcsFile->findPrevious(T_DOC_COMMENT, $stackPtr, null, null, ' */');

					$hasPackage = false;
					$hasSubPackage = false;
					$hasAuthor = false;
					$hasSince = false;

					for($i = $startComment; $i <= $endComment; $i++)
					{
						// author
						if(substr($tokens[$i]['content'], 0, 10) == ' * @author')
						{
							// reset
							$hasAuthor = true;

							// validate syntax
							if(substr($tokens[$i]['content'], 10, 1) != ' ')
							{
								$phpcsFile->addError('After @author there should be exactly one space.', $i);
							}

							// validate author format
							$content = trim(substr($tokens[$i]['content'], strrpos($tokens[$i]['content'], ' ')));
							if(preg_match('/.*<.*@.*>$/', $content) != 1)
							{
								$phpcsFile->addError('Invalid syntax for the @author-value', $i);
							}
						}
					}

					// no author found
					if(!$hasAuthor)
					{
						$phpcsFile->addError('No author found in PHPDoc', $startComment);
					}
				}
				break;

			case T_EXTENDS:
			case T_IMPLEMENTS:
				if($previous['content'] != ' ')
				{
					$phpcsFile->addError('Space excpected before extends, implements', $stackPtr);
				}

				if($next['content'] != ' ')
				{
					$phpcsFile->addError('Space expected after extends, implements', $stackPtr);
				}
				break;

			case T_FUNCTION:
				$prevClass = $phpcsFile->findPrevious(T_CLASS, $stackPtr);

				if(isset($tokens[$prevClass]['scope_closer']) && $current['line'] <= $tokens[$tokens[$prevClass]['scope_closer']]['line'])
				{
					if($prevClass != 0)
					{
						// get class
						$className = strtolower($tokens[$prevClass + 2]['content']);
						if(!in_array($className, self::$classesWithErrors))
						{
							// add to list of functions
							self::$functions[$className][] = strtolower($tokens[$stackPtr + 2]['content']);

							// create local copy
							$local = self::$functions[$className];
							sort($local);

							// check difference
							$diff = (array) array_diff_assoc($local, self::$functions[$className]);
							if(count($diff) > 0)
							{
								$phpcsFile->addError('The methods should be placed in alphabetical order.', $stackPtr);
								self::$classesWithErrors[] = $className;
							}
						}
					}
				}
				break;

			case T_CLONE:
			case T_NAMESPACE:
			case T_NS_SEPARATOR:
				// @later we don't use these elements at the moment
				break;
		}

		unset($tokens);
		unset($current);
		unset($lines);
		unset($next);
		unset($previous);

	}

	/**
	 * Register on class related tokens.
	 */
	public function register()
	{
		return array(T_CLASS, T_EXTENDS, T_IMPLEMENTS, T_INTERFACE, T_NAMESPACE, T_NS_SEPARATOR, T_CLONE, T_FUNCTION);
	}
}
