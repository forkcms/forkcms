<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package		filesystem
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @author 		Tijs Verkoyen <tijs@spoon-library.be>
 * @author		Dave Lens <dave@spoon-library.be>
 * @since		0.1.1
 */


/** SpoonFilter class */
require_once 'spoon/filter/filter.php';


/**
 * This exception is used to handle filesystem related exceptions.
 *
 * @package		filesystem
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		0.1.1
 */
class SpoonFileSystemException extends SpoonException {}


/**
 * Most of the functions you can apply on folders.
 *
 * @package		filesystem
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @author		Tijs Verkoyen <tijs@spoon-library.be>
 * @since		0.1.1
 */
class SpoonDirectory
{
	/**
	 * Creates a folder with the given chmod settings
	 *
	 * @return	bool
	 * @param	string $folder
	 * @param	string[optional] $chmod
	 */
	public static function create($folder, $chmod = 0777)
	{
		return @mkdir((string) $folder, $chmod, true);
	}


	/**
	 * Copies a file/folder
	 *
	 * @return	bool
	 * @param	string $source
	 * @param	string $destination
	 * @param	bool[optional] $strict
	 * @param 	int[optional] $chmod
	 */
	public static function copy($source, $destination, $overwrite = true, $strict = true, $chmod = 0777)
	{
		// redefine vars
		$source = (string) $source;
		$destination = (string) $destination;
		$return = true;

		// validation
		if($strict)
		{
			if(!@file_exists($source)) throw new SpoonFileSystemException('The given path ('. $source .') doesn\'t exist.');
			if(!$overwrite && @file_exists($destination)) throw new SpoonFileSystemException('The given path ('. $destination .') already exists.');
		}

		// is a directory
		if(is_dir($source))
		{
			// create dir
			if(!self::exists($destination))
			{
				// create dir
				$return = self::create($destination, $chmod);

				// check
				if(!$return)
				{
					if($strict) throw new SpoonFileSystemException('The directory-structure couldn\'t be created.');
					return false;
				}
			}

			// get content
			$contentList = (array) self::getList($source, true);

			// loop content
			foreach ($contentList as $item)
			{
				// copy dir (recursive)
				if(is_dir($source .'/'. $item)) self::copy($source .'/'. $item, $destination .'/'. $item);
				else
				{
					// delete the file if needed
					if($overwrite && SpoonFile::exists($destination .'/'. $item)) SpoonFile::delete($destination .'/'. $item);

					// copy file
					if(!SpoonFile::exists($destination .'/'. $item))
					{
						// copy file
						$return = @copy($source .'/'. $item, $destination .'/'. $item);

						// check
						if(!$return)
						{
							if($strict) throw new SpoonFileSystemException('The directory/file ('. $source .'/'. $item .') couldn\'t be copied.');
							return false;
						}

						// chmod
						SpoonFile::chmod($destination .'/'. $item, $chmod);
					}
				}
			}
		}

		// not a directory
		else
		{
			// delete the file if needed
			if($overwrite && SpoonFile::exists($destination)) SpoonFile::delete($destination);

			// copy file
			if(!SpoonFile::exists($destination))
			{
				// copy file
				$return = @copy($source, $destination);

				// check
				if(!$return)
				{
					if($strict) throw new SpoonFileSystemException('The directory/file ('. $source .') couldn\'t be copied.');
					return false;
				}

				// chmod
				@chmod($destination, $chmod);
			}
		}

		// return
		return true;
	}


	/**
	 * Deletes a directory and all of its subdirectories
	 *
	 * @return	bool
	 * @param	string $directory
	 */
	public static function delete($directory)
	{
		// redfine directory
		$directory = (string) $directory;

		// directory exists
		if(self::exists($directory))
		{
			// get the list
			$list = self::getList($directory, true);

			// has subdirectories/files
			if(count($list) != 0)
			{
				// loop directories and execute function
				foreach((array) $list as $item)
				{
					// delete folder recursive
					if(is_dir($directory .'/'. $item)) self::delete($directory .'/'. $item);

					// delete file
					else SpoonFile::delete($directory .'/'. $item);
				}
			}

			// has no content
			@rmdir($directory);
		}

		// directory doesn't exist
		else return false;
	}


	/**
	 * Checks if this directory exists
	 *
	 * @return	bool
	 * @param	string $directory
	 */
	public static function exists($directory)
	{
		// redefine directory
		$directory = (string) $directory;

		// directory exists
		if(file_exists($directory) && is_dir($directory)) return true;

		// doesn't exist
		return false;
	}


