<?php
/**
 * $Id: ZipFile.class.php 757 2009-11-26 16:23:49Z spocke $
 *
 * @licence GPL
 * @author Moxiecode
 * @copyright Copyright © 2003-2008, Moxiecode Systems AB, All rights reserved.
 */

/**
 * Zip file class this class is used to zip/unzip files and directories.
 *
 * Example of usage:
 * <pre>
 * // Create a zip file and add a file and directory to it
 * $zip = new Moxiecode_ZipFile("test.zip");
 * $zip->open();
 * $zip->addFile("/mydir/myfile.txt", "c:/mylocalfile.txt");
 * $zip->addDir("/mydir", "c:/mylocaldir");
 * $zip->commit();
 * $zip->close();
 *
 * // Extract the zip file.
 * $zip = new Moxiecode_ZipFile("test.zip");
 * $zip->open();
 * $zip->extract('/', "c:/mylocaldir");
 * $zip->close();
 * </pre>
 *
 * @package MoxieZip
 */
class Moxiecode_ZipFile {
	/**#@+
	 * @access private
	 */

	var $_entries;
	var $_path;
	var $_fp;
	var $_offset;
	var $_size;
	var $_header;
	var $_entryLookup;
	var $_compressionLevel;

	/**#@-*/

	/**
	 * Constructor for the ZipFile.
	 *
	 * @param string $path Path to zip file. For example: mydir/test.zip
	 */
	function Moxiecode_ZipFile($path) {
		$header = array();
		$header['disk'] = 0;
		$header['disks'] = 0;
		$header['comment'] = 0;
		$header['comment_len'] = 0;

		$this->_path = $path;
		$this->_header = $header;
		$this->_entryLookup = array();
		$this->_compressionLevel = 5;
	}

	/**
	 * Sets the compression level 0-9. Defaults to 5.
	 *
	 * @param int $level Compression level.
	 */
	function setCompressionLevel($level) {
		$this->_compressionLevel = $level;
	}

	/**
	 * Sets the comment contents of the whole zip file.
	 *
	 * @param string $comment Comment to set on Zip file.
	 */
	function setComment($comment) {
		$this->_header['comment'] = $comment;
		$this->_header['comment_len'] = strlen($comment);
	}

	/**
	 * Adds a string of data as a specific file inside the zip.
	 *
	 * @param string $zip_path File path inside the zip to store the data in for example /mydir/myfile.txt.
	 * @param string $data Data to store inside the file.
	 * @param string $comment Optional comment to add to the file inside the zip.
	 */
	function addData($zip_path, $data, $comment = "") {
		$zip_path = preg_replace('/^\//', '', $zip_path);

		$this->deleteFile($zip_path);

		$entry = new Moxiecode_ZipEntry($this);

		$entry->setPath($zip_path);
		$entry->setData($data);
		$entry->setComment($comment);

		$this->buildPath($entry->getPath());
		$this->addEntry($entry);
	}

	/**
	 * Adds a local file to the zip file.
	 *
	 * @param string $zip_path Path to store the local file as inside the zip. For example /mydir/myfile.txt.
	 * @param string $path Local file path to store inside the zip for example c:/myfile.txt
	 * @param string $comment Optional comment to add to the file inside the zip.
	 */
	function addFile($zip_path, $path = "", $comment = "") {
		$zip_path = preg_replace('/^\//', '', $zip_path);

		$this->deleteFile($zip_path);

		$entry = new Moxiecode_ZipEntry($this);

		if ($path != '')
			$entry->setLocalPath($path);

		$entry->setPath($zip_path);
		$entry->setComment($comment);

		$this->buildPath($entry->getPath());
		$this->addEntry($entry);
	}

