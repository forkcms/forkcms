<?php

if(!defined('SPOON_CHARSET')) define('SPOON_CHARSET', 'utf-8');

$includePath = dirname(dirname(dirname(dirname(__FILE__))));
set_include_path(get_include_path() . PATH_SEPARATOR . $includePath);

require_once 'spoon/spoon.php';
require_once 'PHPUnit/Framework/TestCase.php';

class SpoonFormTextareaTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var	SpoonForm
	 */
	protected $frm;

	/**
	 * @var	SpoonFormTextarea
	 */
	protected $txtMessage;

	public function setup()
	{
		$this->frm = new SpoonForm('textarea');
		$this->txtMessage = new SpoonFormTextarea('message', 'I am the default value');
		$this->frm->add($this->txtMessage);
	}

	public function testGetDefaultValue()
	{
		$this->assertEquals('I am the default value', $this->txtMessage->getDefaultValue());
	}

	public function testErrors()
	{
		$this->txtMessage->setError('You suck');
		$this->assertEquals('You suck', $this->txtMessage->getErrors());
		$this->txtMessage->addError(' cock');
		$this->assertEquals('You suck cock', $this->txtMessage->getErrors());
		$this->txtMessage->setError('');
		$this->assertEquals('', $this->txtMessage->getErrors());
	}

	public function testAttributes()
	{
		$this->txtMessage->setAttribute('rel', 'bauffman.jpg');
		$this->assertEquals('bauffman.jpg', $this->txtMessage->getAttribute('rel'));
		$this->txtMessage->setAttributes(array('id' => 'specialID'));
		$this->assertEquals(array('id' => 'specialID', 'name' => 'message', 'cols' => 62, 'rows' => 5, 'class' => 'inputTextarea', 'rel' => 'bauffman.jpg'), $this->txtMessage->getAttributes());
	}

	public function testIsFilled()
	{
		$this->assertFalse($this->txtMessage->isFilled());
		$_POST['message'] = 'I am not empty';
		$this->assertTrue($this->txtMessage->isFilled());
		$_POST['message'] = array('foo', 'bar');
		$this->assertTrue($this->txtMessage->isFilled());
	}

	public function testIsAlphabetical()
	{
		$this->assertFalse($this->txtMessage->isAlphabetical());
		$_POST['message'] = 'Bauffman';
		$this->assertTrue($this->txtMessage->isAlphabetical());
		$_POST['message'] = array('foo', 'bar');
		$this->assertTrue($this->txtMessage->isAlphabetical());
	}

	public function testIsAlphaNumeric()
	{
		$_POST['message'] = 'Spaces are not allowed?';
		$this->assertFalse($this->txtMessage->isAlphaNumeric());
		$_POST['message'] = 'L33t';
		$this->assertTrue($this->txtMessage->isAlphaNumeric());
		$_POST['message'] = array('foo', 'bar');
		$this->assertTrue($this->txtMessage->isAlphaNumeric());
	}

	public function testIsMaximumCharacters()
	{
		$_POST['message'] = 'Writing tests can be pretty frakkin boring';
		$this->assertTrue($this->txtMessage->isMaximumCharacters(100));
		$this->assertFalse($this->txtMessage->isMaximumCharacters(10));
		$_POST['message'] = array('foo', 'bar');
		$this->assertFalse($this->txtMessage->isMaximumCharacters(0));
	}

	public function testIsMinimumCharacters()
	{
		$_POST['message'] = 'Stil pretty bored';
		$this->assertTrue($this->txtMessage->isMinimumCharacters(10));
		$this->assertTrue($this->txtMessage->isMinimumCharacters(2));
		$this->assertFalse($this->txtMessage->isMinimumCharacters(23));
		$_POST['message'] = array('foo', 'bar');
		$this->assertFalse($this->txtMessage->isMinimumCharacters(10));
	}

	public function testGetValue()
	{
		$_POST['form'] = 'textarea';
		$_POST['message'] = '<a href="http://www.spoon-library.be">Bobby Tables, my friends call mééé</a>';
		$this->assertEquals(SpoonFilter::htmlspecialchars($_POST['message']), $this->txtMessage->getValue());
		$this->assertEquals($_POST['message'], $this->txtMessage->getValue(true));
		$_POST['message'] = array('foo', 'bar');
		$this->assertEquals('Array', $this->txtMessage->getValue(true));
	}
}
