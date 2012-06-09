<?php

if(!defined('SPOON_CHARSET')) define('SPOON_CHARSET', 'utf-8');

$includePath = dirname(dirname(dirname(dirname(__FILE__))));
set_include_path(get_include_path() . PATH_SEPARATOR . $includePath);

require_once 'spoon/spoon.php';
require_once 'PHPUnit/Framework/TestCase.php';

class SpoonFileFieldTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var	SpoonForm
	 */
	protected $frm;

	/**
	 * @var	SpoonFormFile
	 */
	protected $filePDF;

	public function setup()
	{
		$this->frm = new SpoonForm('filefield');
		$this->filePDF = new SpoonFormFile('pdf');
		$this->frm->add($this->filePDF);

		$_FILES['pdf']['name'] = 'My pdf.pdf';
		$_FILES['pdf']['type'] = 'application/pdf';
		$_FILES['pdf']['tmp_name'] = '/Applications/MAMP/tmp/php/phpDBDfyR';
		$_FILES['pdf']['error'] = 0;
		$_FILES['pdf']['size'] = 86592;
	}

	public function testAttributes()
	{
		$this->filePDF->setAttribute('rel', 'bauffman.jpg');
		$this->assertEquals('bauffman.jpg', $this->filePDF->getAttribute('rel'));
		$this->filePDF->setAttributes(array('id' => 'specialID'));
		$this->assertEquals(array('id' => 'specialID', 'name' => 'pdf', 'class' => 'inputFilefield', 'rel' => 'bauffman.jpg'), $this->filePDF->getAttributes());
	}

	public function testGetExtension()
	{
		$_POST['form'] = 'filefield';
		$this->assertEquals('pdf', $this->filePDF->getExtension());
		$_FILES['pdf']['name'] = 'I love you.mp3.eXe';
		$this->assertEquals('eXe', $this->filePDF->getExtension(false));
	}

	public function testGetFileName()
	{
		$_POST['form'] = 'filefield';
		$this->assertEquals('My pdf.pdf', $this->filePDF->getFileName());
		$this->assertEquals('My pdf', $this->filePDF->getFileName(false)); // my pdf.pdf
	}

	public function testGetFileSize()
	{
		$_POST['form'] = 'filefield';
		$this->assertEquals(86592, $this->filePDF->getFileSize('b'));
		$this->assertEquals(84.56, $this->filePDF->getFileSize('kb', 2));
	}

	public function testIsAllowedExtension()
	{
		$_POST['form'] = 'filefield';
		$this->assertEquals(true, $this->filePDF->isAllowedExtension(array('jpg', 'pdf', 'jpeg')));
		$this->assertEquals(false, $this->filePDF->isAllowedExtension(array('xpdf')));
	}

	public function testIsFileName()
	{
		$_POST['form'] = 'filefield';
		$this->assertEquals(true, $this->filePDF->isFilename());
		$_FILES['pdf']['name'] = 'WTF / Panda\' cuteness';
		$this->assertEquals(false, $this->filePDF->isFilename());
	}

	public function testIsFileSize()
	{
		$_POST['form'] = 'filefield';
		$this->assertEquals(true, $this->filePDF->isFileSize('50', 'kb', 'greater'));
		$this->assertEquals(false, $this->filePDF->isFileSize('50', 'kb', 'smaller'));
		$this->assertEquals(true, $this->filePDF->isFileSize('1', 'mb', 'smaller'));
		$this->assertEquals(true, $this->filePDF->isFileSize(86592, 'b', 'equal'));
	}

	public function testIsFilled()
	{
		$_POST['form'] = 'filefield';
		$this->assertEquals(true, $this->filePDF->isFilled());
	}
}
