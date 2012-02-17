<?php

if(!defined('SPOON_CHARSET')) define('SPOON_CHARSET', 'utf-8');

$includePath = dirname(dirname(dirname(dirname(__FILE__))));
set_include_path(get_include_path() . PATH_SEPARATOR . $includePath);

require_once 'spoon/spoon.php';
require_once 'PHPUnit/Framework/TestCase.php';

class SpoonFormHiddenTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var	SpoonForm
	 */
	protected $frm;

	/**
	 * @var	SpoonFormHidden
	 */
	protected $hidHidden;

	public function setup()
	{
		$this->frm = new SpoonForm('hiddenfield');
		$this->hidHidden = new SpoonFormHidden('hidden', 'I am the default value');
		$this->frm->add($this->hidHidden);
	}

	public function testAttributes()
	{
		$this->hidHidden->setAttribute('rel', 'bauffman.jpg');
		$this->assertEquals('bauffman.jpg', $this->hidHidden->getAttribute('rel'));
		$this->hidHidden->setAttributes(array('id' => 'specialID'));
		$this->assertEquals(array('id' => 'specialID', 'name' => 'hidden', 'rel' => 'bauffman.jpg'), $this->hidHidden->getAttributes());
	}

	public function testIsFilled()
	{
		$this->assertEquals(false, $this->hidHidden->isFilled());
		$_POST['hidden'] = 'I am not empty';
		$this->assertTrue($this->hidHidden->isFilled());
		$_POST['hidden'] = array('foo', 'bar');
		$this->assertTrue($this->hidHidden->isFilled());
	}

	public function testGetValue()
	{
		$_POST['form'] = 'hiddenfield';
		$_POST['hidden'] = 'But I am le tired';
		$this->assertEquals($_POST['hidden'], $this->hidHidden->getValue());
		$_POST['hidden'] = array('foo', 'bar');
		$this->assertEquals('Array', $this->hidHidden->getValue());
	}
}