	/**
	 * Returns a list of directorys within a directory
	 *
	 * @return	array
	 * @param	string $path
	 * @param	bool[optional] $showFiles
	 * @param	array[optional] $excluded
	 * @param 	string[optional] $includeRegexp
	 */
	public static function getList($path, $showFiles = false, $excluded = array(), $includeRegexp = null)
	{
		// redefine arguments
		$path = (string) $path;
		$showFiles = (bool) $showFiles;
		$excluded = (array) $excluded;

		// validate regex
		if($includeRegexp !== null)
		{
			// redefine
			$includeRegexp = (string) $includeRegexp;

			// validate
			if(!SpoonFilter::isValidRegexp($includeRegexp)) throw new SpoonFileSystemException('Invalid regular expression ('. $includeRegexp .').');
		}

		// define file list
		$aDirectories = array();

		// directory exists
		if(self::exists($path))
		{
			// attempt to open directory
			$directory = @opendir($path);

			// do your thing if directory-handle isn't false
			if($directory !== false)
			{
							// start reading
				while((($file = readdir($directory)) !== false))
				{
					// no '.' and '..' and it's a file
					if(($file != '.') && ($file != '..'))
					{
						// directory
						if(is_dir($path .'/'. $file))
						{
							// exclude certain files
							if(count($excluded) != 0)
							{
								if(!in_array($file, $excluded))
								{
									if($includeRegexp !== null)
									{
										// init var
										$aMatches = array();

										// is this a match?
										if(preg_match($includeRegexp, $file, $aMatches) != 0) $aDirectories[] = $file;
									}

									// add to list
									else $aDirectories[] = $file;
								}
							}

							// no excludes defined
							else
							{
								if($includeRegexp !== null)
								{
									// init var
									$aMatches = array();

									// is this a match?
									if(preg_match($includeRegexp, $file, $aMatches) != 0) $aDirectories[] = $file;
								}

								// add to list
								else $aDirectories[] = $file;
							}
						}

						// file
						else
						{
							// show files
							if($showFiles)
							{
								// exclude certain files
								if(count($excluded) != 0)
								{
									if(!in_array($file, $excluded))
									{
										$aDirectories[] = $file;
									}
								}

								// add file
								else $aDirectories[] = $file;
							}
						}
					}
				}
			}

			// close directory
			@closedir($directory);
		}

		// cough up directory listing
		natsort($aDirectories);

		return $aDirectories;
	}


	/**
	 * Retrieve the size of a directory in megabytes
	 *
	 * @return	int
	 * @param	string $path
	 * @param	bool[optional] $subdirectories
	 */
	public static function getSize($path, $subdirectories = true)
	{
		// internal size
		$size = 0;

		// redefine arguments
		$path = (string) $path;
		$subdirectories = (bool) $subdirectories;

		// directory doesn't exists
		if(!self::exists($path)) return false;

		// directory exists
		else
		{
			$list = (array) self::getList($path, true);

			// loop list
			foreach($list as $item)
			{
				// get directory size if subdirectories should be included
				if(is_dir($path .'/'. $item) && $subdirectories) $size += self::getSize($path .'/'. $item, $subdirectories);

				// add filesize
				else $size += filesize($path .'/'. $item);
			}
		}

		// return good size
		return $size;
	}


	/**
	 * Renames a folder/file
	 *
	 * @return	bool
	 * @param	string $source
	 * @param	string $destination
	 * @param 	bool[optional] $overwrite
	 * @param	int[optional] $chmod
	 */
	public static function move($source, $destination, $overwrite = true, $chmod = 0777)
	{
		// redefine vars
		$source = (string) $source;
		$destination = (string) $destination;
		$overwrite = (bool) $overwrite;

		// validation
		if(!file_exists($source)) throw new SpoonFileSystemException('The given path ('. $source .') doesn\'t exists.');
		if(!$overwrite && file_exists($destination)) throw new SpoonFileSystemException('The given destination ('. $destination .') already exists.');

		// create missing directories
		if(!file_exists(dirname($destination))) self::create(dirname($destination));

		// delete file
		if($overwrite && file_exists($destination)) self::delete($destination);

		// rename
		$return = @rename($source, $destination);
		@chmod($destination, $chmod);

		// return
		return $return;
	}
}


/**
 * This class provides a wide range of methods to be used on
 * files.
 *
 * @package		filesystem
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @author		Tijs Verkoyen <tijs@spoon-library.be>
 * @since		0.1.1
 */
class SpoonFile
{
	/**
	 * Deletes the filename
	 *
	 * @return	bool
	 * @param	string $filename
	 */
	public static function delete($filename)
	{
		return @unlink((string) $filename);
	}