	/**
	 * Adds a directory inside the zip file. This can be an empty directory or a local directory that
	 * is to be added to the zip.
	 *
	 * @param string $zip_path Path to store the directory as inside the zip. For example /mydir/dir.
	 * @param string $path Optional local file path for example c:/mydir.
	 * @param string $comment Optional comment to add for directory inside the zip file.
	 * @param bool $recursive Optional state to do a recursive add or not this is true by default.
	 */
	function addDirectory($zip_path, $path = "", $comment = "", $recursive = true) {
		$zip_path = preg_replace('/^\//', '', $zip_path);

		$zip_path = $this->_addTrailingSlash($zip_path);

		$this->deleteDir($zip_path);

		if ($path) {
			$path = realpath($path);
			$files = $this->_listTree($path, $recursive);

			foreach ($files as $file) {
				$entry = new Moxiecode_ZipEntry($this);

				$entry->setPath($zip_path . substr($file, strlen($path) + 1));
				$entry->setLocalPath($file);
				$entry->setComment($comment);
				$entry->setType(is_dir($file));

				$this->addEntry($entry);
				$this->buildPath($entry->getPath(), is_dir($file));
			}

			$this->buildPath($zip_path . substr($file, strlen($path) + 1), is_dir($file));
		} else {
			$entry = new Moxiecode_ZipEntry($this);

			$entry->setPath($zip_path);
			$entry->setComment($comment);
			$entry->setType(1);

			$this->addEntry($entry);
			$this->buildPath($entry->getPath());
		}
	}

	/**
	 * Moves/renames an entry inside the zip file.
	 *
	 * @param string $from_path Path to move the file/directory from.
	 * @param string $to_oath Path to move the file/directory to.
	 */
	function moveEntry($from_path, $to_path) {
		$from_path = preg_replace('/^\//', '', $from_path);
		$to_path = preg_replace('/^\//', '', $to_path);

		$entry = $this->getEntryByPath($from_path);

		if ($entry->isFile()) {
			unset($this->_entryLookup[$entry->getPath()]);
			$entry->setPath($to_path);
			$this->_entryLookup[$to_path] = $entry;
			$this->buildPath($to_path);
		} else {
			$from_path = preg_replace('/\/$/', '', $from_path) . '/';
			$to_path = preg_replace('/\/$/', '', $to_path) . '/';

			$newEntries = array();

			for ($i=0; $i<count($this->_entries); $i++) {
				$entry = $this->_entries[$i];

				// Move all entries
				if (strpos($entry->getPath(), $from_path) === 0) {
					unset($this->_entryLookup[$entry->getPath()]);
					$entry->setPath($to_path . substr($entry->getPath(), strlen($from_path)));
					$this->_entryLookup[$entry->getPath()] = $entry;
				}
			}

			$this->buildPath($to_path);
		}
	}

	/**
	 * Adds a new entry to the zip. An entry is an instance of the Moxiecode_ZipEntry class.
	 *
	 * @param Moxiecode_ZipEntry $entry Entry to add to zip file structure.
	 */
	function addEntry(&$entry) {
		if ($entry) {
			$this->_entries[] = $entry;
			$this->_entryLookup[$entry->getPath()] = $entry;
		}
	}

	/**
	 * Builds a path inside the zip. This method will be executed by addFile/addDirectory to
	 * build a complete structure inside the zip if a file like /a/b/c/d.txt would be added.
	 *
	 * @param string $path Path inside the zip file to build.
	 * @param bool $is_dir Optional is it a directory or an file defaults to false.
	 */
	function buildPath($path, $is_dir = false) {
		$pos = strrpos($path, '/');

		if ($pos === false)
			return;

		$path = substr($path, 0, $pos);

		// Check for dir
		$entry = $this->getEntryByPath($path);
		if ($entry)
			return;

		// Dir not found create all parents
		$items = explode('/', $path);
		$path = "";

		if ($is_dir)
			array_pop($items);

		foreach ($items as $item) {
			$path .= $item . '/';

			// Look for entry
			$entry = $this->getEntryByPath($path);

			if (!$entry) {
				$entry = new Moxiecode_ZipEntry($this);

				$entry->setPath($path);
				$this->addEntry($entry);
			}
		}
	}

	/**
	 * Returns an entry instance by a specific path.
	 *
	 * @param string $path Path inside the zip to retrive.
	 * @return Moxiecode_ZipEntry Zip entry instance for the specified zip path or null.
	 */
	function &getEntryByPath($path) {
		$path = preg_replace('/^\//', '', $path);

		if (isset($this->_entryLookup[$path]))
			return $this->_entryLookup[$path];

		for ($i=0; $i<count($this->_entries); $i++) {
			$entry = $this->_entries[$i];

			if ($entry->getPath() == $path || $entry->getPath() == $path . '/') {
				$this->_entryLookup[$path] = $entry;
				return $entry;
			}
		}

		$obj = null; // Stupid PHP 5 notices

		return $obj;
	}

