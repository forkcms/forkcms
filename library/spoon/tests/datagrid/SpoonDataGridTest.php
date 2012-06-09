<?php

if(!defined('SPOON_CHARSET')) define('SPOON_CHARSET', 'utf-8');

$includePath = dirname(dirname(dirname(dirname(__FILE__))));
set_include_path(get_include_path() . PATH_SEPARATOR . $includePath);

require_once 'spoon/spoon.php';
require_once 'PHPUnit/Framework/TestCase.php';

class SpoonDataGridTest extends PHPUnit_Framework_TestCase
{
	public function testMain()
	{
		// data array
		$array[] = array('name' => 'Davy Hellemans', 'email' => 'davy@spoon-library.be');
		$array[] = array('name' => 'Tijs Verkoyen', 'email' => 'tijs@spoon-library.be');
		$array[] = array('name' => 'Dave Lens', 'email' => 'dave@spoon-library.be');

		// create source
		$source = new SpoonDatagridSourceArray($array);

		// create datagrid
		$dg = new SpoonDatagrid($source);
	}

	public function testGetTemplate()
	{
		// data array
		$array[] = array('name' => 'Davy Hellemans', 'email' => 'davy@spoon-library.be');
		$array[] = array('name' => 'Tijs Verkoyen', 'email' => 'tijs@spoon-library.be');
		$array[] = array('name' => 'Dave Lens', 'email' => 'dave@spoon-library.be');

		// create source
		$source = new SpoonDatagridSourceArray($array);

		// create datagrid
		$dg = new SpoonDatagrid($source);

		// fetch instance
		if($dg->getTemplate() instanceof SpoonTemplate) { /* do nothing */ }
		else throw new SpoonException('getTemplate should return an object of SpoonTemplate.');
	}
}
