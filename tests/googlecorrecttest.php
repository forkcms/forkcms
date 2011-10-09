<?php

// get the api
require_once dirname(__FILE__) . '/../library/external/google_correct.php';

class GooglecorrectTest extends PHPUnit_Framework_Testcase
{
	// the api instance
	private $gc;

	public function setUp()
	{
		$this->gc = new GoogleCorrect('nl', 'test');
	}

	public function testSetup()
	{
		$this->assertEquals($this->gc->getSearchTerm(), 'test');
	}

	public function testTerm()
	{
		// set the term
		$this->gc->setSearchTerm('ipsum');

		// is it set?
		$this->assertEquals($this->gc->getSearchTerm(), 'ipsum');
	}

	public function testCall()
	{
		// do the call
		$this->gc->doCall();

		// does the request have a key?
		$this->assertArrayHasKey('0', $this->gc->getResponse());
	}
}