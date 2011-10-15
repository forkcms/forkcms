<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package		spoon
 * @subpackage	log
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		1.0.0
 */


/**
 * This base class provides methods used to log data.
 *
 * @package		spoon
 * @subpackage	log
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		1.0.0
 */
class SpoonLog
{
	/**
	 * Maximum log size expressed in megabytes.
	 *
	 * @var	int
	 */
	private $maxLogSize = 10;


	/**
	 * Location for the log file.
	 *
	 * @var	string
	 */
	private $path;


	/**
	 * Log type, can be determined freely.
	 *
	 * @var	string
	 */
	private $type;


	/**
	 * Class constructor.
	 *
	 * @return	SpoonLog
	 * @param	string[optional] $type	This variable will be used as the name for the logfile.
	 * @param	string[optional] $path	The location of the logfile(s).
	 */
	public function __construct($type = 'custom', $path = null)
	{
		$this->setPath($path);
		$this->setType($type);
	}


	/**
	 * Fetch the maximum log size, expressed in megabyte.
	 *
	 * @return	int
	 */
	public function getMaxLogSize()
	{
		return $this->maxLogSize;
	}


	/**
	 * Fetch the logfile location.
	 *
	 * @return	string
	 */
	public function getPath()
	{
		return $this->path;
	}


	/**
	 * Fetch the log type.
	 *
	 * @return	string
	 */
	public function getType()
	{
		return $this->type;
	}


	/**
	 * Archive the current log file, but only if it exists.
	 *
	 * @return	SpoonLog
	 */
	public function rotate()
	{
		// file
		$file = $this->getPath() . '/' . $this->type . '.log';

		// rename file
		if(SpoonFile::exists($file)) SpoonDirectory::move($file, $file . '.' . date('Ymdhis'));

		// self
		return $this;
	}


	/**
	 * Set the log maximum filesize before rotation occurs.
	 *
	 * @return	SpoonLog
	 * @param	int $value		The maximum log size expressed in megabytes, which needs to be 1 or more.
	 */
	public function setMaxLogSize($value)
	{
		$this->maxLogSize = ((int) $value >= 1) ? (int) $value : $this->maxLogSize;
		return $this;
	}


	/**
	 * Set the location where to save the logfile.
	 *
	 * @return	SpoonLog
	 * @param	string[optional] $path		The path where you want to store the logfile. If null it will be saved in 'spoon/log/*'.
	 */
	public function setPath($path = null)
	{
		$this->path = ($path !== null) ? (string) $path : realpath(dirname(__FILE__));
		return $this;
	}


	/**
	 * Sets the type, which will be used as the name for the logfile.
	 *
	 * @return	SpoonLog
	 * @param	string $value		The value for this type. Only a-z, 0-9, underscores and hyphens are allowed.
	 */
	public function setType($value)
	{
		// redefine
		$value = (string) $value;

		// check for invalid characters
		if(!SpoonFilter::isValidAgainstRegexp('/(^[a-z0-9\-_]+$)/i', $value)) throw new SpoonLogException('The log type should only contain a-z, 0-9, underscores and hyphens. Your value "' . $value . '" is invalid.');

		// set type & return object
		$this->type = $value;
		return $this;
	}


	/**
	 * Write a message to the log.
	 *
	 * @return	SpoonLog
	 * @param	string $message		the message that should be logged.
	 */
	public function write($message)
	{
		// milliseconds
		list($milliseconds) = explode(' ', microtime());
		$milliseconds = round($milliseconds * 1000, 0);

		// redefine var
		$message = date('Y-m-d H:i:s') . ' ' . $milliseconds . 'ms | ' . $message . PHP_EOL;

		// file
		$file = $this->getPath() . '/' . $this->type . '.log';

		// rename if needed
		if(SpoonFile::exists($file) && (int) @filesize($file) >= ($this->maxLogSize * 1024 * 1024))
		{
			// start new log file
			$this->rotate();
		}

		// write content
		SpoonFile::setContent($file, $message, true, true);

		// self
		return $this;
	}
}


/**
 * This exception is used to handle log related exceptions.
 *
 * @package		spoon
 * @subpackage	log
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		1.0.0
 */
class SpoonLogException extends SpoonException {}