	/**
	 * Deletes a file inside the zip.
	 *
	 * @param string $path Path inside the zip to delete for example. /mydir/myfile.txt.
	 * @return bool state if the file was deleted or not.
	 */
	function deleteFile($path) {
		$path = preg_replace('/^\//', '', $path);
		$newEntries = array();
		$deleted = false;

		// No dir, no file = no need
		if (!isset($this->_entryLookup[$path]) && !isset($this->_entryLookup[$path . '/']))
			return;

		for ($i=0; $i<count($this->_entries); $i++) {
			$entry = $this->_entries[$i];

			if ($entry->getPath() != $path)
				$newEntries[] = $entry;
			else {
				unset($this->_entryLookup[$path]);
				$deleted = true;
			}
		}

		$this->_entries = $newEntries;

		return $deleted;
	}

	/**
	 * Deletes the specified directory inside the zip.
	 *
	 * @param string $path Path inside zip file to delete. For example: /mydir/mydir2.
	 * @param bool $deep Optional state if it should delete the directory recursive.
	 * @return bool state if the directory was deleted or not.
	 */
	function deleteDir($path, $deep = true) {
		$path = preg_replace('/^\//', '', $path);
		$path = $this->_addTrailingSlash($path);
		$deleted = false;

		// No dir, no file = no need
		if (!isset($this->_entryLookup[$path]) && !isset($this->_entryLookup[$path . '/']))
			return;

		if (!$deep) {
			for ($i=0; $i<count($this->_entries); $i++) {
				$entry = $this->_entries[$i];

				if (strpos($entry->getPath(), $path) !== 0 && strlen($entry->getPath()) > strlen($path))
					return false;
			}
		}

		$newEntries = array();

		for ($i=0; $i<count($this->_entries); $i++) {
			$entry = $this->_entries[$i];

			if (strpos($entry->getPath(), $path) !== 0)
				$newEntries[] = $entry;
			else {
				$deleted = true;
				unset($this->_entryLookup[$path]);
			}
		}

		$this->_entries = $newEntries;

		return $deleted;
	}

	/**
	 * Commits/stores the zip file to a specific path or the path specified in contructor.
	 *
	 * @param string $path Optional local file system path to store the zip to.
	 * @return bool true/false state if the zip was stored or not.
	 */
	function commit($path = '') {
		$paths = array();
		$status = false;

		// Get entry paths
		for ($i=0; $i<count($this->_entries); $i++)
			$paths[] = $this->_entries[$i]->getPath();

		// Sort entry paths
		sort($paths);

		$tmpFile = false;

		if (!$path)
			$path = $this->_path;

		$this->_size = 0;
		$this->_offset = 0;

		// If zip is the same and open use tmp file
		if ($this->_fp && $path == $this->_path) {
			$tmpFile = true;
			$path = $path . ".tmp";
		}

		// Write output file
		if (file_exists($path))
			@unlink($path);

		$fp = @fopen($path, 'wb');
		if ($fp) {
			for ($i=0; $i<count($paths); $i++) {
				$entry = $this->getEntryByPath($paths[$i]);
				$this->_writeLocalFileHeader($fp, $entry);
			}

			for ($i=0; $i<count($paths); $i++) {
				$entry = $this->getEntryByPath($paths[$i]);
				$this->_writeCentralDirHeader($fp, $entry);
			}

			$this->_writeCentralDirEnd($fp);

			@fclose($fp);
			$status = true;
		}

		// If zip is the same and open use tmp file
		if ($tmpFile) {
			$this->close();

			if (file_exists($this->_path))
				@unlink($this->_path);

			@rename($this->_toOSPath($path), $this->_toOSPath($this->_path));
		}

		return $status;
	}

