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
	 * Creates a folder with the given chmod settings.
	 *
	 * @return	bool						True if the directory was created, false if not.
	 * @param	string $directory			The name for the directory.
	 * @param	string[optional] $chmod		Mode that will be applied on the directory.
	 */
	public static function create($directory, $chmod = 0777)
	{
		return @mkdir((string) $directory, $chmod, true);
	}


	/**
	 * Copies a file/folder.
	 *
	 * @return	bool						True if the file/directory was copied, false if not.
	 * @param	string $source				The full path to the source file/folder.
	 * @param	bool[optional] $overwrite	If the destination already exists, should we overwrite?
	 * @param	string $destination			The full path to the destination.
	 * @param	bool[optional] $strict		If strict is true, exceptions will be thrown when an error occures.
	 * @param 	int[optional] $chmod		Mode that will be applied on the file/directory.
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
			foreach($contentList as $item)
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
						@chmod($destination .'/'. $item, $chmod);
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
	 * Deletes a directory and all of its subdirectories.
	 *
	 * @return	bool				True if the directory was deleted, false if not.
	 * @param	string $directory	Full path to the directory.
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
					// delete directory recursive
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
	 * Checks if this directory exists.
	 *
	 * @return	bool				True if the directory exists, false if not.
	 * @param	string $directory	Full path of the directory to check.
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
	 * Returns a list of directories within a directory.
	 *
	 * @return	array								An array containing all directories (and files if $showFiles is true).
	 * @param	string $path						Path of the directory.
	 * @param	bool[optional] $showFiles			Should files be included in the list.
	 * @param	array[optional] $excluded			An array containing directories/files to exclude.
	 * @param 	string[optional] $includeRegexp		An regular expression that represents the directories/files to include in the list. Other directories will be excluded.
	 */
	public static function getList($path, $showFiles = false, array $excluded = null, $includeRegexp = null)
	{
		// redefine arguments
		$path = (string) $path;
		$showFiles = (bool) $showFiles;

		// validate regex
		if($includeRegexp !== null)
		{
			// redefine
			$includeRegexp = (string) $includeRegexp;

			// validate
			if(!SpoonFilter::isValidRegexp($includeRegexp)) throw new SpoonFileSystemException('Invalid regular expression ('. $includeRegexp .').');
		}

		// define file list
		$directories = array();

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
										$matches = array();

										// is this a match?
										if(preg_match($includeRegexp, $file, $matches) != 0) $directories[] = $file;
									}

									// add to list
									else $directories[] = $file;
								}
							}

							// no excludes defined
							else
							{
								if($includeRegexp !== null)
								{
									// init var
									$matches = array();

									// is this a match?
									if(preg_match($includeRegexp, $file, $matches) != 0) $directories[] = $file;
								}

								// add to list
								else $directories[] = $file;
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
										$directories[] = $file;
									}
								}

								// add file
								else $directories[] = $file;
							}
						}
					}
				}
			}

			// close directory
			@closedir($directory);
		}

		// cough up directory listing
		natsort($directories);

		return $directories;
	}


	/**
	 * Retrieve the size of a directory in megabytes.
	 *
	 * @return	int									The size in MB.
	 * @param	string $path						The path of the directory.
	 * @param	bool[optional] $subdirectories		Should the subfolders be included in the calculation.
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
			// fetch list
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

		return $size;
	}


	/**
	 * Move/rename a directory/file.
	 *
	 * @return	bool						True if the directory was moved or renamed, false if not.
	 * @param	string $source				Path of the source directory.
	 * @param	string $destination			Path of the destination.
	 * @param 	bool[optional] $overwrite	Should an existing directory be overwritten?
	 * @param	int[optional] $chmod		Mode that should be applied on the directory.
	 */
	public static function move($source, $destination, $overwrite = true, $chmod = 0777)
	{
		// redefine vars
		$source = (string) $source;
		$destination = (string) $destination;
		$overwrite = (bool) $overwrite;

		// validation
		if(!file_exists($source)) throw new SpoonFileSystemException('The given path ('. $source .') doesn\'t exist.');
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
	 * Deletes a file,
	 *
	 * @return	bool				true if the file was deleted, false if not.
	 * @param	string $filename	Full path (including filename) of the file that should be deleted.
	 */
	public static function delete($filename)
	{
		return @unlink((string) $filename);
	}


	/**
	 * Download a file from a public URL.
	 *
	 * @return	bool						True if the file was downloaded, false if not.
	 * @param	string $sourceURL			The URL of the file to download.
	 * @param	string $destinationPath		The path where the file should be downloaded to.
	 * @param	bool[optional] $overwrite	In case the destinationPath already exists, should we overwrite this file?
	 */
	public static function download($sourceURL, $destinationPath, $overwrite = true)
	{
		// redefine
		$sourceURL = (string) $sourceURL;
		$destinationPath = (string) $destinationPath;
		$overwrite = (bool) $overwrite;

		// validate if the file already exists
		if(!$overwrite && self::exists($destinationPath)) return false;

		// open file handler
		$fileHandle = @fopen($destinationPath, 'w');

		// validate filehandle
		if($fileHandle === false) return false;

		$options = array(CURLOPT_URL => $sourceURL,
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
		if($httpCode != 200) throw new SpoonFileSystemException('The file "'. $sourceURL .'" isn\'t available for download.');

		// return
		return true;
	}


	/**
	 * Does this file exist.
	 *
	 * @return	bool				True if the file exists, false if not.
	 * @param	string $filename	The full path of the file to check for existance.
	 */
	public static function exists($filename)
	{
		return (@file_exists((string) $filename) && is_file((string) $filename));
	}


	/**
	 * Fetch the extension for a filename.
	 *
	 * @return	string						The extension.
	 * @param	string $filename			The full path of the file.
	 * @param	bool[optional] $lowercase	Should the extension be returned in lowercase or in its original form.
	 */
	public static function getExtension($filename, $lowercase = true)
	{
		// init var
		$filename = ($lowercase) ? strtolower((string) $filename) : (string) $filename;

		// fetch extension
		$aExtension = explode('.', $filename);

		// count the chunks
		$count = count($aExtension);

		// has an extension
		if($count != 0) return $aExtension[$count - 1];

		// no extension
		return '';
	}


	/**
	 * Fetch the content from a file or URL.
	 *
	 * @return	string				The content.
	 * @param	string $filename	The path or URL to the file. URLs will only work if fopen-wrappers are enabled.
	 */
	public static function getContent($filename)
	{
		return @file_get_contents((string) $filename);
	}


	/**
	 * Fetch the information about a file.
	 *
	 * @return	array				An array that contains a lot of information about the file.
	 * @param	string $filename	The path of the file.
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
		$file = array();
		$file['basename'] = $pathInfo['basename'];
		$file['extension'] = self::getExtension($filename);
		$file['name'] = substr($file['basename'], 0, strlen($file['basename']) - strlen($file['extension']) -1);
		$file['size'] = @filesize($filename);
		$file['is_executable'] = @is_executable($filename);
		$file['is_readable'] = @is_readable($filename);
		$file['is_writable'] = @is_writable($filename);
		$file['modification_date'] = @filemtime($filename);
		$file['path'] = $pathInfo['dirname'];
		$file['permissions'] = @fileperms($filename);

		// calculate human readable size
		$size = $file['size'];
		$mod = 1024;
		for($i = 0; $size > $mod; $i++) $size /= $mod;
		$file['human_readable_size'] = round($size, 2) .' '. $units[$i];

		// clear cache
		@clearstatcache();

		// cough it up
		return $file;
	}


	/**
	 * Retrieves a list of files within a directory.
	 *
	 * @return	array								An array containing a list of files in the given directory.
	 * @param	string $path						The path to the directory.
	 * @param	string[optional] $includeRegexp		A regular expresion that filters the files that should be included in the list.
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
		$files = array();

		// directory exists
		if(SpoonDirectory::exists($path))
		{
			// attempt to open directory
			if($directory = @opendir($path))
			{
				// start reading
				while((($file = readdir($directory)) !== false))
				{
					// no '.' and '..' and it's a file
					if(($file != '.') && ($file != '..') && is_file($path .'/'. $file))
					{
						// is there a include-pattern?
						if($includeRegexp !== null)
						{
							// init var
							$matches = array();

							// is this a match?
							if(preg_match($includeRegexp, $file, $matches) != 0) $files[] = $file;
						}

						// no excludes defined
						else $files[] = $file;
					}
				}
			}

			// close directory
			@closedir($directory);
		}

		// directory doesn't exist or a problem occured
		return $files;
	}


	/**
	 * Move/rename a directory/file.
	 *
	 * @return	bool						True if the file was moved or renamed, false if not.
	 * @param	string $source				Path of the source file.
	 * @param	string $destination			Path of the destination.
	 * @param 	bool[optional] $overwrite	Should an existing file be overwritten?
	 * @param	int[optional] $chmod		Chmod mode that should be applied on the file/directory.
	 */
	public static function move($source, $destination, $overwrite = true, $chmod = 0777)
	{
		// this is just an alias for SpoonDirectory::move
		return SpoonDirectory::move($source, $destination, $overwrite, $chmod);
	}


	/**
	 * Writes a string to a file.
	 *
	 * @return	bool						True if the content was written, false if not.
	 * @param	string $filename			The path of the file.
	 * @param	string $content				The content that should be written.
	 * @param	bool[optional] $createFile	Should the file be created if it doesn't exists?
	 * @param	bool[optional] $append		Should the content be appended if the file already exists?
	 * @param	int[optional] $chmod		Mode that should be applied on the file.
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