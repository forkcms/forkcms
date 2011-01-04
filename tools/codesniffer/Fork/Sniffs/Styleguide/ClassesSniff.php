m<?php

/**
 * Fork_Sniffs_Styleguide_ClassesSniff
 * Checks if classes are meeting the styleguide
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
class Fork_Sniffs_Styleguide_ClassesSniff implements PHP_CodeSniffer_Sniff
{
	public function register()
	{
		return array(T_CLASS, T_EXTENDS, T_IMPLEMENTS, T_INTERFACE, T_NAMESPACE, T_NS_SEPARATOR, T_CLONE);
	}


	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		// get the tokens
		$tokens = $phpcsFile->getTokens();
		$current = $tokens[$stackPtr];

		// get all lines
		$lines = file($phpcsFile->getFilename());

		// get previous token
		$previous = $tokens[$stackPtr - 1];
		// get nex token
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
					if($lines[$tokens[$current['scope_closer']]['line']] == "\n" && $lines[$tokens[$current['scope_closer']]['line'] + 1] == "\n" && trim($lines[$tokens[$current['scope_closer']]['line'] + 2]) != '') {}
					else $phpcsFile->addWarning('Expected 2 empty lines after a class.', $stackPtr);
				}

				if($next['code'] != T_WHITESPACE) $phpcsFile->addWarning('Space expected after class, interface', $stackPtr);

				// find comment
				if($phpcsFile->findPrevious(T_DOC_COMMENT, $stackPtr, $stackPtr - 4) === false) $phpcsFile->addWarning('PHPDoc expected before class', $stackPtr);

				// get classname
				$className = trim(str_replace('class', '', $lines[$current['line'] - 1]));

				// exceptions may have brackets on the same line
				if(substr_count($className, 'Exception') == 0)
				{
					if(($tokens[$current['scope_opener']]['line'] - $current['line']) != 1) $phpcsFile->addWarning('Opening bracket should be at the next line', $stackPtr);
					if($tokens[$current['scope_opener']]['column'] != $tokens[$current['scope_closer']]['column']) $phpcsFile->addWarning('Closing brace should be at same column as opening brace.', $stackPtr);
					if($tokens[$current['scope_opener'] + 1]['content'] != "\n") $phpcsFile->addWarning('Content should be on a new line.', $stackPtr);
					if($tokens[$current['scope_opener'] + 1]['column'] != $tokens[$current['scope_closer']]['column'] + 1) $phpcsFile->addWarning('Content should be indented correctly', $stackPtr);
				}

				// is it a fork class?
				if(substr_count($className, 'Frontend') > 0 || substr_count($className, 'Backend') > 0 || substr_count($className, 'Api') > 0 || substr_count($className, 'Installer') > 0)
				{
					$folder = substr($phpcsFile->getFilename(), strpos($phpcsFile->getFilename(), '/default_www') + 1);
					$chunks = explode('/', $folder);
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
					$startComment = $phpcsFile->findPrevious(T_DOC_COMMENT, $stackPtr, null, null, '/**'."\n");
					$endComment = $phpcsFile->findPrevious(T_DOC_COMMENT, $stackPtr, null, null, ' */');

					$hasPackage = false;
					$hasSubPackage = false;
					$hasAuthor = false;
					$hasSince = false;

					for($i = $startComment; $i<= $endComment; $i++)
					{
						// package
						if(substr($tokens[$i]['content'], 0, 11) == ' * @package')
						{
							// reset
							$hasPackage = true;

							// find part
							$content = trim(substr($tokens[$i]['content'], strrpos($tokens[$i]['content'], "\t")));

							// validate content
							if($content != $correctPackage) $phpcsFile->addError('Invalid value for @package', $i);

							// validate syntax
							if(substr($tokens[$i]['content'], 11, 1) != "\t") $phpcsFile->addError('After @package there should be at least one tab', $i);
						}

						// subpackage
						if(substr($tokens[$i]['content'], 0, 14) == ' * @subpackage')
						{
							// reset
							$hasSubPackage = true;

							// find part
							$content = trim(substr($tokens[$i]['content'], strrpos($tokens[$i]['content'], "\t")));

							// validate content
							if(substr_count($correctSubPackage, '.') == 0 && $content != $correctSubPackage) $phpcsFile->addError('Invalid value for @subpackage', $i);

							// validate syntax
							if(substr($tokens[$i]['content'], 14, 1) != "\t") $phpcsFile->addError('After @subpackage there should be at least one tab', $i);
						}

						// author
						if(substr($tokens[$i]['content'], 0, 10) == ' * @author')
						{
							// reset
							$hasAuthor = true;

							// validate syntax
							if(substr($tokens[$i]['content'], 10, 1) != "\t") $phpcsFile->addError('After @author there should be at least one tab', $i);

							// find part
							$content = trim(substr($tokens[$i]['content'], strrpos($tokens[$i]['content'], "\t")));

							// validate
							if(preg_match('/.*<.*@.*>$/', $content) != 1) $phpcsFile->addError('Invalid syntax for the @author-value', $i);
						}

						// since
						if(substr($tokens[$i]['content'], 0, 9) == ' * @since')
						{
							// reset
							$hasSince = true;

							// validate syntax
							if(substr($tokens[$i]['content'], 9, 1) != "\t") $phpcsFile->addError('After @since there should be at least one tab', $i);

							// find part
							$content = trim(substr($tokens[$i]['content'], strrpos($tokens[$i]['content'], "\t")));

							// validate
							if(preg_match('/^[0-9\.]*$/', $content) != 1) $phpcsFile->addError('Invalid syntax for the @since-value', $i);
						}
					}

					if(!$hasPackage) $phpcsFile->addError('No package found in PHPDoc', $startComment);
					if(!$hasSubPackage) $phpcsFile->addError('No subpackage found in PHPDoc', $startComment);
					if(!$hasAuthor) $phpcsFile->addError('No author found in PHPDoc', $startComment);
					if(!$hasSince) $phpcsFile->addError('No since found in PHPDoc', $startComment);
				}
			break;

			case T_EXTENDS:
			case T_IMPLEMENTS:
				if($previous['content'] != ' ') $phpcsFile->addWarning('Space excpected before extends, implements', $stackPtr);
				if($next['content'] != ' ') $phpcsFile->addWarning('Space expected after extends, implements', $stackPtr);
			break;

			case T_CLONE:
			case T_NAMESPACE:
			case T_NS_SEPARATOR:
				// @later we don't use these elements at the moment
			break;
		}

		// cleanup
		unset($tokens);
		unset($current);
		unset($lines);
		unset($next);
		unset($previous);

	}
}

?>