	/**
	 * Opens the zip file for read or write access.
	 */
	function open() {
		if (!$this->_fp) {
			// Load zip
			if (!file_exists($this->_path))
				return;

			$this->_fp = @fopen($this->_path, "rb");

			if ($this->_fp) {
				// Parse local file headers
				while ($header = $this->_readLocalFileHeader()) {
					/*echo "Local file header:\n";
					var_dump($header);*/

					$entry = new Moxiecode_ZipEntry($this, $header);
					$this->_entries[] = $entry;
					$this->_entryLookup[$entry->getPath()] = $entry;
				}

				// Parse central dir headers
				while ($header = $this->_readCentralDirHeader()) {
					/*echo "Central dir header:\n";
					var_dump($header);*/

					// Append to existing headers
					for ($i=0; $i<count($this->_entries); $i++) {
						$entry = $this->_entries[$i];

						if ($entry->getPath() == $header['filename'])
							$entry->_addHeader($header);
					}
				}

				// Parse central dir end header
				if ($header = $this->_readCentralDirEnd()) {
					/*echo "Central dir end:\n";
					var_dump($header);*/

					$this->_setHeader($header);
				}
			}
		}
	}

	/**
	 * Closes the zip file.
	 */
	function close() {
		if ($this->_fp) {
			@fclose($this->_fp);
			$this->_fp = null;
		}
	}

	/**
	 * Extract the specified zip path as a local path.
	 *
	 * @param string $zip_path Location inside the zip to extract for example: /mydir/myfile or / for the whole zip.
	 * @param string $path Local file system path to unpack the zip to.
	 * @param bool $is_target Optional state if the contents should be unpacks inside the specified path or as the specified path.
	 */
	function extract($zip_path, $path, $is_target = false) {
		$path = $this->_toUnixPath($path);

		// Extract single file
		$entry = $this->getEntryByPath($zip_path);
		if ($entry && $entry->isFile()) {
			if ($is_target)
				$this->_extractEntry($entry, $path);
			else
				$this->_extractEntry($entry, $path . "/" . $entry->getName());

			return;
		}

		// Extract files
		$entries = $this->listEntries($zip_path, true);
		for ($i=0; $i<count($entries); $i++) {
			$entry = $entries[$i];

			if ($is_target)
				$outPath = $this->_addTrailingSlash($path) . preg_replace('/^([^\/]+\/)/', '', $entries[$i]->getPath());
			else
				$outPath = $this->_addTrailingSlash($path) . $entries[$i]->getPath();

			if ($entry->isDirectory())
				$this->_mkdirs($this->_toOSPath($outPath));
			else
				$this->_extractEntry($entry, $outPath);
		}
	}

	/**
	 * Returns all entries inside the zip file as an array.
	 *
	 * @return Array Array of all zip entries.
	 */
	function getEntries() {
		$this->open();

		return $this->_entries;
	}

	/**
	 * Lists entires inside the zip file.
	 *
	 * @param string $path Zip path to list files inside. For example: /mydir.
	 * @param bool $deep List files recursive or not. Defaults to false.
	 * @return Array Array of zip entries.
	 */
	function &listEntries($path = '/', $deep = false) {
		$path = preg_replace('/^\//', '', $path);
		$path = $this->_addTrailingSlash($path);

		if ($path == '')
			$path = '/';

		$slashCount = substr_count($path, '/');
		$entries = $this->getEntries();
		$output = array();

		for ($i=0; $i<count($this->_entries); $i++) {
			$entry = $this->_entries[$i];
			$entryPath = $entry->getPath();
			$entryPath = preg_replace('/\/$/', '', $entryPath);

			if (!$deep) {
				if ($path == '/' && substr_count($entryPath, '/') == 0)
					$output[] = $entry;
				else if (strpos($entryPath, $path) === 0 && substr_count($entryPath, '/') == $slashCount)
					$output[] = $entry;
			} else {
				if ($path == '/' || strpos($entryPath, $path) === 0)
					$output[] = $entry;
			}
		}

		return $output;
	}

	/**#@+
	 * @access private
	 */

	function _setHeader($header) {
		$this->_header;
	}

	function _getHeader() {
		return $this->_header;
	}

	function _extractEntry(&$entry, $path) {
		// Make parent dir
		$ar = explode('/', $path);
		array_pop($ar);	
		$this->_mkdirs(implode('/', $ar));

		// Extract file contents
		$fp = @fopen($path, "wb");
		if ($fp) {
			fwrite($fp, $entry->getData());
			fclose($fp);
		}
	}

