<?php

if(!defined('SPOON_CHARSET')) define('SPOON_CHARSET', 'utf-8');

$includePath = dirname(dirname(dirname(dirname(__FILE__))));
set_include_path(get_include_path() . PATH_SEPARATOR . $includePath);

require_once 'spoon/spoon.php';
require_once 'PHPUnit/Framework/TestCase.php';

class SpoonDirectoryTest extends PHPUnit_Framework_TestCase
{
	protected $path;

	protected function setUp()
	{
		// set path
		$this->path = dirname(dirname(__FILE__)) . '/tmp';
	}

	public function testCopy()
	{
		// setup
		SpoonDirectory::create($this->path . '/copy_org');

		// copy
		SpoonDirectory::copy($this->path . '/copy_org', $this->path . '/copy_dest');

		// check if both folders exists
		$var = (bool) (SpoonDirectory::exists($this->path . '/copy_org') && SpoonDirectory::exists($this->path . '/copy_dest'));

		// assert
		$this->assertTrue($var);

		// cleanup
		SpoonDirectory::delete($this->path . '/copy_org');
		SpoonDirectory::delete($this->path . '/copy_dest');
	}

	public function testCreate()
	{
		// create folder
		SpoonDirectory::create($this->path . '/create');

		// assert
		$this->assertTrue(SpoonDirectory::exists($this->path . '/create'));

		// cleanup
		SpoonDirectory::delete($this->path . '/create');
	}

	public function testDelete()
	{
		// create folder
		SpoonDirectory::create($this->path . '/delete');

		// delete
		SpoonDirectory::delete($this->path . '/delete');

		// assert
		$this->assertFalse(SpoonDirectory::exists($this->path . '/delete'));
	}

	public function testExists()
	{
		// create folder
		SpoonDirectory::create($this->path . '/exists');

		// assert
		$this->assertTrue(SpoonDirectory::exists($this->path . '/exists'));

		// cleanup
		SpoonDirectory::delete($this->path . '/exists');
	}

	public function testGetSize()
	{
		SpoonDirectory::create($this->path . '/size');
		$this->assertEquals(0, SpoonDirectory::getSize($this->path . '/size'));
		SpoonDirectory::delete($this->path . '/size');
	}

	public function isWritable()
	{
		// create folders
		SpoonDirectory::create($this->path . '/writable', 0777);
		SpoonDirectory::create($this->path . '/not_writable', 0000);

		// assert
		$this->assertTrue(SpoonDirectory::isWritable($this->path . '/writable'));
		$this->assertFalse(SpoonDirectory::isWritable($this->path . '/not_writable'));

		// cleanup
		SpoonDirectory::delete($this->path . '/writable');
		SpoonDirectory::delete($this->path . '/not_writable');
	}

	public function testMove()
	{
		// setup
		SpoonDirectory::create($this->path . '/move_org');

		// copy
		SpoonDirectory::move($this->path . '/move_org', $this->path . '/move_dest');

		// check if both folders exists
		$var = (bool) (!SpoonDirectory::exists($this->path . '/move_org') && SpoonDirectory::exists($this->path . '/move_dest'));

		// assert
		$this->assertTrue($var);

		// cleanup
		SpoonDirectory::delete($this->path . '/move_dest');
	}
}
