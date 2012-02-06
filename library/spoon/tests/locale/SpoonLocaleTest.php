<?php

if(!defined('SPOON_CHARSET')) define('SPOON_CHARSET', 'utf-8');

$includePath = dirname(dirname(dirname(dirname(__FILE__))));
set_include_path(get_include_path() . PATH_SEPARATOR . $includePath);

require_once 'spoon/spoon.php';
require_once 'PHPUnit/Framework/TestCase.php';

class SpoonLocaleTest extends PHPUnit_Framework_TestCase
{
	public function testGetAvailableLanguages()
	{
		$this->assertEquals(array('de', 'en', 'es', 'fr', 'nl'), SpoonLocale::getAvailableLanguages());
	}

	public function testGetConjunction()
	{
		$this->assertEquals('and', SpoonLocale::getConjunction('And', 'en'));
		$this->assertEquals('y', SpoonLocale::getConjunction('And', 'es'));
		$this->assertEquals('und', SpoonLocale::getConjunction('And', 'de'));
		$this->assertEquals('et', SpoonLocale::getConjunction('And', 'fr'));
		$this->assertEquals('en', SpoonLocale::getConjunction('And', 'nl'));
	}
}