	function _writeLocalFileHeader($fp, &$entry) {
		$header = $entry->_getHeader();
		$data = '';

		// Compress data and set some headers
		$header['filename_len'] = strlen($entry->getPath());

		// Convert unix time to dos time
		$date = getdate($header['unixtime']);
		$header['mtime'] = ($date['hours'] << 11) + ($date['minutes'] << 5) + $date['seconds'] / 2;
		$header['mdate'] = (($date['year'] - 1980) << 9) + ($date['mon'] << 5) + $date['mday'];

		// Total commander has strange issues
		if ($header['size'] == 0)
			$header['compressed_size'] = 0;

		// Data to compress
		if ($header['size'] > 0 || ($entry->_isDirty && $entry->isFile())) {
			if ($entry->_isDirty) {
				$data = $entry->getData();
				$header['size'] = strlen($data);
				$header['crc'] = crc32($data);
				$header['compression'] = 0x0008; // deflate
				$header['flag'] = 0x0002;

				// Compress
				$data = @gzdeflate($data, $this->_compressionLevel);
				$header['compressed_size'] = strlen($data);
			} else
				$data = $entry->getRawData();
		}

		// Pack and write header
		fwrite($fp, pack("VvvvvvVVVvv",
			0x04034b50, // Signature
			$header['version'],
			$header['flag'],
			$header['compression'],
			$header['mtime'],
			$header['mdate'],
			$header['crc'],
			$header['compressed_size'],
			$header['size'],
			$header['filename_len'],
			$header['extra_len']
		), 30);

		// Write filename and compressed data
		fwrite($fp, $entry->getPath(), $header['filename_len']);

		if (isset($header['extra']))
			fwrite($fp, $header['extra'], $header['extra_len']);

		fwrite($fp, $data, $header['compressed_size']);

		$header['offset'] = $this->_offset;
		$this->_offset += 30 + $header['filename_len'] + $header['extra_len'] + $header['compressed_size'];

		$entry->_setHeader($header);
	}

	function _writeCentralDirHeader($fp, &$entry) {
		$header = $entry->_getHeader();

		// Set extra parameters
		$header['version'] = 0x0014;
		$header['version_extracted'] = $header['compression'] == 8 ? 0x0014 : 0x000A;
		$header['disk'] = 0x0000;
		$header['iattr'] = 0x0001;
		$header['eattr'] = $entry->isDirectory() ? 0x00000010 : 0x00000020;

		// Write central directory record
		fwrite($fp, pack("VvvvvvvVVVvvvvvVV",
			0x02014b50, // Signature
			$header['version'],
			$header['version_extracted'],
			$header['flag'],
			$header['compression'],
			$header['mtime'],
			$header['mdate'],
			$header['crc'],
			$header['compressed_size'],
			$header['size'],
			$header['filename_len'],
			$header['extra_len'],
			$header['comment_len'],
			$header['disk'],
			$header['iattr'],
			$header['eattr'],
			$header['offset']
		), 46);

		// Write filename
		fwrite($fp, $entry->getPath(), $header['filename_len']);

		if (isset($header['extra']))
			fwrite($fp, $header['extra'], $header['extra_len']);

		if (isset($header['comment']))
			fwrite($fp, $header['comment'], $header['comment_len']);

		$this->_size += 46 + $header['filename_len'] + $header['extra_len'] + $header['comment_len'];
	}

	function _writeCentralDirEnd($fp) {
		$header = $this->_header;

		$header['start'] = count($this->_entries);
		$header['entries'] = count($this->_entries);
		$header['size'] = $this->_size;
		$header['offset'] = $this->_offset;

		// Write end of central directory record
		fwrite($fp, pack("VvvvvVVv",
			0x06054b50, // Signature
			$header['disk'],
			$header['disks'],
			$header['start'],
			$header['entries'],
			$header['size'],
			$header['offset'],
			$header['comment_len']
		), 22);

		fwrite($fp, $header['comment'], $header['comment_len']);
	}

