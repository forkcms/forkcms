<?php

if(!defined('SPOON_CHARSET')) define('SPOON_CHARSET', 'utf-8');

$includePath = dirname(dirname(dirname(dirname(__FILE__))));
set_include_path(get_include_path() . PATH_SEPARATOR . $includePath);

require_once 'spoon/spoon.php';
require_once 'PHPUnit/Framework/TestCase.php';

class SpoonFormTextTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var	SpoonForm
	 */
	protected $frm;

	/**
	 * @var	SpoonFormText
	 */
	protected $txtName;

	public function setup()
	{
		$this->frm = new SpoonForm('textfield');
		$this->txtName = new SpoonFormText('name', 'I am the default value');
		$this->frm->add($this->txtName);
	}

	public function testGetDefaultValue()
	{
		$this->assertEquals('I am the default value', $this->txtName->getDefaultValue());
	}

	public function testErrors()
	{
		$this->txtName->setError('You suck');
		$this->assertEquals('You suck', $this->txtName->getErrors());
		$this->txtName->addError(' cock');
		$this->assertEquals('You suck cock', $this->txtName->getErrors());
		$this->txtName->setError('');
		$this->assertEquals('', $this->txtName->getErrors());
	}

	public function testAttributes()
	{
		$this->txtName->setAttribute('rel', 'bauffman.jpg');
		$this->assertEquals('bauffman.jpg', $this->txtName->getAttribute('rel'));
		$this->txtName->setAttributes(array('id' => 'specialID'));
		$this->assertEquals(array('id' => 'specialID', 'name' => 'name', 'type' => 'text', 'class' => 'inputText', 'rel' => 'bauffman.jpg'), $this->txtName->getAttributes());
	}

	public function testIsFilled()
	{
		$this->assertFalse($this->txtName->isFilled());
		$_POST['name'] = 'I am not empty';
		$this->assertTrue($this->txtName->isFilled());
		$_POST['name'] = array('foo', 'bar');
		$this->assertTrue($this->txtName->isFilled());
	}

	public function testIsAlphabetical()
	{
		$this->assertFalse($this->txtName->isAlphabetical());
		$_POST['name'] = 'Bauffman';
		$this->assertTrue($this->txtName->isAlphabetical());

		// since the array will be casted to a string, this will be 'Array' and that is alphabetical :)
		$_POST['name'] = array('foo', 'bar');
		$this->assertTrue($this->txtName->isAlphabetical());
	}

	public function testIsAphaNumeric()
	{
		$_POST['name'] = 'Spaces are not allowed?';
		$this->assertFalse($this->txtName->isAlphaNumeric());
		$_POST['name'] = 'L33t';
		$this->assertTrue($this->txtName->isAlphaNumeric());

		// since the array will be casted to a string, this will be 'Array' and that is alphabetical :)
		$_POST['name'] = array('foo', 'bar');
		$this->assertTrue($this->txtName->isAlphaNumeric());
	}

	public function testIsBetween()
	{
		$_POST['name'] = '101';
		$this->assertTrue($this->txtName->isBetween(1, 102));
		$this->assertTrue($this->txtName->isBetween(-101, 101));
		$this->assertFalse($this->txtName->isBetween(200, 201));
		$this->assertFalse($this->txtName->isBetween(1000, 200));

		$_POST['name'] = array('foo', 'bar');
		$this->assertFalse($this->txtName->isBetween(10, 200));
	}

	public function testIsBool()
	{
		$_POST['name'] = 'true';
		$this->assertTrue($this->txtName->isBool());
		$_POST['name'] = '1';
		$this->assertTrue($this->txtName->isBool());
		$_POST['name'] = 'false';
		$this->assertTrue($this->txtName->isBool());
		$_POST['name'] = '0';
		$this->assertTrue($this->txtName->isBool());
		$_POST['name'] = 'I liek boobies';
		$this->assertFalse($this->txtName->isBool());
		$_POST['name'] = '101';
		$this->assertFalse($this->txtName->isBool());
		$_POST['name'] = '090';
		$this->assertFalse($this->txtName->isBool());
		$_POST['name'] = array('foo', 'bar');
		$this->assertFalse($this->txtName->isBool());
	}

	public function testIsDigital()
	{
		$_POST['name'] = '090';
		$this->assertTrue($this->txtName->isDigital());
		$_POST['name'] = 'Douchebag';
		$this->assertFalse($this->txtName->isDigital());
		$_POST['name'] = '';
		$this->assertFalse($this->txtName->isDigital());
		$_POST['name'] = array('foo', 'bar');
		$this->assertFalse($this->txtName->isDigital());
	}

	public function testIsEmail()
	{
		$this->assertFalse($this->txtName->isEmail());
		$_POST['name'] = 'davy@spoon-library.be';
		$this->assertTrue($this->txtName->isEmail());
		$_POST['name'] = '';
		$this->assertFalse($this->txtName->isEmail());
		$_POST['name'] = array('foo', 'bar');
		$this->assertFalse($this->txtName->isEmail());
	}

	public function testIsFilename()
	{
		$this->assertFalse($this->txtName->isFilename());
		$_POST['name'] = 'something.jpg';
		$this->assertTrue($this->txtName->isFilename());

		// since the array will be casted to a string, this will be 'Array' and that is a possible filename :)
		$_POST['name'] = array('foo', 'bar');
		$this->assertTrue($this->txtName->isFilename());
	}

	public function testIsFloat()
	{
		$this->assertFalse($this->txtName->isFloat());
		$_POST['name'] = 1.1;
		$this->assertTrue($this->txtName->isFloat());
		$_POST['name'] = -209;
		$this->assertTrue($this->txtName->isFloat());
		$_POST['name'] = 199;
		$this->assertTrue($this->txtName->isFloat());
		$_POST['name'] = '1,35';
		$this->assertTrue($this->txtName->isFloat(null, true));
		$_POST['name'] = '-1,35';
		$this->assertTrue($this->txtName->isFloat(null, true));

		$_POST['name'] = array('foo', 'bar', 190);
		$this->assertFalse($this->txtName->isFloat());
	}

	public function testIsGreatherThan()
	{
		$_POST['name'] = 199;
		$this->assertTrue($this->txtName->isGreaterThan(1));
		$this->assertTrue($this->txtName->isGreaterThan(-199));
		$this->assertFalse($this->txtName->isGreaterThan(199));

		$_POST['name'] = array('foo', 'bar');
		$this->assertFalse($this->txtName->isGreaterThan(1337));
	}

	public function testIsInteger()
	{
		$_POST['name'] = 199;
		$this->assertTrue($this->txtName->isInteger());
		$_POST['name'] = -199;
		$this->assertTrue($this->txtName->isInteger());
		$_POST['name'] = 1.1;
		$this->assertFalse($this->txtName->isInteger());
		$_POST['name'] = '1,9';
		$this->assertFalse($this->txtName->isInteger());
		$_POST['name'] = array('foo', 'bar');
		$this->assertFalse($this->txtName->isInteger());
	}

	public function testIsIp()
	{
		$this->assertFalse($this->txtName->isIp());
		$_POST['name'] = '127.0.0.1';
		$this->assertTrue($this->txtName->isIp());
		$_POST['name'] = '192.168.1.101';
		$this->assertTrue($this->txtName->isIp());
		$_POST['name'] = array('foo', 'bar');
		$this->assertFalse($this->txtName->isIp());
	}

	public function testIsMaximum()
	{
		$_POST['name'] = 'Spanks';
		$this->assertTrue($this->txtName->isMaximum(10));
		$_POST['name'] = 199;
		$this->assertFalse($this->txtName->isMaximum(18));
		$this->assertTrue($this->txtName->isMaximum(300));
		$_POST['name'] = array('foo', 'bar');
		$this->assertTrue($this->txtName->isMaximum(200));
	}

	public function testIsMaximumCharacters()
	{
		$_POST['name'] = 'Writing tests can be pretty frakkin boring';
		$this->assertTrue($this->txtName->isMaximumCharacters(100));
		$this->assertFalse($this->txtName->isMaximumCharacters(10));
		$_POST['name'] = array('foo', 'bar');
		$this->assertTrue($this->txtName->isMaximumCharacters(10));
	}

	public function testIsMinimum()
	{
		$_POST['name'] = 5;
		$this->assertTrue($this->txtName->isMinimum(5));
		$this->assertTrue($this->txtName->isMinimum(4));
		$this->assertFalse($this->txtName->isMinimum(7));
		$_POST['name'] = array('foo', 'bar');
		$this->assertFalse($this->txtName->isMinimum(7));
	}

	public function testIsMinimumCharacters()
	{
		$_POST['name'] = 'Stil pretty bored';
		$this->assertTrue($this->txtName->isMinimumCharacters(10));
		$this->assertTrue($this->txtName->isMinimumCharacters(2));
		$this->assertFalse($this->txtName->isMinimumCharacters(23));
		$_POST['name'] = array('foo', 'bar');
		$this->assertFalse($this->txtName->isMinimumCharacters(23));
	}

	public function testIsNumeric()
	{
		$_POST['name'] = '010192029';
		$this->assertTrue($this->txtName->isNumeric());
		$_POST['name'] = '1337';
		$this->assertTrue($this->txtName->isNumeric());
		$_POST['name'] = 'I can haz two cheezeburgers?';
		$this->assertFalse($this->txtName->isNumeric());
		$_POST['name'] = array('foo', 'bar');
		$this->assertFalse($this->txtName->isNumeric());
	}

	public function testIsSmallerThan()
	{
		$_POST['name'] = 137;
		$this->assertTrue($this->txtName->isSmallerThan(138));
		$this->assertTrue($this->txtName->isSmallerThan(200));
		$this->assertFalse($this->txtName->isSmallerThan(0));
		$this->assertFalse($this->txtName->isSmallerThan(-16));
		$_POST['name'] = array('foo', 'bar');
		$this->assertFalse($this->txtName->isSmallerThan(0));
	}

	public function testIsURL()
	{
		$_POST['name'] = 'http://www.spoon-library.com';
		$this->assertTrue($this->txtName->isURL());
		$_POST['name'] = array('foo', 'bar');
		$this->assertFalse($this->txtName->isURL());
	}

	public function testIsValidAgainstRegexp()
	{
		$_POST['name'] = 'Spoon';
		$this->assertTrue($this->txtName->isValidAgainstRegexp('/([a-z]+)/'));
		$this->assertFalse($this->txtName->isValidAgainstRegexp('/([0-9]+)/'));
		$_POST['name'] = array('foo', 'bar');
		$this->assertTrue($this->txtName->isValidAgainstRegexp('/Array/'));
	}

	public function testGetValue()
	{
		$_POST['form'] = 'textfield';
		$_POST['name'] = '<a href="http://www.spoon-library.be">Bobby Tables, my friends call mééé</a>';
		$this->assertEquals(SpoonFilter::htmlspecialchars($_POST['name']), $this->txtName->getValue());
		$this->assertEquals($_POST['name'], $this->txtName->getValue(true));
		$_POST['name'] = array('foo', 'bar');
		$this->assertEquals('Array', $this->txtName->getValue());
	}
}
