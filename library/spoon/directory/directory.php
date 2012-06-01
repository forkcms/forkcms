<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package		spoon
 * @subpackage	directory
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */


/**
 * Most of the functions you can apply on folders.
 *
 * @package		spoon
 * @subpackage	directory
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @since		0.1.1
 */
class SpoonDirectory
{
	/**
	 * Copies a file/folder.
	 *
	 * @return	bool						True if the file/directory was copied, false if not.
	 * @param	string $source				The full path to the source file/folder.
	 * @param	string $destination			The full path to the destination.
	 * @param	bool[optional] $overwrite	If the destination already exists, should we overwrite?
	 * @param	bool[optional] $strict		If strict is true, exceptions will be thrown when an error occures.
	 * @param	int[optional] $chmod		Chmod mode that should be applied on the directory/file. Defaults to 0777 (+rwx for all) for directories and 0666 (+rw for all) for files.
	 */
	public static function copy($source, $destination, $overwrite = true, $strict = true, $chmod = null)
	{
		// redefine vars
		$source = (string) $source;
		$destination = (string) $destination;
		$return = true;
		if($chmod === null)
		{
			$chmod = is_dir($source) ? 0777 : 0666;
		}

		// validation
		if($strict)
		{
			if(!@file_exists($source)) throw new SpoonDirectoryException('The given path (' . $source . ') doesn\'t exist.');
			if(!$overwrite && @file_exists($destination)) throw new SpoonDirectoryException('The given path (' . $destination . ') already exists.');
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
					if($strict) throw new SpoonDirectoryException('The directory-structure couldn\'t be created.');
					return false;
				}
			}

			// get content
			$contentList = (array) self::getList($source, true);

			// loop content
			foreach($contentList as $item)
			{
				// copy dir (recursive)
				if(is_dir($source . '/' . $item)) self::copy($source . '/' . $item, $destination . '/' . $item);
				else
				{
					// delete the file if needed
					if($overwrite && SpoonFile::exists($destination . '/' . $item)) SpoonFile::delete($destination . '/' . $item);

					// copy file
					if(!SpoonFile::exists($destination . '/' . $item))
					{
						// copy file
						$return = @copy($source . '/' . $item, $destination . '/' . $item);

						// check
						if(!$return)
						{
							if($strict) throw new SpoonDirectoryException('The directory/file (' . $source . '/' . $item . ') couldn\'t be copied.');
							return false;
						}

						// chmod
						@chmod($destination . '/' . $item, $chmod);
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
					if($strict) throw new SpoonDirectoryException('The directory/file (' . $source . ') couldn\'t be copied.');
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
	 * Creates a folder with the given chmod settings.
	 *
	 * @return	bool						True if the directory was created, false if not.
	 * @param	string $directory			The name for the directory.
	 * @param	string[optional] $chmod		Mode that will be applied on the directory.
	 */
	public static function create($directory, $chmod = 0777)
	{
		return (!self::exists($directory)) ? @mkdir((string) $directory, $chmod, true) : true;
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
					if(is_dir($directory . '/' . $item)) self::delete($directory . '/' . $item);

					// delete file
					else SpoonFile::delete($directory . '/' . $item);
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
			if(!SpoonFilter::isValidRegexp($includeRegexp)) throw new SpoonDirectoryException('Invalid regular expression (' . $includeRegexp . ').');
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
						if(is_dir($path . '/' . $file))
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
				if(is_dir($path . '/' . $item) && $subdirectories) $size += self::getSize($path . '/' . $item, $subdirectories);

				// add filesize
				else $size += filesize($path . '/' . $item);
			}
		}

		return $size;
	}


	/**
	 * Check if a directory is writable.
	 * The default is_writable function has problems due to Windows ACLs "bug"
	 *
	 * @return	bool
	 * @param	string $path	The path to check.
	 */
	private static function isWritable($path)
	{
		// redefine argument
		$path = (string) $path;

		// create temporary file
		$file = tempnam($path, 'isWritable');

		// file has been created
		if($file !== false)
		{
			// remove temporary file
			SpoonFile::delete($file);

			// file could not be created = writable
			return true;
		}

		// file could not be created = not writable
		return false;
	}


	/**
	 * Move/rename a directory/file.
	 *
	 * @return	bool						True if the directory was moved or renamed, false if not.
	 * @param	string $source				Path of the source directory.
	 * @param	string $destination			Path of the destination.
	 * @param 	bool[optional] $overwrite	Should an existing directory be overwritten?
	 * @param	int[optional] $chmod		Chmod mode that should be applied on the directory/file. Defaults to 0777 (+rwx for all) for directories and 0666 (+rw for all) for files.
	 */
	public static function move($source, $destination, $overwrite = true, $chmod = null)
	{
		// redefine vars
		$source = (string) $source;
		$destination = (string) $destination;
		$overwrite = (bool) $overwrite;
		if($chmod === null)
		{
			$chmod = is_dir($source) ? 0777 : 0666;
		}

		// validation
		if(!file_exists($source)) throw new SpoonDirectoryException('The given path (' . $source . ') doesn\'t exist.');
		if(!$overwrite && file_exists($destination)) throw new SpoonDirectoryException('The given destination (' . $destination . ') already exists.');

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
 * This exception is used to handle directory related exceptions.
 *
 * @package		spoon
 * @subpackage	directory
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */
class SpoonDirectoryException extends SpoonException {}