	function _readLocalFileHeader() {
		$header = array();

		// Read signature
		$oldPos = ftell($this->_fp);
		$buff = @fread($this->_fp, 4);
		$data = unpack('Vsignature', $buff);

		// Is not local file header
		if ($data['signature'] != 0x04034b50) {
			fseek($this->_fp, $oldPos, SEEK_SET);
			return null;
		}

		// Read header
		$buff = fread($this->_fp, 26);
		$data = unpack('vversion/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len', $buff);
		$header = array_merge($data, $header);

		// Read filename
		if ($header['filename_len'] > 0)
			$header['filename'] = fread($this->_fp, $header['filename_len']);

		// Read extra
		if ($header['extra_len'] != 0)
			$header['extra'] = fread($this->_fp, $header['extra_len']);

		// Convert DOS date/time to UNIX Timestamp
		if ($header['mdate'] && $header['mtime']) {
			$header['unixtime'] = gmmktime(($header['mtime'] & 0xF800) >> 11, ($header['mtime'] & 0x07E0) >> 5, ($header['mtime'] & 0x001F) * 2,
											($header['mdate'] & 0x01E0) >> 5, $header['mdate'] & 0x001F, (($header['mdate'] & 0xFE00) >> 9) + 1980);
		}

		// Store away data offset and jump behind data
		$header['data_offset'] = ftell($this->_fp);
		fseek($this->_fp, $header['compressed_size'], SEEK_CUR);

		return $header;
	}

	function _readCentralDirHeader() {
		$header = array();

		// Read signature
		$oldPos = ftell($this->_fp);
		$buff = @fread($this->_fp, 4);
		$data = unpack('Vsignature', $buff);

		// Is not central dir header
		if ($data['signature'] != 0x02014B50) {
			fseek($this->_fp, $oldPos);
			return null;
		}

		// Read header
		$buff = fread($this->_fp, 42);
		$data = unpack('vversion/vversion_extracted/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len/vcomment_len/vdisk/viattr/Veattr/Voffset', $buff);
		$header = array_merge($data, $header);

		// Read filename
		if ($header['filename_len'] != 0)
			$header['filename'] = fread($this->_fp, $header['filename_len']);

		// Read extra
		if ($header['extra_len'] != 0)
			$header['extra'] = fread($this->_fp, $header['extra_len']);

		// Read comment
		if ($header['comment_len'] != 0)
			$header['comment'] = fread($this->_fp, $header['comment_len']);

		// Convert DOS date/time to UNIX Timestamp
		if ($header['mdate'] && $header['mtime']) {
			$header['unixtime'] = gmmktime(($header['mtime'] & 0xF800) >> 11, ($header['mtime'] & 0x07E0) >> 5, ($header['mtime'] & 0x001F) * 2,
											($header['mdate'] & 0x01E0) >> 5, $header['mdate'] & 0x001F, (($header['mdate'] & 0xFE00) >> 9) + 1980);
		}

		return $header;
	}

	function _readCentralDirEnd() {
		$header = array();

		// Read signature
		$oldPos = ftell($this->_fp);
		$buff = @fread($this->_fp, 4);
		$data = unpack('Vsignature', $buff);

		// Is not central dir header
		if ($data['signature'] != 0x06054b50) {
			fseek($this->_fp, $oldPos);
			return null;
		}

		// Read header
		$buff = fread($this->_fp, 22);
		$data = unpack('vdisk/vdisks/vstart/ventries/Vsize/Voffset/vcomment_len', $buff);
		$header = array_merge($data, $header);

		// Read comment
		if ($header['comment_len'] > 0)
			$header['comment'] = fread($this->_fp, $header['comment_len']);

		return $header;
	}

	function _getFileData($header, $uncompres = true) {
		$data = "";

		if ($this->_fp) {
			$oldPos = ftell($this->_fp);

			fseek($this->_fp, $header['data_offset']);

			$buff = @fread($this->_fp, $header['compressed_size']);
			if ($uncompres && $header['compression'] == 8 && $header['compressed_size'] > 0)
				$data = @gzinflate($buff);
			else
				$data = $buff;

			fseek($this->_fp, $oldPos);
		}

		return $data;
	}

	function _addTrailingSlash($path) {
		if (strlen($path) > 0 && $path[strlen($path)-1] != '/')
			$path .= '/';

		return $path;
	}

	function _removeTrailingSlash($path) {
		// Is root
		if ($path == "/")
			return $path;

		if ($path == "")
			return $path;

		if ($path[strlen($path)-1] == '/')
			$path = substr($path, 0, strlen($path)-1);

		return $path;
	}

