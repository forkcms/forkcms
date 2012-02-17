<?php

date_default_timezone_set('Europe/Brussels');
if(!defined('SPOON_CHARSET')) define('SPOON_CHARSET', 'utf-8');

$includePath = dirname(dirname(dirname(dirname(__FILE__))));
set_include_path(get_include_path() . PATH_SEPARATOR . $includePath);

require_once 'spoon/spoon.php';
require_once 'PHPUnit/Framework/TestCase.php';

class SpoonLogTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var SpoonLog
	 */
	protected $log;

	public function setUp()
	{
		// create directory
		$directory = realpath(dirname(__FILE__) . '/..') . '/tmp/logging';
		SpoonDirectory::create($directory, 0777);

		// create instance
		$this->log = new SpoonLog('custom', $directory);
	}

	public function testGetMaxLogSize()
	{
		$this->assertEquals(10, $this->log->getMaxLogSize());
	}

	public function testSetMaxLogSize()
	{
		$this->log->setMaxLogSize(12);
		$this->assertEquals(12, $this->log->getMaxLogSize());
	}

	public function testSetPath()
	{
		$this->log->setPath('/Users/bauffman/Desktop');
		$this->assertEquals('/Users/bauffman/Desktop', $this->log->getPath());
	}

	public function testSetType()
	{
		$this->log->setType('myCustomLogging');
		$this->assertEquals('myCustomLogging', $this->log->getType());
		$this->log->setType('1337');
		$this->log->setType('my_underscores_logging');
		$this->log->setType('my-hyphen-logging');
	}

	/**
	 * @expectedException SpoonLogException
	 */
	public function testSetTypeFailure()
	{
		$this->log->setType('No way hosé!');
	}

	public function testWrite()
	{
		$this->log->setMaxLogSize(1);
		for($i = 1; $i < 1000; $i++)
		{
			$this->log->write('We wants it, we needs it. Must have the precious. They stole it from us. Sneaky little hobbitses. Wicked, tricksy, false!');
		}
	}

	public function testRotate()
	{
		$this->log->write('Message for the log');
		$this->log->rotate();
		$this->assertFalse(SpoonFile::exists($this->log->getPath() . '/custom.log'));
	}

	public function tearDown()
	{
		// remove directory
		$directory = realpath(dirname(__FILE__) . '/..') . '/tmp/logging';
		SpoonDirectory::delete($directory);
	}
}
