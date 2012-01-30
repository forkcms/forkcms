<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package		spoon
 * @subpackage	file
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		1.2.0
 */


/**
 * This class provides functions to work with CSV-files.
 *
 * @package		spoon
 * @subpackage	file
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @author		Dave Lens <dave@spoon-library.com>
 * @since		1.2.0
 */
class SpoonFileCSV
{
	const DEFAULT_DELIMITER = ',';
	const DEFAULT_ENCLOSURE = '"';

	/**
	 * Converts an array to a CSV file
	 *
	 * @return	mixed
	 * @param	string $path						The full path to the file you wish to create.
	 * @param	array $array						The array to convert.
	 * @param	array[optional] $columns			The column names you want to use.
	 * @param	array[optional] $excludeColumns		The columns you want to exclude.
	 * @param	string[optional] $delimiter			The field delimiter of the CSV.
	 * @param	string[optional] $enclosure			The enclosure character of the CSV.
	 * @param	bool[optional] $download			Should the file be downloaded?
	 */
	public static function arrayToFile($path, array $array, array $columns = null, array $excludeColumns = null, $delimiter = ',', $enclosure = '"', $download = false)
	{
		// get the content of the file
		$csv = self::arrayToString($array, $columns, $excludeColumns, $delimiter, $enclosure);

		// set file content
		SpoonFile::setContent($path, $csv);

		// user chose to download this file
		if($download) return self::download($path);
	}


	/**
	 * Converts an array into a CSV-formatted string
	 *
	 * @return	string
	 * @param	array $array						The array to convert.
	 * @param	array[optional] $columns			The column names you want to use.
	 * @param	array[optional] $excludeColumns		The columns you want to exclude.
	 * @param	string[optional] $delimiter			The field delimiter of the CSV.
	 * @param	string[optional] $enclosure			The enclosure character of the CSV.
	 * @param	string[optional] $lineEnding		The line-ending of the CSV.
	 */
	public static function arrayToString(array $array, array $columns = null, array $excludeColumns = null, $delimiter = ',', $enclosure = '"', $lineEnding = null)
	{
		// validate array
		if(!empty($array) && !isset($array[0])) throw new SpoonFileException('Invalid array format.');

		// no columns set means we will use the keys as the column name if they're not integers
		if(empty($columns) && isset($array[0][0])) throw new SpoonFileException('Provide column names or use strings as array keys.');

		// column names are set
		if(empty($columns)) $columns = array_keys($array[0]);

		// check for delimiter/enclosure
		if($delimiter === null) $delimiter = self::DEFAULT_DELIMITER;
		if($enclosure === null) $enclosure = self::DEFAULT_ENCLOSURE;
		if($lineEnding === null) $lineEnding = PHP_EOL;

		// unset the excluded columns
		if(!empty($excludeColumns)) foreach($excludeColumns as $column) unset($columns[array_search($column, $columns)]);

		// escape enclosure chars
		$columns = self::escapeEnclosure($columns, $enclosure);

		// start the string with the columns
		$csv = $enclosure . implode($enclosure . $delimiter . $enclosure, $columns) . $enclosure . $lineEnding;

		// stop here if the array is empty
		if(empty($array)) return $csv;

		// count columns and cells
		$countCells = count($array[0]);

		// loop the array
		foreach($array as $row)
		{
			// cellcount check
			if($countCells != count($row)) throw new SpoonFileException('Each row has to have the same number of cells as the first row.');

			// some columns are excluded
			if(!empty($excludeColumns))
			{
				// unset the excluded columns
				foreach($excludeColumns as $column) unset($row[$column], $columns[array_search($column, $columns)]);
			}

			// escape enclosure chars
			$row = self::escapeEnclosure($row, $enclosure);

			// add this row to the CSV
			$csv .= $enclosure . implode($enclosure . $delimiter . $enclosure, (array) $row) . $enclosure . $lineEnding;
		}

		// no input
		return $csv;
	}


	/**
	 * Sets the headers so we may download the CSV file in question
	 *
	 * @return	array
	 * @param	string $path	The full path to the CSV file you wish to download.
	 */
	private static function download($path)
	{
		// check if the file exists
		if(!SpoonFile::exists($path)) throw new SpoonFileException('The file ' . $path . ' doesn\'t exist.');

		// fetch the filename from the path string
		$explodedFilename = explode('/', $path);
		$filename = end($explodedFilename);

		// set headers for download
		$headers = array();
		$headers[] = 'Content-type: text/csv; charset=utf-8';
		$headers[] = 'Content-Disposition: attachment; filename="' . $filename . '"';

		// overwrite the headers
		SpoonHTTP::setHeaders($headers);

		// get the file contents
		$content = SpoonFile::getContent($path);

		// delete the CSV file
		SpoonFile::delete($path);

		// output the file contents
		echo $content;

		// exit here
		exit;
	}


	/**
	 * Escape the character that is being used as enclosure in a csv row.
	 *
	 * @return	array				The escaped version of the row coming in.
	 * @param	array $row			The row to escape.
	 * @param	string $enclosure	The character being used as enclosure.
	 */
	public static function escapeEnclosure($row, $enclosure)
	{
		// init var
		$escaped = array();

		// apply enclosure
		foreach($row as $key => $value)
		{
			$escaped[$key] = str_replace($enclosure, $enclosure . $enclosure, $value);
		}

		return $escaped;
	}


