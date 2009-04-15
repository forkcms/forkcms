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

/** SpoonFile class */
require_once 'spoon/filesystem/file.php';

/** SpoonFilter class */
require_once 'spoon/filter/filter.php';


/**
 * Most of the functions you can apply on folders.
 *
 * @package			filesystem
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			0.1.1
 */
final class SpoonDirectory
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
				SpoonFile::chmod($destination, $chmod);
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
			// has subdirectories/files
			if(count(self::getList($directory, true)) != 0)
			{
				// loop files and delete
				foreach((array) SpoonFile::getList($directory) as $file) SpoonFile::delete($directory .'/'. $file);

				// loop directories and execute function
				foreach((array) self::getList($directory) as $folder) self::delete($directory .'/'. $folder);
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
							if($showFiles) $aDirectories[] = $file;
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
	 * @param	bool[optional] $subdirectores
	 */
	public static function getSize($path, $subdirectores = true)
	{
		// internal size
		$size = 0;

		// redefine arguments
		$path = (string) $path;
		$subdirectores = (bool) $subdirectores;

		// directory doesn't exists
		if(!self::exists($path)) return false;

		// directory exists
		else
		{
			// has files
			if(count(SpoonFile::getList($path) != 0))
			{
				// loop files
				foreach (SpoonFile::getList($path) as $file) $size += filesize($path .'/'. $file);
			}

			// check subdirectoryies?
			if($subdirectores)
			{
				// has subdirectories
				if(count(self::getList($path)) != 0)
				{
					// loop subdirectories
					foreach (self::getList($path) as $directory) $size += self::getSize($path .'/'. $directory, true);
				}
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
	 * @param	bool[optional] $strict
	 * @param	int[optional] $chmod
	 */
	public static function move($source, $destination, $overwrite = true, $strict = true, $chmod = 0777)
	{
		// redefine vars
		$source = (string) $source;
		$destination = (string) $destination;
		$overwrite = (bool) $overwrite;

		// validation
		if($strict)
		{
			if(!file_exists($source)) throw new SpoonFileSystemException('The given path ('. $source .') doesn\'t exists.');
			if(!$overwrite && file_exists($destination)) throw new SpoonFileSystemException('The given path ('. $destination .') already exists.');
		}

		// delete file
		if($overwrite && file_exists($destination)) self::delete($destination);

		// rename
		$return = @rename($source, $destination);
		SpoonFile::chmod($destination, $chmod);

		// return
		return $return;
	}

}

?>