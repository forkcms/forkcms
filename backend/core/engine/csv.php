<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is our extended version of SpoonFileCSV
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendCSV extends SpoonFileCSV
{
	/**
	 * Output a CSV-file as a download
	 *
	 * @param string $filename					The name of the file.
	 * @param array $array						The array to convert.
	 * @param array[optional] $columns			The column names you want to use.
	 * @param array[optional] $excludeColumns	The columns you want to exclude.
	 */
	public static function outputCSV($filename, array $array, array $columns = null, array $excludeColumns = null)
	{
		// get settings
		$splitCharacter = BackendAuthentication::getUser()->getSetting('csv_split_character');
		$lineEnding = BackendAuthentication::getUser()->getSetting('csv_line_ending');

		// reformat
		if($lineEnding == '\n') $lineEnding = "\n";
		if($lineEnding == '\r\n') $lineEnding = "\r\n";

		// convert into CSV
		$csv = SpoonFileCSV::arrayToString($array, $columns, $excludeColumns, $splitCharacter, '"', $lineEnding);

		// set headers for download
		$headers[] = 'Content-type: application/csv; charset=' . SPOON_CHARSET;
		$headers[] = 'Content-Disposition: attachment; filename="' . $filename;
		$headers[] = 'Content-Length: ' . strlen($csv);
		$headers[] = 'Pragma: no-cache';

		// overwrite the headers
		SpoonHTTP::setHeaders($headers);

		// ouput the CSV
		echo $csv;
		exit;
	}
}