	function _toUnixPath($path) {
		return str_replace(DIRECTORY_SEPARATOR, "/", $path);
	}

	function _toOSPath($path) {
		return str_replace("/", DIRECTORY_SEPARATOR, $path);
	}

	function _listTree($path, $recursive = true) {
		$files = array();

		if ($dir = opendir($path)) {
			while (false !== ($file = readdir($dir))) {
				if ($file == "." || $file == "..")
					continue;

				$file = $path . "/" . $file;
				$files[] = $file;

				if (is_dir($file) && $recursive)
					$files = array_merge($files, $this->_listTree($file, $recursive));
			}

			closedir($dir);
		}

		return $files;
	}

	function _mkdirs($path, $rights = 0777) {
		$path = preg_replace('/\/$/', '', $path);
		$dirs = array();

		// Figure out what needs to be created
		while ($path) {
			if (file_exists($path))
				break;

			$dirs[] = $path;
			$pathAr = explode("/", $path);
			array_pop($pathAr);
			$path = implode("/", $pathAr);
		}

		// Create the dirs
		$dirs = array_reverse($dirs);
		foreach ($dirs as $path) {
			if (!@is_dir($path) && strlen($path) > 0)
				mkdir($path, $rights);
		}
	}

	/**#@-*/
}

/**
 * Zip file entry this class represents a entry within the zip for example a file or a directory.
 *
 * Example of usage:
 * <pre>
 * // Create a zip file and add a file and directory to it
 * $zip = new Moxiecode_ZipFile("test.zip");
 * $zip->open();
 * $entries = $zip->listEntries('/', true);
 * foreach ($entries as $entry) {
 * 	echo $entry->getPath();
 * }
 * $zip->close();
 * </pre>
 *
 * @package MoxieZip
 */
class Moxiecode_ZipEntry {
	/**#@+
	 * @access private
	 */

	var $_zip;
	var $_header;
	var $_data;
	var $_rawData;
	var $_isDirty;
	var $_localPath;

	/**#@-*/

	/**
	 * Constructs a new zip entry.
	 *
	 * @param Moxiecode_ZipFile $zip Zip file to bind entry to.
	 * @param Array $header Optional header array.
	 */
	function Moxiecode_ZipEntry(&$zip, $header = false) {
		if (!$header) {
			$header = array();
			$header['version'] = 0x0014;
			$header['version_extracted'] = 0x0000;
			$header['flag'] = 0;
			$header['compression'] = 0;
			$header['mtime'] = 0;
			$header['mdate'] = 0;
			$header['crc'] = 0;
			$header['compressed_size'] = 0;
			$header['size'] = 0;
			$header['filename_len'] = 0;
			$header['extra_len'] = 0;
			$header['comment_len'] = 0;
			$header['disk'] = 0;
			$header['iattr'] = 0;
			$header['eattr'] = 0;
			$header['offset'] = 0;
			$header['filename'] = '';
			$header['extra'] = '';
			$header['comment'] = '';
			$header['unixtime'] = time();
		}

		$this->_zip = $zip;
		$this->_header = $header;
		$this->_isDirty = false;
	}

	/**
	 * Returns the raw uncompressed data inside the zip for the entry.
	 *
	 * @return string Raw data string.
	 */
	function getRawData() {
		if (!$this->_rawData)
			$this->_rawData = $this->_zip->_getFileData($this->_header, false);

		return $this->_rawData;
	}

	/**
	 * Sets the raw uncompressed data inside the zip of the entry.
	 *
	 * @param string $data Raw data string.
	 */
	function setRawData($data) {
		$this->_rawData = $data;
	}

	/**
	 * Returns the uncompressed data of the entry.
	 *
	 * @return string Uncompressed zip entry data.
	 */
	function getData() {
		if ($this->_localPath)
			$this->setData(file_get_contents($this->_localPath));

		if ($this->_data)
			return $this->_data;

		return $this->_zip->_getFileData($this->_header);
	}

