<?php

if(!defined('SPOON_CHARSET')) define('SPOON_CHARSET', 'utf-8');

$includePath = dirname(dirname(dirname(dirname(__FILE__))));
set_include_path(get_include_path() . PATH_SEPARATOR . $includePath);

require_once 'spoon/spoon.php';
require_once 'PHPUnit/Framework/TestCase.php';

class SpoonFormDropdownTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var	SpoonForm
	 */
	protected $frm;

	/**
	 * @var	SpoonFormDropdown
	 */
	protected $ddmSingle, $ddmMultiple, $ddmOptGroupSingle, $ddmOptGroupMultiple, $ddmDefaultElement;

	public function setup()
	{
		$this->frm = new SpoonForm('dropdown');
		$this->ddmSingle = new SpoonFormDropdown('single', array(1 => 'Davy Hellemans', 'Tys Verkoyen', 'Dave Lens'));
		$this->ddmMultiple = new SpoonFormDropdown('multiple', array(1 => 'Swimming', 'Running', 'Cycling', 'Boxing', 'Slackin'), null, true);
		$this->ddmOptGroupSingle = new SpoonFormDropdown('optgroup_single', array('foo', 123 => 'bar', 'foobar' => array('foo', 'baz')));
		$this->ddmOptGroupMultiple = new SpoonFormDropdown('optgroup_multiple', array('foo', 123 => 'bar', 'foobar' => array('foo', 'baz')), null, true);
		$this->ddmDefaultElement = new SpoonFormDropdown('default_element', array(1 => 'Davy Hellemans'));
		$this->ddmDefaultElement->setDefaultElement('Baz', 1337);
		$this->frm->add($this->ddmSingle, $this->ddmMultiple, $this->ddmOptGroupSingle, $this->ddmOptGroupMultiple, $this->ddmDefaultElement);
	}

	public function testAttributes()
	{
		// single dropdown
		$this->ddmSingle->setAttribute('rel', 'bauffman.jpg');
		$this->assertEquals('bauffman.jpg', $this->ddmSingle->getAttribute('rel'));
		$this->ddmSingle->setAttributes(array('id' => 'specialID'));
		$this->assertEquals(array('id' => 'specialID', 'name' => 'single', 'class' => 'inputDropdown', 'size' => 1, 'rel' => 'bauffman.jpg'), $this->ddmSingle->getAttributes());

		// single dropdown (optgroups)
		$this->ddmOptGroupSingle->setAttribute('rel', 'bauffman.jpg');
		$this->assertEquals('bauffman.jpg', $this->ddmOptGroupSingle->getAttribute('rel'));
		$this->ddmOptGroupSingle->setAttributes(array('id' => 'specialID'));
		$this->assertEquals(array('id' => 'specialID', 'name' => 'optgroup_single', 'class' => 'inputDropdown', 'size' => 1, 'rel' => 'bauffman.jpg'), $this->ddmOptGroupSingle->getAttributes());

		// multiple dropdown
		$this->ddmMultiple->setAttribute('rel', 'bauffman.jpg');
		$this->assertEquals('bauffman.jpg', $this->ddmMultiple->getAttribute('rel'));
		$this->ddmMultiple->setAttributes(array('id' => 'specialID'));
		$this->assertEquals(array('id' => 'specialID', 'name' => 'multiple', 'class' => 'inputDropdown', 'rel' => 'bauffman.jpg'), $this->ddmMultiple->getAttributes());

		// multiple dropdown (optgroups)
		$this->ddmOptGroupMultiple->setAttribute('rel', 'bauffman.jpg');
		$this->assertEquals('bauffman.jpg', $this->ddmOptGroupMultiple->getAttribute('rel'));
		$this->ddmOptGroupMultiple->setAttributes(array('id' => 'specialID'));
		$this->assertEquals(array('id' => 'specialID', 'name' => 'optgroup_multiple', 'class' => 'inputDropdown', 'rel' => 'bauffman.jpg'), $this->ddmOptGroupMultiple->getAttributes());
	}

	public function testIsFilled()
	{
		// single dropdown
		$this->assertEquals(false, $this->ddmSingle->isFilled());
		$_POST['single'] = '2';
		$_POST['form'] = 'dropdown';
		$this->assertTrue($this->ddmSingle->isFilled());
		$_POST['single'] = '1337';
		$this->assertFalse($this->ddmSingle->isFilled());

		// default element (single)
		$this->ddmSingle->setDefaultElement('', 1337);
		$this->assertTrue($this->ddmSingle->isFilled());
		$_POST['single'] = 'spoon';
		$this->assertFalse($this->ddmSingle->isFilled());

		// arrays
		$_POST['single'] = array('foo', 'bar');
		$this->assertFalse($this->ddmSingle->isFilled());

		// single dropdown (optgroups)
		$this->assertEquals(false, $this->ddmOptGroupSingle->isFilled());
		$_POST['optgroup_single'] = '1';
		$_POST['form'] = 'dropdown';
		$this->assertTrue($this->ddmOptGroupSingle->isFilled());
		$_POST['optgroup_single'] = '1337';
		$this->assertFalse($this->ddmOptGroupSingle->isFilled());

		// default element (single & optgroups)
		$this->ddmOptGroupSingle->setDefaultElement('', 1337);
		$this->assertTrue($this->ddmOptGroupSingle->isFilled());
		$_POST['optgroup_single'] = 'spoon';
		$this->assertFalse($this->ddmOptGroupSingle->isFilled());

		// multiple dropdown
		$this->assertFalse($this->ddmMultiple->isFilled());
		$_POST['multiple'] = array('1', '2');
		$this->assertTrue($this->ddmMultiple->isFilled());
		$_POST['multiple'] = array('1336', '1337', '1338');
		$this->assertFalse($this->ddmMultiple->isFilled());
		$_POST['multiple'] = array('1337', 1);
		$this->assertTrue($this->ddmMultiple->isFilled());

		// default element (multiple)
		$this->ddmMultiple->setDefaultElement('', '1337');
		$_POST['multiple'] = 'nothing';
		$this->assertFalse($this->ddmMultiple->isFilled());
		$_POST['multiple'] = array('1337');
		$this->assertTrue($this->ddmMultiple->isFilled());

		// multiple dropdown (optgroups)
		$this->assertFalse($this->ddmOptGroupMultiple->isFilled());
		$_POST['optgroup_multiple'] = array('0', '1');
		$this->assertTrue($this->ddmOptGroupMultiple->isFilled());
		$_POST['optgroup_multiple'] = array('1336', '1337', '1338');
		$this->assertFalse($this->ddmOptGroupMultiple->isFilled());
		$_POST['optgroup_multiple'] = array('1337', 1);
		$this->assertTrue($this->ddmOptGroupMultiple->isFilled());

		// default element (multiple & optgroups)
		$this->ddmOptGroupMultiple->setDefaultElement('', '1337');
		$_POST['optgroup_multiple'] = 'nothing';
		$this->assertFalse($this->ddmOptGroupMultiple->isFilled());
		$_POST['optgroup_multiple'] = array('1337');
		$this->assertTrue($this->ddmOptGroupMultiple->isFilled());
	}

	public function testGetValue()
	{
		$_POST['form'] = 'dropdown';
		$_POST['single'] = '1';
		$_POST['multiple'] = array('1', '2', '3');
		$_POST['optgroup_single'] = '123';
		$_POST['optgroup_multiple'] = array('0', '123');
		$this->assertEquals($_POST['single'], $this->ddmSingle->getValue());
		$this->assertEquals($_POST['optgroup_single'], $this->ddmOptGroupSingle->getValue());
		$this->assertEquals($_POST['multiple'], $this->ddmMultiple->getValue());
		$this->assertEquals($_POST['optgroup_multiple'], $this->ddmOptGroupMultiple->getValue());

		$_POST['single'] = array('foo', 'bar');
		$this->assertNull($this->ddmSingle->getValue());
	}

	/**
	 * There was an issue with a dropdown that default elements were not taken into account
	 * when they actually contain a value.
	 *
	 * @group bugfix
	 * @link https://github.com/Spoon/library/pull/19
	 */
	public function testDefaultElement()
	{
		$_POST['form'] = 'dropdown';
		$_POST['default_element'] = '1337';
		$this->assertTrue($this->ddmDefaultElement->isFilled());
		$this->assertEquals($_POST['default_element'], $this->ddmDefaultElement->getValue());
	}

	/**
	 * @group bugfix
	 */
	public function testParse()
	{
		$this->ddmDefaultElement->parse();
	}
}
