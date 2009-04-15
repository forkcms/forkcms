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
 * @since			0.1.1
 */
final class SpoonFile
{
	/**
	 * Attempts to chmod the given file & returns the status (octal mode required)
	 *
	 * @return	bool
	 * @param	string $filename
	 * @param	string[optional] $mode
	 */
	public static function chmod($filename, $mode = 0777)
	{
		// redefine filename
		$filename = (string) $filename;

		// return chmod status
		return @chmod($filename, $mode);
	}


	/**
	 * Copies a file/folder alias for SpoonDirectory::copy
	 *
	 * @return	bool
	 * @param	string $source
	 * @param	string $destination
	 * @param 	int[optional] $chmod
	 */
	public static function copy($source, $destination, $overwrite = true, $chmod = 0777)
	{
		return SpoonDirectory::copy($source, $destination, $overwrite, $chmod);
	}


	/**
	 * Deletes the given filename or returns false
	 *
	 * @return	bool
	 * @param	string $filename
	 */
	public static function delete($filename)
	{
		// file exists
		if(self::exists($filename, false)) return @unlink($filename);

		// doesn't exist
		return false;
	}


	/**
	 * Returns true if the given filename exists and is a file and is readable
	 *
	 * @return	bool
	 * @param	string $filename
	 * @param	bool[optional] $strict
	 */
	public static function exists($filename, $strict = true)
	{
		// redefine arguments
		$filename = (string) $filename;
		$strict = (bool) $strict;

		// file exists
		if(file_exists($filename))
		{
			// non-strict mode
			if(!$strict) return true;

			// strict mode
			if(is_file($filename) && is_readable($filename)) return true;
		}

		return false;
	}


	/**
	 * Retrieve the extension from the given filename/string
	 *
	 * @return	string
	 * @param	string $filename
	 * @param	bool[optional] $lowercase
	 */
	public static function getExtension($filename, $lowercase = true)
	{
		// redefine filename
		$filename = ($lowercase) ? strtolower((string) $filename) : (string) $filename;

		// define extension
		$extension = explode('.', $filename);

		// has an extension
		if(count($extension) != 0) return $extension[(count($extension) -1)];

		// no extension
		return '';
	}


	/**
	 * Retrieves the content of the given file as an array or string
	 *
	 * @return	mixed
	 * @param	string $filename
	 * @param	string[optional] $type
	 * @param	bool[optional] $strict
	 */
	public static function getFileContent($filename, $type = 'string', $strict = true)
	{
		// redefine filename & type
		$filename = (string) $filename;
		$type = SpoonFilter::getValue($type, array('array', 'string'), 'string');
		$strict = (bool) $strict;

		// strict mode
		if($strict && !self::exists($filename)) throw new SpoonFileSystemException('The file "'. $filename .'" does not exist or can not be read.');

		// load as string
		if($type == 'string') return @file_get_contents($filename, false);

		// load as array
		return @file($filename);
	}


	/**
	 * Retrieves info about a file
	 *
	 * @return	array
	 * @param	string $filename
	 */
	public static function getFileInfo($filename)
	{
		// redefine var
		$filename = (string) $filename;

		// get pathinfo
		$pathInfo = pathinfo($filename);

		// build array
		$fileInfo['basename'] = $pathInfo['basename'];
		$fileInfo['extension'] = self::getExtension($filename);
		$fileInfo['file_name'] = substr($pathInfo['basename'], 0, strlen($fileInfo['basename']) - strlen($fileInfo['extension']) -1);
		$fileInfo['file_size'] = @filesize($filename);
		$fileInfo['is_executable'] = @is_executable($filename);
		$fileInfo['is_readable'] = @is_readable($filename);
		$fileInfo['is_writable'] = @is_writable($filename);
		$fileInfo['modification_date'] = @filemtime($filename);
		$fileInfo['path'] = $pathInfo['dirname'];
		$fileInfo['permissions'] = @fileperms($filename);

		// clear cache
		@clearstatcache();

		// array
		return $fileInfo;
	}


	/**
	 * Retrieves a list of files within a directory (optionally excluding some files)
	 *
	 * @return	array
	 * @param	string $path
	 * @param	string[optional] $includeRegexp
	 */
	public static function getList($path, $includeRegexp = null)
	{
		// redefine arguments
		$path = (string) $path;

		// validate regex
		if($includeRegexp !== null)
		{
			// redefine
			$includeRegexp = (string) $includeRegexp;

			// validate
			if(!SpoonFilter::isValidRegexp($includeRegexp)) throw new SpoonFileSystemException('Invalid regular expression ('. $includeRegexp .')');
		}

		// define list
		$aFiles = array();

		// directory exists
		if(SpoonDirectory::exists($path))
		{
			// attempt to open dire
			if($directory = @opendir($path))
			{
				// start reading
				while ((($file = readdir($directory)) !== false))
				{
					// no '.' and '..' and it's a file
					if(($file != '.') && ($file != '..') && is_file($path .'/'. $file))
					{
						// is there a include-pattern?
						if($includeRegexp !== null)
						{
							// init var
							$aMatches = array();

							// is this a match?
							if(preg_match($includeRegexp, $file, $aMatches) != 0) $aFiles[] = $file;
						}

						// no excludes defined
						else $aFiles[] = $file;
					}
				}
			}

			// close directory
			@closedir($directory);
		}

		// directory doesn't exist or a problem occured
		return $aFiles;
	}


	/**
	 * Puts content in the file
	 *
	 * @return	void
	 * @param	string $filename
	 * @param	string $string
	 * @param	bool[optional] $append
	 * @param	bool[optional] $createFile
	 * @param	int[optional] $chmod
	 */
	public static function setFileContent($filename, $string, $append = false, $createFile = true, $chmod = 0777)
	{
		// redefine vars
		$filename = (string) $filename;
		$string = (string) $string;
		$append = (bool) $append;
		$createFile = (bool) $createFile;

		// check if the file exists
		if(!$createFile && self::exists($filename)) throw new SpoonFileSystemException('The file (' . $filename . ')doesn\'t exists.');

		// check for dirs
		$chunks = explode('/', $filename);
		$path = '';

		// remove last
		array_pop($chunks);

		// loop chunks
		foreach ($chunks as $chunk)
		{
			$path .= '/'. $chunk;

			// create dir if it not exists
			if(!SpoonDirectory::exists($path)) SpoonDirectory::create($path);
		}

		// open file and get the handler in append mode
		if($append) $fileHandler = @fopen($filename, 'a');
		else $fileHandler = @fopen($filename, 'w');

		// check if there are errors
		if(!$fileHandler) throw new SpoonFileSystemException('The file ('. $filename .') couldn\'t be created. Check if PHP has enough permissions.');

		// write content
		@fwrite($fileHandler, $string);
		@fclose($fileHandler);
		self::chmod($filename, $chmod);

		return true;
	}


	/**
	 * Renames a file, alias for SpoonDirectory::move
	 *
	 * @return	bool
	 * @param	string $source
	 * @param	string $destination
	 * @param 	bool[optional] $overwrite
	 * @param	string[optional] $chmod
	 */
	public static function move($source, $destination, $overwrite = true, $chmod = 0777)
	{
		SpoonDirectory::move($source, $destination, $overwrite, $chmod);
	}
}

?>