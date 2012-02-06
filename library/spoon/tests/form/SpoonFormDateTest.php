<?php

date_default_timezone_set('Europe/Brussels');
if(!defined('SPOON_CHARSET')) define('SPOON_CHARSET', 'utf-8');

$includePath = dirname(dirname(dirname(dirname(__FILE__))));
set_include_path(get_include_path() . PATH_SEPARATOR . $includePath);

require_once 'spoon/spoon.php';
require_once 'PHPUnit/Framework/TestCase.php';

class SpoonFormDateTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var	SpoonForm
	 */
	protected $frm;

	/**
	 * @var	SpoonFormDate
	 */
	protected $txtDate;

	public function setup()
	{
		$this->frm = new SpoonForm('datefield');
		$this->txtDate = new SpoonFormDate('date', strtotime('Last Monday'), 'd/m/Y');
		$this->frm->add($this->txtDate);
		$_POST['form'] = 'datefield';
	}

	public function testGetDefaultValue()
	{
		$this->assertEquals(date('d/m/Y', strtotime('Last Monday')), $this->txtDate->getDefaultValue());
	}

	public function testErrors()
	{
		$this->txtDate->setError('You suck');
		$this->assertEquals('You suck', $this->txtDate->getErrors());
		$this->txtDate->addError(' cock');
		$this->assertEquals('You suck cock', $this->txtDate->getErrors());
		$this->txtDate->setError('');
		$this->assertEquals('', $this->txtDate->getErrors());
	}

	public function testAttributes()
	{
		$this->txtDate->setAttribute('rel', 'bauffman.jpg');
		$this->assertEquals('bauffman.jpg', $this->txtDate->getAttribute('rel'));
		$this->txtDate->setAttributes(array('id' => 'specialID'));
		$this->assertEquals(array('id' => 'specialID', 'name' => 'date','maxlength' => 10, 'class' => 'inputDatefield', 'rel' => 'bauffman.jpg'), $this->txtDate->getAttributes());
	}

	public function testIsFilled()
	{
		$this->assertFalse($this->txtDate->isFilled());
		$_POST['date'] = '12/10/2009';
		$this->assertTrue($this->txtDate->isFilled());
		$_POST['date'] = array('foo', 'bar');
		$this->assertTrue($this->txtDate->isFilled());
	}

	public function testIsValid()
	{
		$this->assertFalse($this->txtDate->isValid());
		$_POST['date'] = '29/02/1997';
		$this->assertFalse($this->txtDate->isValid());
		$_POST['date'] = '29/02/2000';
		$this->assertTrue($this->txtDate->isValid());
		$_POST['date'] = '31/04/2009';
		$this->assertFalse($this->txtDate->isValid());
		$_POST['date'] = array('foo', 'bar');
		$this->assertFalse($this->txtDate->isValid());
	}

	public function testGetTimestamp()
	{
		$_POST['date'] = '12/10/2009';
		$this->assertEquals('12/10/2009 12:13:14', date('d/m/Y H:i:s', $this->txtDate->getTimestamp(null, null, null, 12, 13, 14)));
		$this->assertEquals('12/10/2010 12:13:14', date('d/m/Y H:i:s', $this->txtDate->getTimestamp(2010, null, null, 12, 13, 14)));
		$this->assertEquals('12/11/2009 12:13:14', date('d/m/Y H:i:s', $this->txtDate->getTimestamp(null, 11, null, 12, 13, 14)));
		$this->assertEquals('25/10/2009 12:13:14', date('d/m/Y H:i:s', $this->txtDate->getTimestamp(null, null, 25, 12, 13, 14)));

		$_POST['date'] = array('foo', 'bar');
		$this->assertEquals(date('Y-m-d H:i:s'), date('Y-m-d H:i:s', $this->txtDate->getTimestamp()));
	}

	public function testGetValue()
	{
		$_POST['form'] = 'datefield';
		$_POST['date'] = '12/10/2009';
		$this->assertEquals('12/10/2009', $this->txtDate->getValue());

		$_POST['date'] = array('foo', 'bar');
		$this->assertEquals('Array', $this->txtDate->getValue());
	}
}
