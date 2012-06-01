<?php

date_default_timezone_set('Europe/Brussels');
if(!defined('SPOON_CHARSET')) define('SPOON_CHARSET', 'utf-8');

$includePath = dirname(dirname(dirname(dirname(__FILE__))));
set_include_path(get_include_path() . PATH_SEPARATOR . $includePath);

require_once 'spoon/spoon.php';
require_once 'PHPUnit/Framework/TestCase.php';

class SpoonFormTest extends PHPUnit_Framework_TestCase
{
	public function testMain()
	{
		$frm = new SpoonForm('name', 'action');
		$frm->addButton('submit', 'submit');
		$frm->addCheckbox('agree', true);
		$frm->addDate('date', time(), 'd/m/Y');
		$frm->addDropdown('author', array(1 => 'Davy', 'Tijs', 'Dave'), 1);
		$frm->addFile('pdf');
		$frm->addImage('image');
		$frm->addHidden('cant_see_me', 'whoop-tie-doo');
		$frm->addMultiCheckbox('hobbies', array(array('label' => 'Swimming', 'value' => 'swimming')));
		$frm->addPassword('top_sekret', 'stars-and-stripes');
		$frm->addRadiobutton('gender', array(array('label' => 'Male', 'value' => 'male')));
		$frm->addTextarea('message', 'big piece of text');
		$frm->addText('email', 'something@example.org');
		$frm->addText('now', date('H:i'));
	}

	public function  testExistsField()
	{
		// setup
		$frm = new SpoonForm('name', 'action');
		$frm->addButton('submit', 'submit');

		// checks
		$this->assertTrue($frm->existsField('submit'));
		$this->assertFalse($frm->existsField('custom_field'));
	}
}
