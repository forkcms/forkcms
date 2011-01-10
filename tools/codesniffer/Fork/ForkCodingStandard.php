<?php
if(class_exists('PHP_CodeSniffer_Standards_CodingStandard', true) === false) throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_CodingStandard not found');


/**
 * Fork Coding Standard.
 *
 * @author    Tijs Verkoyen <tijs@sumocoders.be>
 */
class PHP_CodeSniffer_Standards_Fork_ForkCodingStandard extends PHP_CodeSniffer_Standards_CodingStandard
{
	/**
	 * Return a list of external sniffs to include with this standard.
	 *
	 * The Fork standard uses some PEAR sniffs.
	 *
	 * @return array
	 */
	public function getIncludedSniffs()
	{
		return array('Generic/Sniffs/Classes/DuplicateClassNameSniff.php',
						'Generic/Sniffs/CodeAnalysis/ForLoopShouldBeWhileLoopSniff.php',
						'Generic/Sniffs/CodeAnalysis/JumbledIncrementerSniff.php',
						'Generic/Sniffs/CodeAnalysis/UnconditionalIfStatementSniff.php',
						'Generic/Sniffs/CodeAnalysis/UnnecessaryFinalModifierSniff.php',
						'Generic/Sniffs/CodeAnalysis/UnusedFunctionParameterSniff.php',
//						'Generic/Sniffs/CodeAnalysis/UselessOverridingMethodSniff.php',
						'Generic/Sniffs/Commenting/TodoSniff.php',
						'Generic/Sniffs/Formatting/SpaceAfterCastSniff.php',
						'Generic/Sniffs/Functions/OpeningFunctionBraceBsdAllmanSniff.php',
						'Generic/Sniffs/NamingConventions/ConstructorNameSniff.php',
						'Generic/Sniffs/NamingConventions/UpperCaseConstantNameSniff.php',
						'Generic/Sniffs/PHP/DisallowShortOpenTagSniff.php',
						'Generic/Sniffs/PHP/ForbiddenFunctionsSniff.php',
						'Generic/Sniffs/PHP/LowerCaseConstantSniff.php');
	}
}
?>