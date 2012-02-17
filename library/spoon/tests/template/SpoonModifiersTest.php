<?php

date_default_timezone_set('Europe/Brussels');
if(!defined('SPOON_CHARSET')) define('SPOON_CHARSET', 'utf-8');

$includePath = dirname(dirname(dirname(dirname(__FILE__))));
set_include_path(get_include_path() . PATH_SEPARATOR . $includePath);

require_once 'spoon/spoon.php';
require_once 'PHPUnit/Framework/TestCase.php';

class SpoonTemplateModifiersTest extends PHPUnit_Framework_TestCase
{
	public function testClearModifiers()
	{
		SpoonTemplateModifiers::clearModifiers();
		$this->assertEquals(array(), SpoonTemplateModifiers::getModifiers());
	}

	public function testCreateHTMLLinks()
	{
		$tlds = array(
			'aero', 'asia', 'biz', 'cat', 'com', 'coop', 'edu', 'gov', 'info', 'int', 'jobs', 'mil', 'mobi',
			'museum', 'name', 'net', 'org', 'pro', 'tel', 'travel', 'ac', 'ad', 'ae', 'af', 'ag', 'ai', 'al',
			'am', 'an', 'ao', 'aq', 'ar', 'as', 'at', 'au', 'aw', 'ax', 'az', 'ba', 'bb', 'bd' ,'be', 'bf', 'bg',
			'bh', 'bi', 'bj', 'bm', 'bn', 'bo', 'br', 'bs', 'bt', 'bv', 'bw', 'by' ,'bz', 'ca', 'cc' ,'cd', 'cf',
			'cg', 'ch', 'ci', 'ck', 'cl', 'cm', 'cn', 'co', 'cr', 'cu', 'cv', 'cx', 'cy', 'cz', 'cz', 'de', 'dj', 'dk',
			'dm', 'do', 'dz', 'ec', 'ee', 'eg', 'er', 'es', 'et', 'eu', 'fi', 'fj', 'fk', 'fm', 'fo', 'fr', 'ga', 'gb',
			'gd', 'ge', 'gf', 'gg', 'gh', 'gi', 'gl', 'gm', 'gn', 'gp', 'gq', 'gr', 'gs', 'gt', 'gu', 'gw', 'gy', 'hk',
			'hm', 'hn', 'hr', 'ht', 'hu', 'id', 'ie', 'il', 'im', 'in', 'io', 'iq', 'ir', 'is', 'it', 'je', 'jm', 'jo',
			'jp', 'ke', 'kg', 'kh', 'ki', 'km', 'kn', 'kp', 'kr', 'kw', 'ky', 'kz', 'la', 'lb', 'lc', 'li', 'lk', 'lr',
			'ls', 'lt', 'lu', 'lv', 'ly', 'ma', 'mc', 'md', 'me', 'mg', 'mh', 'mk', 'ml', 'mn', 'mn', 'mo', 'mp', 'mr',
			'ms', 'mt', 'mu', 'mv', 'mw', 'mx', 'my', 'mz', 'na', 'nc', 'ne', 'nf', 'ng', 'ni', 'nl', 'no', 'np', 'nr',
			'nu', 'nz', 'pa', 'pe', 'pf', 'pg', 'ph', 'pk', 'pl', 'pm', 'pn', 'pr', 'ps', 'pt', 'pw', 'py', 'qa',
			're', 'rs', 'ru', 'rw', 'sa', 'sb', 'sc', 'sd', 'se', 'sg', 'sh', 'si', 'sj', 'sj', 'sk', 'sl', 'sm',
			'sn', 'so', 'sr', 'st', 'su', 'sv', 'sy', 'sz', 'tc', 'td', 'tf', 'tg', 'th', 'tj', 'tk', 'tl', 'tm', 'tn',
			'to', 'tp', 'tr', 'tt', 'tv', 'tw', 'tz', 'ua', 'ug', 'uk', 'us', 'uy', 'uz', 'va', 'vc', 've', 'vg', 'vi',
			'vn', 'vu', 'wf', 'ws', 'ye', 'yt', 'yu', 'za', 'zm', 'zw', 'arpa'
		);

		foreach($tlds as $tld)
		{
			$this->assertEquals('verkeerde link: www.link.' . $tld . 'l', SpoonTemplateModifiers::createHTMLLinks('verkeerde link: www.link.' . $tld . 'l'));
			$this->assertEquals('zonder http: <a href="http://www.link.' . $tld . '">www.link.' . $tld . '</a>', SpoonTemplateModifiers::createHTMLLinks('zonder http: www.link.' . $tld));
			$this->assertEquals('met http: <a href="http://www.link.' . $tld . '">http://www.link.' . $tld . '</a>', SpoonTemplateModifiers::createHTMLLinks('met http: http://www.link.' . $tld));

			// port
			$this->assertEquals('verkeerde link: www.link.' . $tld . 'l:80', SpoonTemplateModifiers::createHTMLLinks('verkeerde link: www.link.' . $tld . 'l:80'));
			$this->assertEquals('zonder http: <a href="http://www.link.' . $tld . ':80">www.link.' . $tld . ':80</a>', SpoonTemplateModifiers::createHTMLLinks('zonder http: www.link.' . $tld . ':80'));
			$this->assertEquals('met http: <a href="http://www.link.' . $tld . ':80">http://www.link.' . $tld . ':80</a>', SpoonTemplateModifiers::createHTMLLinks('met http: http://www.link.' . $tld . ':80')); // @todo hier zit je!

			// querystring
			$this->assertEquals('verkeerde link: www.link.' . $tld . 'l?m=12&b=0%20d', SpoonTemplateModifiers::createHTMLLinks('verkeerde link: www.link.' . $tld . 'l?m=12&b=0%20d'));
			$this->assertEquals('zonder http: <a href="http://www.link.' . $tld . '?m=12&b=0%20d">www.link.' . $tld . '?m=12&b=0%20d</a>', SpoonTemplateModifiers::createHTMLLinks('zonder http: www.link.' . $tld . '?m=12&b=0%20d'));
			$this->assertEquals('met http: <a href="http://www.link.' . $tld . '?m=12&b=0%20d">http://www.link.' . $tld . '?m=12&b=0%20d</a>', SpoonTemplateModifiers::createHTMLLinks('met http: http://www.link.' . $tld . '?m=12&b=0%20d'));

			// folder
			$this->assertEquals('verkeerde link: www.link.' . $tld . 'l/mekker', SpoonTemplateModifiers::createHTMLLinks('verkeerde link: www.link.' . $tld . 'l/mekker'));
			$this->assertEquals('zonder http: <a href="http://www.link.' . $tld . '/mekker">www.link.' . $tld . '/mekker</a>', SpoonTemplateModifiers::createHTMLLinks('zonder http: www.link.' . $tld . '/mekker'));
			$this->assertEquals('met http: <a href="http://www.link.' . $tld . '/mekker">http://www.link.' . $tld . '/mekker</a>', SpoonTemplateModifiers::createHTMLLinks('met http: http://www.link.' . $tld . '/mekker'));
		}
	}

	public function testGetDate()
	{
		$this->assertEquals(date('d'), SpoonTemplateModifiers::date(time(), 'd'));
		$this->assertEquals(date('d/m/Y H:i', strtotime('2011-11-04 13:37')), SpoonTemplateModifiers::date('2011-11-04 13:37', 'd/m/Y H:i'));
	}

	public function testLowercase()
	{
		$this->assertEquals('hooray for boobies!', SpoonTemplateModifiers::lowercase('HOORAY FOR BOOBIES!'));
		$this->assertEquals('bleéééé', SpoonTemplateModifiers::lowercase('BLEéééé'));
	}

	public function testUppercase()
	{
		$this->assertEquals('HOORAY FOR BOOBIES!', SpoonTemplateModifiers::uppercase('hooray for boobies!'));
		$this->assertEquals('BLEÉÉÉÉ', SpoonTemplateModifiers::uppercase('bleéééé'));
	}
}