	/**
	 * Sets the date for the zip entry.
	 *
	 * @param string $data Data to set inside zip entry.
	 */
	function setData($data) {
		$this->_header['unixtime'] = time();
		$this->_header['size'] = strlen($data);
		$this->_data = $data;
		$this->_isDirty = true;
	}

	/**
	 * Sets the local path for the entry. The specified file will be stored
	 * inside the zip for the specific entry.
	 *
	 * @param string $path Local file system path to store inside zip entry.
	 */
	function setLocalPath($path) {
		$this->_header['unixtime'] = filemtime($path);
		$this->_localPath = $path;
		$this->_isDirty = true;
	}

	/**
	 * Returns the local file system path for the entry.
	 *
	 * @return string Local file system path for the entry.
	 */
	function getLocalPath() {
		return $this->_localPath;
	}

	/**
	 * Returns the last modified time of the zip entry as a unix timestamp.
	 *
	 * @return int Last modified time of the zip entry as a unix timestamp.
	 */
	function getLastModified() {
		return $this->_header['unixtime'];
	}

	/**
	 * Sets the last modified time of the zip entry as a unix timestamp.
	 *
	 * @param int $date Last modified time of the zip entry as a unix timestamp.
	 */
	function setLastModified($date) {
		$this->_header['unixtime'] = $date;
	}

	/**
	 * Returns the internal zip path. For example /mydir/myfile.txt.
	 *
	 * @return string Internal zip path. For example /mydir/myfile.txt.
	 */
	function getPath() {
		return $this->_header['filename'];
	}

	/**
	 * Sets the internal zip path. For example /mydir/myfile.txt.
	 *
	 * @param string $path Internal zip path. For example /mydir/myfile.txt.
	 */
	function setPath($path) {
		$path = preg_replace('/^\//', '', $path);
		$path = $this->_zip->_toUnixPath($path);
		$this->_header['filename'] = $path;
		$this->_header['filename_len'] = strlen($path);
	}

	/**
	 * Sets the comment for the zip entry.
	 *
	 * @param string $comment Comment for the zip entry.
	 */
	function setComment($comment) {
		$this->_header['comment'] = $comment;
		$this->_header['comment_len'] = strlen($comment);
	}

	/**
	 * Gets the comment for the zip entry.
	 *
	 * @return string Comment for the zip entry.
	 */
	function getComment() {
		return $this->_header['comment'];
	}

	/**
	 * Sets the extra field for the zip entry.
	 *
	 * @param string $extra Extra field for the zip entry.
	 */
	function setExtra($extra) {
		$this->_header['extra'] = $extra;
		$this->_header['extra_len'] = strlen($extra);
	}

	/**
	 * Gets the extra field for the zip entry.
	 *
	 * @return string Extra field for the zip entry.
	 */
	function getExtra() {
		return $this->_header['extra'];
	}

	/**
	 * Returns the zip entry size in bytes.
	 *
	 * @return int Numner of bytes for the zip entry.
	 */
	function getSize() {
		return $this->_header['size'];
	}

	/**
	 * Returns the file name/directory name for the zip entry.
	 *
	 * @return string File name/directory name for the zip entry.
	 */
	function getName() {
		$ar = explode('/', $this->_zip->_removeTrailingSlash($this->_header['filename']));

		return array_pop($ar);
	}

	/**
	 * Sets the type for the entry. 1 equals directory 0 equals file.
	 *
	 * @param int $type Type for the entry. 1 equals directory 0 equals file.
	 */
	function setType($type) {
		if ($type == 1)
			$this->_header['filename'] = $this->_zip->_addTrailingSlash($this->_header['filename']);
	}

	/**
	 * Returns true/false if the entry is a file or not.
	 *
	 * @return bool true/false if the entry is a file or not.
	 */
	function isFile() {
		return !$this->isDirectory();
	}

	/**
	 * Returns true/false if the entry is a directory or not.
	 *
	 * @return bool true/false if the entry is a directory or not.
	 */
	function isDirectory() {
		return substr($this->getPath(), strlen($this->getPath()) - 1) == '/';
	}

	/**#@+
	 * @access private
	 */

	function _addHeader($header) {
		$this->_header = array_merge($this->_header, $header);
	}

	function _getHeader() {
		return $this->_header;
	}

	function _setHeader($header) {
		$this->_header = $header;
	}

	/**#@-*/
}

?>