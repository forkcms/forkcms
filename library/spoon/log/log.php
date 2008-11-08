<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package			log
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			1.0.0
 */


/** Spoon class */
require_once 'spoon/spoon.php';

/** SpoonLogException class */
require_once 'spoon/log/exception.php';

/** SpoonFile class */
require_once 'spoon/filesystem/file.php';


/**
 * This base class provides methods used to log data.
 *
 * @package			log
 *
 *
 * @author			Tijs Verkoyen <tijs@spoon-library.be>
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			1.0.0
 */
final class SpoonLog
{
	const MAX_FILE_SIZE = 10;


	/**
	 * File size
	 *
	 * @var	int
	 */
	private static $customFileSize;


	/**
	 * File size
	 *
	 * @var	int
	 */
	private static $errorFileSize;


	/**
	 * Log path
	 *
	 * @var	string
	 */
	private static $logPath;


	/**
	 * Get the current file size
	 *
	 * @return	int
	 * @param	string $file
	 */
	private static function getCurrentFileSize($file, $type)
	{
		// set type
		$type = SpoonFilter::getValue($type, array('custom', 'error'), 'error');

		// error
		if($type == 'error')
		{
			if(self::$errorFileSize) return self::$errorFileSize;
			return self::$errorFileSize = (int) @filesize($file);
		}

		// custom
		if(self::$customFileSize) return self::$customFileSize;
		return self::$customFileSize = (int) @filesize($file);
	}


	/**
	 * Get the log path
	 *
	 * @return	string
	 */
	public static function getPath()
	{
		if(self::$logPath === null) return (string) str_replace('/spoon/log/log.php', '', __FILE__);
		return self::$logPath;
	}


	/**
	 * Set the logpath
	 *
	 * @return	void
	 * @param	string $path
	 */
	public static function setPath($path)
	{
		self::$logPath = (string) $path;
	}


	/**
	 * Write an error
	 *
	 * @return	void
	 * @param	string $message
	 * @param	string[optional] $type
	 */
	public static function write($message, $type = 'error')
	{
		// redefine var
		$message = date('Y-m-d H:i:s') .' | '. $message . "\n";
		$type = SpoonFilter::getValue($type, array('error', 'custom'), 'error');

		// file
		$file = self::getPath() .'/'. $type .'.log';

		// get filesize
		$fileSize = self::getCurrentFileSize($file, $type);

		// rename if needed
		if($fileSize >= (self::MAX_FILE_SIZE * 1024))
		{
			// start new log file
			SpoonFile::move($file, $file .'.'. date('Ymdhis'));
		}

		// write content
		SpoonFile::setFileContent($file, $message, true);
	}
}

?>