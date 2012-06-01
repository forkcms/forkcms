<?php

if(!defined('SPOON_CHARSET')) define('SPOON_CHARSET', 'utf-8');

$includePath = dirname(dirname(dirname(dirname(__FILE__))));
set_include_path(get_include_path() . PATH_SEPARATOR . $includePath);

require_once 'spoon/spoon.php';
require_once 'PHPUnit/Framework/TestCase.php';

class SpoonFeedRSSTest extends PHPUnit_Framework_TestCase
{
	public function testMain()
	{
		$rss = new SpoonFeedRSS('Spoon Library', 'http://feeds2.feedburner.com/spoonlibrary', 'Spoon Library - RSS feed.');
	}
}
