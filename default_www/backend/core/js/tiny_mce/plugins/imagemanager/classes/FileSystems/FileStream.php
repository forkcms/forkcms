<?php
/**
 * $Id: FileStream.php 10 2007-05-27 10:55:12Z spocke $
 *
 * @package MCFileManager.filesystems
 * @author Moxiecode
 * @copyright Copyright © 2005, Moxiecode Systems AB, All rights reserved.
 */

/**
 * This class is the base class for file streams and is to be extended by all FileSystem implementations.
 *
 * @package MCManager.filesystems
 */
class Moxiecode_FileStream {
	/**
	 * Skip/jump over specified number of bytes from stream.
	 *
	 * @param int $bytes Number of bytes to skip.
	 * @return int Number of skipped bytes.
	 */
	function skip($bytes) {
	}

	/**
	 * Reads the specified number of bytes and returns an string with data.
	 *
	 * @param int $len Number of bytes to read.
	 * @return string Data read from stream or null if it's at the end of stream.
	 */
	function read($len) {
	}

	/**
	 * Reads all data avaliable in a stream and returns it as a string.
	 *
	 * @return string All data read from stream.
	 */
	function readToEnd() {
	}

	/**
	 * Writes a string to a stream.
	 *
	 * @param string $buff String buffer to write to file.
	 * @param int $len Number of bytes from string to write.
	 */
	function write($buff, $len = -1) {
	}

	/**
	 * Flush buffered data out to stream.
	 */
	function flush() {
	}

	/**
	 * Closes the specified stream. This will first flush the stream before closing.
	 */
	function close() {
	}
}

?>