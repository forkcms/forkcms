<?php

if(!defined('SPOON_CHARSET')) define('SPOON_CHARSET', 'utf-8');

$includePath = dirname(dirname(dirname(dirname(__FILE__))));
set_include_path(get_include_path() . PATH_SEPARATOR . $includePath);

require_once 'spoon/spoon.php';
require_once 'PHPUnit/Framework/TestCase.php';

class SpoonFormPasswordTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var	SpoonForm
	 */
	protected $frm;

	/**
	 * @var	SpoonFormPassword
	 */
	protected $txtPassword;

	public function setup()
	{
		$this->frm = new SpoonForm('passwordfield');
		$this->txtPassword = new SpoonFormPassword('name', 'I am the default value');
		$this->frm->add($this->txtPassword);
	}

	public function testGetDefaultValue()
	{
		$this->assertEquals('I am the default value', $this->txtPassword->getDefaultValue());
	}

	public function testErrors()
	{
		$this->txtPassword->setError('You suck');
		$this->assertEquals('You suck', $this->txtPassword->getErrors());
		$this->txtPassword->addError(' cock');
		$this->assertEquals('You suck cock', $this->txtPassword->getErrors());
		$this->txtPassword->setError('');
		$this->assertEquals('', $this->txtPassword->getErrors());
	}

	public function testAttributes()
	{
		$this->txtPassword->setAttribute('rel', 'bauffman.jpg');
		$this->assertEquals('bauffman.jpg', $this->txtPassword->getAttribute('rel'));
		$this->txtPassword->setAttributes(array('id' => 'specialID'));
		$this->assertEquals(array('id' => 'specialID', 'name' => 'name', 'class' => 'inputPassword', 'rel' => 'bauffman.jpg'), $this->txtPassword->getAttributes());
	}

	public function testIsFilled()
	{
		$this->assertFalse($this->txtPassword->isFilled());
		$_POST['name'] = 'I am not empty';
		$this->assertTrue($this->txtPassword->isFilled());
		$_POST['name'] = array('foo', 'bar');
		$this->assertTrue($this->txtPassword->isFilled());
	}

	public function testIsAlphabetical()
	{
		$this->assertFalse($this->txtPassword->isAlphabetical());
		$_POST['name'] = 'Bauffman';
		$this->assertTrue($this->txtPassword->isAlphabetical());
		$_POST['name'] = array('foo', 'bar');
		$this->assertTrue($this->txtPassword->isAlphabetical());
	}

	public function testIsAlphaNumeric()
	{
		$_POST['name'] = 'Spaces are not allowed?';
		$this->assertFalse($this->txtPassword->isAlphaNumeric());
		$_POST['name'] = 'L33t';
		$this->assertTrue($this->txtPassword->isAlphaNumeric());
		$_POST['name'] = array('foo', 'bar');
		$this->assertTrue($this->txtPassword->isAlphaNumeric());
	}

	public function testIsMaximumCharacters()
	{
		$_POST['name'] = 'Writing tests can be pretty frakkin boring';
		$this->assertTrue($this->txtPassword->isMaximumCharacters(100));
		$this->assertFalse($this->txtPassword->isMaximumCharacters(10));
		$_POST['name'] = array('foo', 'bar');
		$this->assertFalse($this->txtPassword->isMaximumCharacters(4));
	}

	public function testIsMinimumCharacaters()
	{
		$_POST['name'] = 'Stil pretty bored';
		$this->assertTrue($this->txtPassword->isMinimumCharacters(10));
		$this->assertTrue($this->txtPassword->isMinimumCharacters(2));
		$this->assertFalse($this->txtPassword->isMinimumCharacters(23));
		$_POST['name'] = array('foo', 'bar');
		$this->assertFalse($this->txtPassword->isMinimumCharacters(23));
	}

	public function testIsValidAgainstRegexp()
	{
		$_POST['name'] = 'Spoon';
		$this->assertTrue($this->txtPassword->isValidAgainstRegexp('/([a-z]+)/'));
		$this->assertFalse($this->txtPassword->isValidAgainstRegexp('/([0-9]+)/'));
		$_POST['name'] = array('foo', 'bar');
		$this->assertTrue($this->txtPassword->isValidAgainstRegexp('/Array/'));
	}

	public function testGetValue()
	{
		$_POST['form'] = 'passwordfield';
		$_POST['name'] = '<a href="http://www.spoon-library.be">Bobby Tables, my friends call mééé</a>';
		$this->assertEquals($_POST['name'], $this->txtPassword->getValue());
		$_POST['name'] = array('foo', 'bar');
		$this->assertEquals('Array', $this->txtPassword->getValue());
	}
}
