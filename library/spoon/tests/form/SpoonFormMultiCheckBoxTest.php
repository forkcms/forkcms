<?php

if(!defined('SPOON_CHARSET')) define('SPOON_CHARSET', 'utf-8');

$includePath = dirname(dirname(dirname(dirname(__FILE__))));
set_include_path(get_include_path() . PATH_SEPARATOR . $includePath);

require_once 'spoon/spoon.php';
require_once 'PHPUnit/Framework/TestCase.php';

class SpoonFormMultiCheckBoxTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var	SpoonForm
	 */
	protected $frm;

	/**
	 * @var	SpoonFormMultiCheckbox
	 */
	protected $chkHobbies;

	public function setup()
	{
		$this->frm = new SpoonForm('multicheckbox');
		$hobbies[] = array('label' => 'Swimming', 'value' => 10);
		$hobbies[] = array('label' => 'Cycling', 'value' => 20, 'attributes' => array('rel' => 'bauffman.jpg'));
		$hobbies[] = array('label' => 'Running', 'value' => 30);
		$this->chkHobbies = new SpoonFormMultiCheckbox('hobbies', $hobbies, array(10, 20));
		$this->frm->add($this->chkHobbies);
	}

	public function testGetChecked()
	{
		$this->assertEquals(array('10', '20'), $this->chkHobbies->getChecked());
	}

	public function testIsFilled()
	{
		$this->assertFalse($this->chkHobbies->isFilled());
		$_POST['hobbies'] = array('bimbo', 'tramp');
		$this->assertFalse($this->chkHobbies->isFilled());
		$_POST['form'] = 'multicheckbox';
		$this->assertFalse($this->chkHobbies->isFilled());
		$_POST['hobbies'] = array(20);
		$this->assertTrue($this->chkHobbies->isFilled());
		$_POST['hobbies'] = array(20, 'bimbo', 'tramp');
		$this->assertTrue($this->chkHobbies->isFilled());
		$_POST['hobbies'] = 'foobar';
		$this->assertFalse($this->chkHobbies->isFilled());
	}

	public function testGetValue()
	{
		$_POST['form'] = 'multicheckbox';
		$this->assertEquals(array(), $this->chkHobbies->getValue());
		$_POST['hobbies'] = array('bimbo', 'tramp');
		$this->assertEquals(array(), $this->chkHobbies->getValue());
		$_POST['hobbies'] = array('10');
		$this->assertEquals(array('10'), $this->chkHobbies->getValue());
		$_POST['hobbies'] = array('10', 'bimbo', 'tramp');
		$this->assertEquals(array('10'), $this->chkHobbies->getValue());
		$_POST['hobbies'] = array('bimbo', 'tramp', '10', '30');
		$this->assertEquals(array('10', '30'), $this->chkHobbies->getValue());
		$this->chkHobbies->setAllowExternalData(true);
		$this->assertEquals(array('bimbo', 'tramp', '10', '30'), $this->chkHobbies->getValue());
		$_POST['hobbies'] = 'foobar';
		$this->assertEquals(array(), $this->chkHobbies->getValue());
	}

	public function testNotSupplyingCorrectFormatThrowsException()
	{
		$values = array('12' => 'aaa', '132' => 'bbb', '32' => 'ccc');
		$this->setExpectedException('SpoonFormException');
		$c = new SpoonFormMultiCheckbox('test', $values);
	}

	public function testNotSupplyingLabelThrowsException()
	{
		$values = array(
			array('value' => 'aaa'),
			array('value' => 'bbb'),
			array('value' => 'ccc')
		);
		$this->setExpectedException('SpoonFormException');
		$c = new SpoonFormMultiCheckbox('test', $values);
	}

	public function testNotSupplyingValueThrowsException()
	{
		$values = array(
			array('label' => 'aaa'),
			array('label' => 'bbb'),
			array('label' => 'ccc')
		);
		$this->setExpectedException('SpoonFormException');
		$c = new SpoonFormMultiCheckbox('test', $values);
	}
}
