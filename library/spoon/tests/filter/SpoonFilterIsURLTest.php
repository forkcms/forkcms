<?php

if(!defined('SPOON_CHARSET')) define('SPOON_CHARSET', 'utf-8');

$includePath = dirname(dirname(dirname(dirname(__FILE__))));
set_include_path(get_include_path() . PATH_SEPARATOR . $includePath);

require_once 'spoon/spoon.php';
require_once 'PHPUnit/Framework/TestCase.php';

class SpoonFilterIsURLTest extends PHPUnit_Framework_TestCase
{
	/**
	 * URL tests based on those of Mathias Bynens (http://mathiasbynens.be/demo/url-regex)
	 */
	public function testIsURL()
	{
		// URLs that should match
		$this->assertTrue(SpoonFilter::isURL('http://foo.com/blah_blah/'));
		$this->assertTrue(SpoonFilter::isURL('http://foo.com/blah_blah_(wikipedia)'));
		$this->assertTrue(SpoonFilter::isURL('http://foo.com/blah_blah_(wikipedia)_(again)'));
		$this->assertTrue(SpoonFilter::isURL('http://www.example.com/wpstyle/?p=364'));
		$this->assertTrue(SpoonFilter::isURL('https://www.example.com/foo/?bar=baz&inga=42&quux'));
		$this->assertTrue(SpoonFilter::isURL('http://✪df.ws/123'));
		$this->assertTrue(SpoonFilter::isURL('http://userid:password@example.com:8080'));
		$this->assertTrue(SpoonFilter::isURL('http://userid:password@example.com:8080/'));
		$this->assertTrue(SpoonFilter::isURL('http://userid@example.com'));
		$this->assertTrue(SpoonFilter::isURL('http://userid@example.com/'));
		$this->assertTrue(SpoonFilter::isURL('http://userid@example.com:8080'));
		$this->assertTrue(SpoonFilter::isURL('http://userid@example.com:8080/'));
		$this->assertTrue(SpoonFilter::isURL('http://userid:password@example.com'));
		$this->assertTrue(SpoonFilter::isURL('http://userid:password@example.com/'));
		$this->assertTrue(SpoonFilter::isURL('http://142.42.1.1/'));
		$this->assertTrue(SpoonFilter::isURL('http://142.42.1.1:8080/'));
		$this->assertTrue(SpoonFilter::isURL('http://➡.ws/䨹'));
		$this->assertTrue(SpoonFilter::isURL('http://⌘.ws'));
		$this->assertTrue(SpoonFilter::isURL('http://⌘.ws/'));
		$this->assertTrue(SpoonFilter::isURL('http://foo.com/blah_(wikipedia)#cite-1'));
		$this->assertTrue(SpoonFilter::isURL('http://foo.com/blah_(wikipedia)_blah#cite-1'));
		$this->assertTrue(SpoonFilter::isURL('http://foo.com/unicode_(✪)_in_parens'));
		$this->assertTrue(SpoonFilter::isURL('http://foo.com/(something)?after=parens'));
		$this->assertTrue(SpoonFilter::isURL('http://☺.damowmow.com/'));
		$this->assertTrue(SpoonFilter::isURL('http://code.google.com/events/#&product=browser'));
		$this->assertTrue(SpoonFilter::isURL('http://j.mp'));
		$this->assertTrue(SpoonFilter::isURL('ftp://foo.bar/baz'));
		$this->assertTrue(SpoonFilter::isURL('http://foo.bar/?q=Test%20URL-encoded%20stuff'));
		$this->assertTrue(SpoonFilter::isURL('http://例子.测试'));
		$this->assertTrue(SpoonFilter::isURL('http://उदाहरण.परीक्षा'));
		$this->assertTrue(SpoonFilter::isURL('http://-.~_!$&\'()*+,;=:%40:80%2f::::::@example.com'));
		$this->assertTrue(SpoonFilter::isURL('http://1337.net'));
		$this->assertTrue(SpoonFilter::isURL('http://223.255.255.254'));
		$this->assertTrue(SpoonFilter::isURL('http://feedproxy.google.com/~r/netlog/~3/EdqJ5FkO78o/internet-is-de-petrischaal-van-de-maatschappij'));

		// URLs that should fail
		$this->assertFalse(SpoonFilter::isURL('http://'));
		$this->assertFalse(SpoonFilter::isURL('http://.'));
		$this->assertFalse(SpoonFilter::isURL('http://..'));
		$this->assertFalse(SpoonFilter::isURL('http://../'));
		$this->assertFalse(SpoonFilter::isURL('http://?'));
		$this->assertFalse(SpoonFilter::isURL('http://??'));
		$this->assertFalse(SpoonFilter::isURL('http://??/'));
		$this->assertFalse(SpoonFilter::isURL('http://#'));
		$this->assertFalse(SpoonFilter::isURL('http://##'));
		$this->assertFalse(SpoonFilter::isURL('http://##/'));
		$this->assertFalse(SpoonFilter::isURL('http://foo.bar?q=Spaces should be encoded'));
		$this->assertFalse(SpoonFilter::isURL('//'));
		$this->assertFalse(SpoonFilter::isURL('//a'));
		$this->assertFalse(SpoonFilter::isURL('///a'));
		$this->assertFalse(SpoonFilter::isURL('///'));
		$this->assertFalse(SpoonFilter::isURL('http:///a'));
		$this->assertFalse(SpoonFilter::isURL('foo.com'));
		$this->assertFalse(SpoonFilter::isURL('rdar://1234'));
		$this->assertFalse(SpoonFilter::isURL('h://test'));
		$this->assertFalse(SpoonFilter::isURL('http:// shouldfail.com'));
		$this->assertFalse(SpoonFilter::isURL(':// should fail'));
		$this->assertFalse(SpoonFilter::isURL('http://foo.bar/foo(bar)baz quux'));
		$this->assertFalse(SpoonFilter::isURL('ftps://foo.bar/'));
		$this->assertFalse(SpoonFilter::isURL('http://-error-.invalid/'));
		$this->assertFalse(SpoonFilter::isURL('http://a.b--c.de/'));
		$this->assertFalse(SpoonFilter::isURL('http://-a.b.co'));
		$this->assertFalse(SpoonFilter::isURL('http://a.b-.co'));
		$this->assertFalse(SpoonFilter::isURL('http://0.0.0.0'));
		$this->assertFalse(SpoonFilter::isURL('http://10.1.1.0'));
		$this->assertFalse(SpoonFilter::isURL('http://10.1.1.255'));
		$this->assertFalse(SpoonFilter::isURL('http://224.1.1.1'));
		$this->assertFalse(SpoonFilter::isURL('http://1.1.1.1.1'));
		$this->assertFalse(SpoonFilter::isURL('http://123.123.123'));
		$this->assertFalse(SpoonFilter::isURL('http://.www.foo.bar/'));
		$this->assertFalse(SpoonFilter::isURL('http://www.foo.bar./'));
		$this->assertFalse(SpoonFilter::isURL('http://.www.foo.bar./8*/'));
		$this->assertFalse(SpoonFilter::isURL('http://10.1.1.1'));
		$this->assertFalse(SpoonFilter::isURL('http://10.1.1.254'));
	}
}
