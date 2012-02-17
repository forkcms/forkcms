<?php

if(!defined('SPOON_CHARSET')) define('SPOON_CHARSET', 'utf-8');

$includePath = dirname(dirname(dirname(dirname(__FILE__))));
set_include_path(get_include_path() . PATH_SEPARATOR . $includePath);

require_once 'spoon/spoon.php';
require_once 'PHPUnit/Framework/TestCase.php';

class SpoonFormRadiobuttonTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var	SpoonForm
	 */
	protected $frm;

	/**
	 * @var	SpoonFormRadiobutton
	 */
	protected $rbtGender;

	public function setup()
	{
		$this->frm = new SpoonForm('radiobutton');
		$gender[] = array('label' => 'Female', 'value' => 'F');
		$gender[] = array('label' => 'Male', 'value' => 'M');
		$this->rbtGender = new SpoonFormRadiobutton('gender', $gender, 'M');
		$this->frm->add($this->rbtGender);
	}

	public function testGetChecked()
	{
		$this->assertEquals('M', $this->rbtGender->getChecked());
	}

	public function testGetValue()
	{
		$_POST['form'] = 'radiobutton';
		$this->assertEquals('M', $this->rbtGender->getValue());
		$_POST['gender'] = 'F';
		$this->assertEquals('F', $this->rbtGender->getValue());
		$_POST['gender'] = array('foo', 'bar');
		$this->assertEquals('F', $this->rbtGender->getValue());
	}

	public function testIsFilled()
	{
		$_POST['form'] = 'radiobutton';
		$_POST['gender'] = 'M';
		$this->assertTrue($this->rbtGender->isFilled());
		$_POST['gender'] = 'foobar';
		$this->assertFalse($this->rbtGender->isFilled());
		$_POST['gender'] = array('foo', 'bar');
		$this->assertFalse($this->rbtGender->isFilled());
	}
}
