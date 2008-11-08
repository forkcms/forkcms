<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package			filesystem
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			0.1.1
 */


/** Spoon class */
require_once 'spoon/spoon.php';

/** SpoonFileSystemException class */
require_once 'spoon/filesystem/exception.php';

/** SpoonDirectory class */
require_once 'spoon/filesystem/directory.php';


/**
 * This base class provides all the methods used on files.
 *
 * @package			filesystem
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			0.1.1
 */
class SpoonCSV
{
	/**
	 * Reads an entire (CSV) file into an array
	 *
	 * @return	array
	 * @param	string $path
	 * @param	bool[optional] $firstRowAreColumnNames
	 * @param	string[optional] $splitChar
	 * @param	string[optional] $encapsulationString
	 */
	public static function fileToArray($path, $firstRowAreColumnNames = true, $splitChar = ';', $encapsulationString = '')
	{
		// reset variables
		$path = (string) $path;
		$firstRowAreColumnNames = (bool) $firstRowAreColumnNames;
		$splitChar = (string) $splitChar;
		$encapsulationString = (string) $encapsulationString;

		// validation
		if(!SpoonFile::exists($path)) throw new SpoonFileSystemException($path . ' doesn\'t exists.');

		// automagicaly detect line endings
		@ini_set('auto_detect_line_endings', 1);

		// init var
		$rows = array();

		// open file
		$handle = @fopen($path, 'r');

		// get data
		if($encapsulationString == '')
		{
			// loop lines
			while (($row = @fgetcsv($handle, 0, $splitChar)) !== false) $rows[] = $row;
		}
		else
		{
			// loop lines
			while (($row = @fgetcsv($handle, 0, $splitChar, $encapsulationString)) !== false) $rows[] = $row;
		}

		// close file
		@fclose($handle);

		// count lines
		$iLines = count($rows);

		// no lines
		if($iLines == 0) return false;

		// first row are column-names
		if($firstRowAreColumnNames && isset($rows[0]))
		{
			// get column-names
			$aColumnNames = array_values($rows[0]);

			// remove first row
			array_shift($rows);

			// init var
			$rowCounter = 0;
			$aData = array();

			// loop actual data
			foreach ($rows as $row)
			{
				// init var
				$fieldCounter = 0;

				// loop fields
				foreach ($row as $field)
				{
					// check if the columnname exists and use it as key
					if(isset($aColumnNames[$fieldCounter])) $aData[$rowCounter][strtolower($aColumnNames[$fieldCounter])] = $field;

					// otherwise give it a number
					else $aData[$rowCounter][$fieldCounter] = $field;

					// increment
					$fieldCounter++;
				}

				// increment
				$rowCounter++;
			}

			// return
			return $aData;
		}

		// first row is also date
		else return $rows;

		// fallback
		return false;
	}


	/**
	 * Converts an array into CSV-format
	 *
	 * @return	string
	 * @param	array $array
	 * @param	bool[optional] $keysAreColumnNames
	 * @param	string[optional] $splitChar
	 * @param	string[optional] $encapsulationString
	 */
	public static function arrayToCSV($array = array(), $keysAreColumnNames = true, $splitChar = ';', $encapsulationString = '', $htmlDecode = false)
	{
		// init vars
		$headerString = '';
		$dataString = '';
		$countCells = 0;

		// validate array
		if(!isset($array[0])) throw new SpoonFileSystemException('Invalid array-format.');

		// count cels
		$countCells = count($array[0]);

		// get column-names if needed
		if($keysAreColumnNames)
		{
			// get keys
			$headers = array_keys($array[0]);

			// convert data to CSV
			$headerString = $encapsulationString . join($encapsulationString . $splitChar . $encapsulationString, $headers) . $encapsulationString . "\n";
		}

		// loop rows
		foreach ($array as $row)
		{
			// check if the cellcount is ok.
			if($countCells != count($row)) throw new SpoonFileSystemException('Each row has to have the same number of cells as the first row.');

			// decode htmlentities
			if($htmlDecode)
			{
				$temp = array_map(array('SpoonFilter', 'htmlentitiesDecode'), $row);
				$row = $temp;
			}

			// convert data to CSV
			$dataString .= $encapsulationString . join($encapsulationString . $splitChar . $encapsulationString, $row) . $encapsulationString ."\n";
		}

		// return
		return $headerString . $dataString;
	}
}

?>