	/**
	 * Download a file from a public url
	 *
	 * @return	bool
	 * @param	string $sourceUrl
	 * @param	string $destinationPath
	 * @param	bool[optional] $overwrite
	 */
	public static function download($sourceUrl, $destinationPath, $overwrite = true)
	{
		// redefine
		$sourceUrl = (string) $sourceUrl;
		$destinationPath = (string) $destinationPath;
		$overwrite = (bool) $overwrite;

		// validate if the file already exists
		if(!$overwrite && self::exists($destinationPath)) return false;

		// open file handler
		$fileHandle = @fopen($destinationPath, 'w');

		// validate filehandle
		if($fileHandle === false) return false;

		$options = array(CURLOPT_URL => $sourceUrl,
						 CURLOPT_FILE => $fileHandle,
						 CURLOPT_HEADER => false);

		// init curl
		$curl = curl_init();

		// set options
		curl_setopt_array($curl, $options);

		// execute the call
		curl_exec($curl);

		// get errornumber
		$errorNumber = curl_errno($curl);
		$errorMessage = curl_error($curl);
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		// close
		curl_close($curl);
		fclose($fileHandle);

		// validate the errornumber
		if($errorNumber != 0) throw new SpoonFileSystemException($errorMessage);
		if($httpCode != 200) throw new SpoonFileSystemException('The file "'. $sourceUrl .'" isn\'t available for download.');

		// return
		return true;
	}


	/**
	 * Does this file exist
	 *
	 * @return	bool
	 * @param	string $filename
	 */
	public static function exists($filename)
	{
		return (@file_exists((string) $filename) && is_file((string) $filename));
	}


	/**
	 * Fetch the extension for a filename
	 *
	 * @return	string
	 * @param	string $filename
	 * @param	bool[optional] $lowercase
	 */
	public static function getExtension($filename, $lowercase = true)
	{
		// @todo rewrite using reverse strpos
		// init var
		$filename = ($lowercase) ? strtolower((string) $filename) : (string) $filename;

		// fetch extension
		$aExtension = explode('.', $filename);

		// has an extension
		if(count($aExtension) != 0) return $aExtension[(count($aExtension) -1)];

		// no extension
		return '';
	}


	/**
	 * Fetch the content from a file or URL
	 *
	 * @return	string
	 * @param	string $filename
	 */
	public static function getContent($filename)
	{
		return @file_get_contents((string) $filename);
	}


	/**
	 * Fetch the information about a file
	 *
	 * @return	array
	 * @param	string $filename
	 */
	public static function getInfo($filename)
	{
		// redefine
		$filename = (string) $filename;

		// init var
		$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');

		// fetch pathinfo
		$pathInfo = pathinfo($filename);

		// build details array
		$aFile['basename'] = $pathInfo['basename'];
		$aFile['extension'] = self::getExtension($filename);
		$aFile['name'] = substr($aFile['basename'], 0, strlen($aFile['basename']) - strlen($aFile['extension']) -1);
		$aFile['size'] = @filesize($filename);
		$aFile['is_executable'] = @is_executable($filename);
		$aFile['is_readable'] = @is_readable($filename);
		$aFile['is_writable'] = @is_writable($filename);
		$aFile['modification_date'] = @filemtime($filename);
		$aFile['path'] = $pathInfo['dirname'];
		$aFile['permissions'] = @fileperms($filename);

		// calculate human readable size
		$size = $aFile['size'];
		$mod = 1024;
		for($i = 0; $size > $mod; $i++) $size /= $mod;
		$aFile['human_readable_size'] = round($size, 2) .' '. $units[$i];

		// clear cache
		@clearstatcache();

		// cough it up
		return $aFile;
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
	 * Renames a folder/file, this is just an alias for SpoonDirectory::move();
	 *
	 * @return	bool
	 * @param	string $source
	 * @param	string $destination
	 * @param 	bool[optional] $overwrite
	 * @param	int[optional] $chmod
	 */
	public static function move($source, $destination, $overwrite = true, $chmod = 0777)
	{
		// this is just an alias for SpoonDirectory::move
		return SpoonDirectory::move($source, $destination, $overwrite, $chmod);
	}


	/**
	 * Writes a string to a file
	 *
	 * @return	bool
	 * @param	string $filename
	 * @param	string $content
	 * @param	bool[optional] $createFile
	 * @param	bool[optional] $append
	 * @param	int[optional] $chmod
	 */
	public static function setContent($filename, $content, $createFile = true, $append = false, $chmod = 0777)
	{
		// redefine vars
		$filename = (string) $filename;
		$content = (string) $content;
		$createFile = (bool) $createFile;
		$append = (bool) $append;

		// file may not be created, but it doesn't exist either
		if(!$createFile && self::exists($filename)) throw new SpoonFileSystemException('The file "'. $filename .'" doesn\'t exist');

		// create directory recursively if needed
		SpoonDirectory::create(dirname($filename), $chmod, true);

		// create file & open for writing
		$handler = ($append) ? @fopen($filename, 'a') : @fopen($filename, 'w');

		// something went wrong
		if($handler === false) throw new SpoonFileSystemException('The file "'. $filename .'" could not be created. Check if PHP has enough permissions.');

		// write to file & close it
		@fwrite($handler, $content);
		@fclose($handler);

		// chmod file
		@chmod($filename, $chmod);

		// status
		return true;
	}
}

?>