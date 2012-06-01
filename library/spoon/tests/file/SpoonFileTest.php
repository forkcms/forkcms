<?php

if(!defined('SPOON_CHARSET')) define('SPOON_CHARSET', 'utf-8');

$includePath = dirname(dirname(dirname(dirname(__FILE__))));
set_include_path(get_include_path() . PATH_SEPARATOR . $includePath);

require_once 'spoon/spoon.php';
require_once 'PHPUnit/Framework/TestCase.php';

class SpoonFileTest extends PHPUnit_Framework_TestCase
{
	public function setup()
	{
		if(!defined('TMPPATH')) define('TMPPATH', dirname(realpath(dirname(__FILE__))) . '/tmp');

		$this->existingUrl = 'http://www.spoon-library.com/downloads/1.0.3/spoon-1.0.3.zip';
		$this->nonExistingUrl = 'http://ksdgg.com/' . time() . '.txt';
		$this->destinationFile = TMPPATH . '/spoon.zip';
	}

	public function testDownload()
	{
		// download
		$this->assertTrue(SpoonFile::download($this->existingUrl, $this->destinationFile));

		// download again, but do not overwrite
		$this->assertFalse(SpoonFile::download($this->existingUrl, $this->destinationFile, false));
	}

	/**
	 * @expectedException SpoonFileException
	 */
	public function testDownloadFailure()
	{
		SpoonFile::download($this->nonExistingUrl, $this->destinationFile);
	}
}
