<?php

/**
 * Checks all lines in the file, and throws warnings if they are over a certain length.
 *
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class Fork_Sniffs_Styleguide_LineLengthSniff extends Generic_Sniffs_Files_LineLengthSniff implements PHP_CodeSniffer_Sniff
{
	/**
	 * The limit that the length of a line should not exceed.
	 *
	 * @var int
	 */
	public $lineLimit = 120;

	/**
	 * The limit that the length of a line must not exceed. Set to zero (0) to disable.
	 *
	 * @var int
	 */
	public $absoluteLineLimit = 0;
}