	/**
	 * Converts a CSV file to an array
	 *
	 * @return	array
	 * @param	array $path						The full path to the CSV-file you want to extract an array from.
	 * @param	array[optional] $columns		The column names you want to use.
	 * @param	array[optional] $excludeColumns	The columns to exclude.
	 * @param	string[optional] $delimiter		The field delimiter of the CSV.
	 * @param	string[optional] $enclosure		The enclosure character of the CSV.
	 */
	public static function fileToArray($path, array $columns = array(), array $excludeColumns = null, $delimiter = ',', $enclosure = '"')
	{
		// reset variables
		$path = (string) $path;

		// validation
		if(!SpoonFile::exists($path)) throw new SpoonFileException($path . ' doesn\'t exists.');

		// get delimiter and enclosure from the contents
		if(!$delimiter || !$enclosure) $autoDetect = self::getDelimiterAndEnclosure(SpoonFile::getContent($path), $delimiter, $enclosure);
		if(!$delimiter) $delimiter = $autoDetect[0];
		if(!$enclosure) $enclosure = $autoDetect[1];

		// automagicaly detect line endings
		@ini_set('auto_detect_line_endings', 1);

		// init var
		$rows = array();

		// open file
		$handle = @fopen($path, 'r');

		// loop lines and store the rows
		while(($row = @fgetcsv($handle, 0, (($delimiter == '') ? ',' : $delimiter), (($enclosure == '') ? '"' : $enclosure))) !== false) $rows[] = $row;

		// close file
		@fclose($handle);

		// no lines
		if(count($rows) == 0) return false;

		// no column names are set
		if(empty($columns)) $columns = array_values($rows[0]);

		// remove the first row
		array_shift($rows);

		// loop the rows
		foreach($rows as $rowId => &$row)
		{
			// the keys of this row
			$keys = array_keys($row);

			// some columns are excluded
			if(!empty($excludeColumns))
			{
				// unset the keys related to the excluded columns
				foreach($excludeColumns as $columnKey => $column) unset($keys[array_search($columnKey, $columns)], $row[$columnKey]);
			}

			// loop the keys
			foreach($keys as $columnId)
			{
				// add the field to this row
				$row[$columns[$columnId]] = $row[$columnId];

				// remove the original field from this row
				unset($row[$columnId]);
			}
		}

		// return the array
		return $rows;
	}


	/**
	 * Returns an array with the delimiter and enclosure used in the given string
	 *
	 * @return	array
	 * @param	string $string					The string you want to extract delimiter and enclosure from.
	 * @param	string[optional] $delimiter		The delimiter you wish to use instead of the default.
	 * @param	string[optional] $enclosure		The enclosure you wish to use instead of the default.
	 */
	private static function getDelimiterAndEnclosure($string, $delimiter = null, $enclosure = null)
	{
		// reset variables
		$string = (string) $string;
		$delimiter = ($delimiter == null) ? self::DEFAULT_DELIMITER : $delimiter;
		$enclosure = ($enclosure == null) ? self::DEFAULT_ENCLOSURE : $enclosure;
		$delimiterCount = 0;
		$enclosureCount = 0;

		// check for comma delimiter
		if(preg_match_all('/,/', $string, $matches))
		{
			// count the commas
			$newCount = count($matches[0]);

			// replace the delimiter, if need be
			if($delimiterCount < $newCount)
			{
				// overwrite the count
				$delimiterCount = $newCount;
				$delimiter = ',';
			}
		}

		// check for semicolon delimiter
		if(preg_match_all('/;/', $string, $matches))
		{
			// count the commas
			$newCount = count($matches[0]);

			// replace the delimiter, if need be
			if($delimiterCount < $newCount)
			{
				// overwrite the count
				$delimiterCount = $newCount;
				$delimiter = ';';
			}
		}

		// check for tab delimiter
		if(preg_match_all('/[\t]/', $string, $matches))
		{
			// count the commas
			$newCount = count($matches[0]);

			// replace the delimiter, if need be
			if($delimiterCount < $newCount)
			{
				// overwrite the count
				$delimiterCount = $newCount;
				$delimiter = '\t';
			}
		}

		// if delimiter is empty it might mean there is only one column

		// check for double quotes enclosure
		if(preg_match_all('/"/', $string, $matches))
		{
			// count the commas
			$newCount = count($matches[0]);

			// replace the delimiter, if need be
			if($enclosureCount < $newCount)
			{
				// overwrite the count
				$enclosureCount = $newCount;
				$enclosure = '"';
			}
		}

		// check for single quotes enclosure
		if(preg_match_all("/'/", $string, $matches))
		{
			// count the commas
			$newCount = count($matches[0]);

			// replace the delimiter, if need be
			if($enclosureCount < $newCount)
			{
				// overwrite the count
				$enclosureCount = $newCount;
				$enclosure = "'";
			}
		}

		// return the results
		return array($delimiter, $enclosure);
	}


	/**
	 * Converts a CSV-formatted string to an array
	 *
	 * @return	array
	 * @param	string $string					The string you wish to convert to an array.
	 * @param	array[optional] $columns		The column names you want to use.
	 * @param	array[optional] $excludeColumns	The columns to exclude.
	 * @param	string[optional] $delimiter		The field delimiter of the CSV.
	 * @param	string[optional] $enclosure		The enclosure character of the CSV.
	 */
	public static function stringToArray($string, array $columns = array(), array $excludeColumns = null, $delimiter = ',', $enclosure = '"')
	{
		// reset variables
		$string = (string) $string;
		$filename = dirname(__FILE__) . '/' . uniqid();

		// save a tempfile
		SpoonFile::setContent($filename, $string);

		// return the file to array
		$array = self::fileToArray($filename, $columns, $excludeColumns, $delimiter, $enclosure);

		// remove the created file
		SpoonFile::delete($filename);

		// return the array
		return $array;
	}
}
