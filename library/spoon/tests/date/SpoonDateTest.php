<?php

date_default_timezone_set('Europe/Brussels');
if(!defined('SPOON_CHARSET')) define('SPOON_CHARSET', 'utf-8');

$includePath = dirname(dirname(dirname(dirname(__FILE__))));
set_include_path(get_include_path() . PATH_SEPARATOR . $includePath);

require_once 'spoon/spoon.php';
require_once 'PHPUnit/Framework/TestCase.php';

class SpoonDateTest extends PHPUnit_Framework_TestCase
{
	public function testGetDate()
	{
		$this->assertEquals(date('Y-m-d H:i'), SpoonDate::getDate('Y-m-d H:i'));
		$this->assertEquals(SpoonDate::getDate('l j F Y', mktime(13, 20, 0, 8, 3, 1983)), 'Wednesday 3 August 1983');
	}
}
