<?php
/**
 * Fork_Sniffs_PHP_ForbiddenNamesSniff.
 *
 * PHP version 5.3
 *
 * @category  PHP
 * @package   Fork
 * @author    Wim Godden <wim.godden@cu.be>
 * @copyright 2010 Cu.be Solutions bvba
 */

/**
 * Fork_Sniffs_PHP_ForbiddenNamesSniff.
 *
 * Prohibits the use of reserved keywords as class, function, namespace or constant names
 *
 * PHP version 5.3
 *
 * @category  PHP
 * @package   Fork
 * @author    Wim Godden <wim.godden@cu.be>
 * @copyright 2010 Cu.be Solutions bvba
 */
class Fork_Sniffs_PHP_ForbiddenNamesSniff implements PHP_CodeSniffer_Sniff
{
	/**
	 * If true, an error will be thrown; otherwise a warning.
	 *
	 * @var bool
	 */
	protected $error = true;

	/**
	 * A list of keywords that can not be used as function, class and namespace name or constant name
	 * Mentions since which version it's not allowed
	 *
	 * @var array(string => string)
	 */
	protected $invalidNames = array(
		'abstract' => '5.0',
		'and' => 'all',
		'array' => 'all',
		'as' => 'all',
		'break' => 'all',
		'case' => 'all',
		'catch' => '5.0',
		'class' => 'all',
		'clone' => '5.0',
		'const' => 'all',
		'continue' => 'all',
		'declare' => 'all',
		'default' => 'all',
		'do' => 'all',
		'else' => 'all',
		'elseif' => 'all',
		'enddeclare' => 'all',
		'endfor' => 'all',
		'endforeach' => 'all',
		'endif' => 'all',
		'endswith' => 'all',
		'endwhile' => 'all',
		'extends' => 'all',
		'final' => '5.0',
		'for' => 'all',
		'foreach' => 'all',
		'function' => 'all',
		'global' => 'all',
		'goto' => '5.3',
		'if' => 'all',
		'implements' => '5.0',
		'interface' => '5.0',
		'instanceof' => '5.0',
		'namespace' => '5.3',
		'new' => 'all',
		'or' => 'all',
		'private' => '5.0',
		'protected' => '5.0',
		'public' => '5.0',
		'static' => 'all',
		'switch' => 'all',
		'throw' => '5.0',
		'try' => '5.0',
		'use' => 'all',
		'var' => 'all',
		'while' => 'all',
		'xor' => 'all',
		'__CLASS__' => 'all',
		'__DIR__' => '5.3',
		'__FILE__' => 'all',
		'__FUNCTION__' => 'all',
		'__METHOD__' => 'all',
		'__NAMESPACE__' => '5.3'
	);

	/**
	 * Processes this test, when one of its tokens is encountered.
	 * @param PHP_CodeSniffer_File $phpcsFile
	 * @param int $stackPtr
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		$tokens = $phpcsFile->getTokens();
		$stackPtr = $phpcsFile->findNext(array(T_CLASS, T_FUNCTION, T_NAMESPACE, T_STRING), $stackPtr);

		/*
		 * We distinguish between the class, function and namespace names or the define statements
		 */
		if($tokens[$stackPtr]['type'] == 'T_STRING')
		{
			$this->processString($phpcsFile, $stackPtr, $tokens);
		}

		else $this->processNonString($phpcsFile, $stackPtr, $tokens);
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile
	 * @param int $stackPtr
	 * @param array $tokens
	 */
	public function processNonString(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $tokens)
	{
		if	(in_array(strtolower($tokens[$stackPtr + 2]['content']), array_keys($this->invalidNames)) === false)
		{
			return;
		}

		$error = "Function name, class name, namespace name or constant name can not be reserved keyword '" . $tokens[$stackPtr + 2]['content'] . "' (since version " . $this->invalidNames[strtolower($tokens[$stackPtr + 2]['content'])] . ")";
		$phpcsFile->addError($error, $stackPtr);
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile
	 * @param int $stackPtr
	 * @param array $tokens
	 */
	public function processString(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $tokens)
	{
		$stackPtr = $phpcsFile->findNext(T_STRING, $stackPtr, null, null, 'define');
		if($stackPtr === false)
		{
			return;
		}

		$closingParenthesis = $phpcsFile->findNext(T_CLOSE_PARENTHESIS, $stackPtr);
		if($closingParenthesis === false)
		{
			return;
		}

		$defineContent = $phpcsFile->findNext(T_CONSTANT_ENCAPSED_STRING, $stackPtr, $closingParenthesis);
		if($defineContent === false)
		{
			return;
		}

		foreach($this->invalidNames as $key => $value)
		{
			if(substr(strtolower($tokens[$defineContent]['content']), 1, strlen($tokens[$defineContent]['content']) - 2) == $key)
			{
				$error = "Function name, class name, namespace name or constant name can not be reserved keyword '" . $key . "' (since version " . $value . ")";
				$phpcsFile->addError($error, $stackPtr);
			}
		}
	}

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(T_CLASS, T_FUNCTION, T_NAMESPACE, T_STRING, T_CONST);
	}
}
