<?php

if(!defined('SPOON_CHARSET')) define('SPOON_CHARSET', 'utf-8');

$includePath = dirname(dirname(dirname(dirname(__FILE__))));
set_include_path(get_include_path() . PATH_SEPARATOR . $includePath);

require_once 'spoon/spoon.php';
require_once 'PHPUnit/Framework/TestCase.php';

class SpoonFilterTest extends PHPUnit_Framework_TestCase
{
	public function testArrayMapRecursive()
	{
		/* without allowedKeys parameter */
		// test array
		$testArray = array(0 => array('string1' => 'This%20is%20a%20string'), 1 => array('string2' => 'This%20is%20a%20string'));

		// expected result
		$testResult = array(0 => array('string1' => 'This is a string'), 1 => array('string2' => 'This is a string'));

		// perform test
		$this->assertEquals($testResult, SpoonFilter::arrayMapRecursive('urldecode', $testArray));


		/* with allowedKeys parameter */
		// test array
		$testArray = array(0 => serialize(array('string1' => 'spoon')), 1 => serialize(array('string2' => 'rocks')));

		// expected result
		$testResult= array(0 => 'a:1:{s:7:"string1";s:5:"spoon";}', 1 => array('string2' => 'rocks'));

		// perform test
		$this->assertEquals($testResult, SpoonFilter::arrayMapRecursive('unserialize', $testArray, '1'));


		/* with allowedKeys parameter, depth of 4 */
		// test array
		$testArray = array(0 => array('array1' => array(array('spoon' => serialize('kicks'), 'serious' => serialize('ass')))), 1 => serialize(array('string2' => 'ass')));

		// expected result
		$testResult = array(0 => array('array1' => array(array('spoon' => 's:5:"kicks";', 'serious' => 'ass'))), 1 => array('string2' => 'ass'));

		// perform test
		$this->assertEquals($testResult, SpoonFilter::arrayMapRecursive('unserialize', $testArray, array('serious', '1')));
	}

	public function testArraySortKeys()
	{
		// test array
		$testArray = array(-2 => 'Davy Hellemans', 1 => 'Tijs Verkoyen', 4 => 'Dave Lens');

		// expected result
		$expectedArray = array('Davy Hellemans', 'Tijs Verkoyen', 'Dave Lens');

		// perform test
		$this->assertEquals($expectedArray, SpoonFilter::arraySortKeys($testArray));
	}

	public function testGetGetValue()
	{
		// setup
		$_GET['id'] = '1337';
		$_GET['type'] = 'web';
		$_GET['animal'] = 'donkey';

		// perform tests
		$this->assertEquals(0, SpoonFilter::getGetValue('category_id', null, 0, 'int'));
		$this->assertEquals(1337, SpoonFilter::getGetValue('id', null, 0, 'int'));
		$this->assertEquals('web', SpoonFilter::getGetValue('type', array('web', 'print'), 'print'));
		$this->assertEquals('whale', SpoonFilter::getGetValue('animal', array('whale', 'horse'), 'whale'));
		$this->assertEquals('donkey', SpoonFilter::getGetValue('animal', null, 'whale'));
	}

	public function testGetPostValue()
	{
		// setup
		$_POST['id'] = '1337';
		$_POST['type'] = 'web';
		$_POST['animal'] = 'donkey';

		// perform tests
		$this->assertEquals(0, SpoonFilter::getPostValue('category_id', null, 0, 'int'));
		$this->assertEquals(1337, SpoonFilter::getPostValue('id', null, 0, 'int'));
		$this->assertEquals('web', SpoonFilter::getPostValue('type', array('web', 'print'), 'print'));
		$this->assertEquals('whale', SpoonFilter::getPostValue('animal', array('whale', 'horse'), 'whale'));
		$this->assertEquals('donkey', SpoonFilter::getPostValue('animal', null, 'whale'));
	}

	public function testGetValue()
	{
		// setup
		$id = '1337';
		$type = 'web';
		$animal = 'donkey';
		$animals = array('1337', 'web', 'donkey');

		// perform tests
		$this->assertEquals(1337, SpoonFilter::getValue($id, null, 0, 'int'));
		$this->assertEquals('web', SpoonFilter::getValue($type, array('web', 'print'), 'print'));
		$this->assertEquals('whale', SpoonFilter::getValue($animal, array('whale', 'horse'), 'whale'));
		$this->assertEquals('donkey', SpoonFilter::getValue($animal, null, 'whale'));
		$this->assertEquals(array('1337', 'web', 'donkey'), SpoonFilter::getValue($animals, null, null, 'array'));
		$this->assertEquals(array('1337', 'web'), SpoonFilter::getValue($animals, array('1337', 'web'), array('soep'), 'array'));
		$this->assertEquals(array('soep'), SpoonFilter::getValue(array('blikken doos'), array('1337', 'web'), array('soep'), 'array'));
	}

	public function testHtmlentities()
	{
		// setup
		$input = 'Ik heb "géén" bananen vandaag';
		$expectedResult = 'Ik heb "g&eacute;&eacute;n" bananen vandaag';

		// perform test
		$this->assertEquals($expectedResult, SpoonFilter::htmlentities(utf8_decode($input), 'iso-8859-1'));
		$this->assertEquals($expectedResult, SpoonFilter::htmlentities($input, 'utf-8'));
		$expectedResult = 'Ik heb &quot;g&eacute;&eacute;n&quot; bananen vandaag';
		$this->assertEquals($expectedResult, SpoonFilter::htmlentities($input, null, ENT_QUOTES));
	}

	public function testHtmlspecialchars()
	{
		// setup
		$input = '<a href="http://www.spoon-library.be">Ik heb géén bananen vandaag</a>';
		$expectedResult = '&lt;a href=&quot;http://www.spoon-library.be&quot;&gt;Ik heb géén bananen vandaag&lt;/a&gt;';

		// perform test
		$this->assertEquals($expectedResult, SpoonFilter::htmlspecialchars($input, 'utf-8'));
	}

	public function testHtmlentitiesDecode()
	{
		// setup
		$input = 'Ik heb g&eacute;&eacute;n bananen vandaag';
		$expectedResult = 'Ik heb géén bananen vandaag';

		// perform test
		$this->assertEquals(utf8_decode($expectedResult), SpoonFilter::htmlentitiesDecode(utf8_decode($input), 'iso-8859-1'));
		$this->assertEquals($expectedResult, SpoonFilter::htmlentitiesDecode($input, 'utf-8'));
	}

	public function testIsAlphabetical()
	{
		$this->assertTrue(SpoonFilter::isAlphabetical('geen'));
		$this->assertTrue(SpoonFilter::isAlphabetical('GeeN'));
		$this->assertFalse(SpoonFilter::isAlphabetical('géén'));
		$this->assertFalse(SpoonFilter::isAlphabetical('gééN'));
	}

	public function testIsAlphaNumeric()
	{
		$this->assertTrue(SpoonFilter::isAlphaNumeric('John09'));
		$this->assertFalse(SpoonFilter::isAlphaNumeric('Johan Mayer 007'));
	}

	public function testIsBetween()
	{
		$this->assertTrue(SpoonFilter::isBetween(1, 10, 5));
		$this->assertTrue(SpoonFilter::isBetween(1, 10, 1));
		$this->assertTrue(SpoonFilter::isBetween(1, 10, 10));
		$this->assertFalse(SpoonFilter::isBetween(1, 10, -1));
		$this->assertFalse(SpoonFilter::isBetween(1, 10, 0));
		$this->assertFalse(SpoonFilter::isBetween(1, 10, 12));
	}

	public function testIsBool()
	{
		$this->assertTrue(SpoonFilter::isBool('true'));
		$this->assertTrue(SpoonFilter::isBool(1));
		$this->assertTrue(SpoonFilter::isBool('on'));
		$this->assertTrue(SpoonFilter::isBool('yes'));
		$this->assertTrue(SpoonFilter::isBool('false'));
		$this->assertTrue(SpoonFilter::isBool(0));
		$this->assertFalse(SpoonFilter::isBool(100));
		$this->assertFalse(SpoonFilter::isBool(900));
		$this->assertTrue(SpoonFilter::isBool(090));
	}

	public function testIsDigital()
	{
		$this->assertTrue(SpoonFilter::isDigital('010192029'));
		$this->assertTrue(SpoonFilter::isDigital(1337));
		$this->assertFalse(SpoonFilter::isDigital('I can has cheezeburger'));
	}

	public function testIsEmail()
	{
		$this->assertTrue(SpoonFilter::isEmail('erik@spoon-library.be'));
		$this->assertTrue(SpoonFilter::isEmail('erik+bauffman@spoon-library.be'));
		$this->assertTrue(SpoonFilter::isEmail('erik-bauffman@spoon-library.be'));
		$this->assertTrue(SpoonFilter::isEmail('erik.bauffman@spoon-library.be'));
		$this->assertTrue(SpoonFilter::isEmail('a.osterhaus@erasmusnc.nl'));
		$this->assertTrue(SpoonFilter::isEmail('asmonto@umich.edu'));
	}

	public function testIsEven()
	{
		$this->assertTrue(SpoonFilter::isEven(0));
		$this->assertFalse(SpoonFilter::isEven(1));
		$this->assertTrue(SpoonFilter::isEven(10901920));
		$this->assertFalse(SpoonFilter::isEven(-1337));
	}

	public function testIsFilename()
	{
		$this->assertTrue(SpoonFilter::isFilename('test.tpl'));
		$this->assertTrue(SpoonFilter::isFilename('spoon_template.php'));
		$this->assertFalse(SpoonFilter::isFilename('/Users/bauffman/Desktop/test.txt'));
	}

	public function testIsFloat()
	{
		$this->assertTrue(SpoonFilter::isFloat(1));
		$this->assertFalse(SpoonFilter::isFloat('a'));
		$this->assertTrue(SpoonFilter::isFloat(1e10));
		$this->assertFalse(SpoonFilter::isFloat('1e10'));
		$this->assertFalse(SpoonFilter::isFloat('1a10'));
		$this->assertTrue(SpoonFilter::isFloat(1.337));
		$this->assertTrue(SpoonFilter::isFloat(-1.337));
		$this->assertTrue(SpoonFilter::isFloat(100));
		$this->assertTrue(SpoonFilter::isFloat(-100));
		$this->assertFalse(SpoonFilter::isFloat('1.,35'));
		$this->assertFalse(SpoonFilter::isFloat('1,.35'));
		$this->assertTrue(SpoonFilter::isFloat('1,35', true));
		$this->assertTrue(SpoonFilter::isFloat('-1,35', true));
		$this->assertTrue(SpoonFilter::isFloat(65.00, true));
		$this->assertTrue(SpoonFilter::isFloat('65.00'));
		$this->assertTrue(SpoonFilter::isFloat(65.010, true));
		$this->assertTrue(SpoonFilter::isFloat('65.010', true));
	}

	public function testIsGreaterThan()
	{
		$this->assertTrue(SpoonFilter::isGreaterThan(1, 10));
		$this->assertTrue(SpoonFilter::isGreaterThan(-10, -1));
		$this->assertTrue(SpoonFilter::isGreaterThan(-1, 10));
		$this->assertFalse(SpoonFilter::isGreaterThan(1, -10));
		$this->assertFalse(SpoonFilter::isGreaterThan(0, 0));
	}

	public function testIsInteger()
	{
		$this->assertTrue(SpoonFilter::isInteger(0));
		$this->assertTrue(SpoonFilter::isInteger(1));
		$this->assertTrue(SpoonFilter::isInteger(1234567890));
		$this->assertTrue(SpoonFilter::isInteger(-1234567890));
		$this->assertFalse(SpoonFilter::isInteger(1.337));
		$this->assertFalse(SpoonFilter::isInteger(-1.337));
	}

	public function testIsInternalReferrer()
	{
		// reset referrer
		unset($_SERVER['HTTP_REFERER']);
		$this->assertTrue(SpoonFilter::isInternalReferrer());

		// new referrer
		$_SERVER['HTTP_REFERER'] = 'http://www.spoon-library.com/about-us';
		$_SERVER['HTTP_HOST'] = 'spoon-library.com';
		$this->assertTrue(SpoonFilter::isInternalReferrer(array('spoon-library.com')));

		// multiple domains
		$this->assertTrue(SpoonFilter::isInternalReferrer(array('docs.spoon-library.com', 'blog.spoon-library.com', 'spoon-library.com')));

		// incorrect!
		$this->assertFalse(SpoonFilter::isInternalReferrer(array('rotten.com')));
		$this->assertFalse(SpoonFilter::isInternalReferrer(array('rotten.com', 'rotn.com')));
	}

	public function testIsIP()
	{
		$this->assertTrue(SpoonFilter::isIp('127.0.0.1'));
		$this->assertTrue(SpoonFilter::isIp('192.168.1.101'));
	}

	public function testIsMaximum()
	{
		$this->assertTrue(SpoonFilter::isMaximum(10, 1));
		$this->assertTrue(SpoonFilter::isMaximum(10, 10));
		$this->assertTrue(SpoonFilter::isMaximum(-10, -10));
		$this->assertFalse(SpoonFilter::isMaximum(100, 101));
		$this->assertFalse(SpoonFilter::isMaximum(-100, -99));
	}

	public function testIsMaximumCharacters()
	{
		$string = 'Ik heb er géén gedacht van';
		$this->assertTrue(SpoonFilter::isMaximumCharacters(26, $string, 'utf-8'));
		$this->assertFalse(SpoonFilter::isMaximumCharacters(10, $string, 'utf-8'));
		$this->assertTrue(SpoonFilter::isMaximumCharacters(26, utf8_decode($string), 'iso-8859-1'));
	}

	public function testIsMinimum()
	{
		$this->assertFalse(SpoonFilter::isMinimum(10, 1));
		$this->assertTrue(SpoonFilter::isMinimum(10, 10));
		$this->assertTrue(SpoonFilter::isMinimum(-10, -10));
		$this->assertTrue(SpoonFilter::isMinimum(100, 101));
		$this->assertTrue(SpoonFilter::isMinimum(-100, -99));
	}

	public function testIsMinimumCharacters()
	{
		$string = 'Ik heb er géén gedacht van';
		$this->assertTrue(SpoonFilter::isMinimumCharacters(10, $string, 'utf-8'));
		$this->assertFalse(SpoonFilter::isMinimumCharacters(30, $string, 'utf-8'));
		$this->assertTrue(SpoonFilter::isMinimumCharacters(10, utf8_decode($string), 'iso-8859-1'));
	}

	public function testIsNumeric()
	{
		$this->assertTrue(SpoonFilter::isNumeric('010192029'));
		$this->assertTrue(SpoonFilter::isNumeric(1337));
		$this->assertFalse(SpoonFilter::isNumeric('I can has cheezeburger'));
	}

	public function testIsOdd()
	{
		$this->assertFalse(SpoonFilter::isOdd(0));
		$this->assertTrue(SpoonFilter::isOdd(1));
		$this->assertFalse(SpoonFilter::isOdd(10901920));
		$this->assertTrue(SpoonFilter::isOdd(-1337));
	}

	public function testIsSmallerThan()
	{
		$this->assertFalse(SpoonFilter::isSmallerThan(1, 10));
		$this->assertFalse(SpoonFilter::isSmallerThan(-10, -1));
		$this->assertFalse(SpoonFilter::isSmallerThan(-1, 10));
		$this->assertTrue(SpoonFilter::isSmallerThan(1, -10));
		$this->assertFalse(SpoonFilter::isSmallerThan(0, 0));
	}

	public function testIsString()
	{
		$this->assertTrue(SpoonFilter::isString('This should qualify as a string.'));
	}

	public function testIsValidAgainstRegexp()
	{
		$this->assertTrue(SpoonFilter::isValidAgainstRegexp('/(^[a-z]+$)/', 'alphabet'));
		$this->assertFalse(SpoonFilter::isValidAgainstRegexp('/(^[a-z]+$)/', 'alphabet my ass!'));
		$this->assertFalse(SpoonFilter::isValidAgainstRegexp('/(boobies)/', 'I like babies'));
	}

	public function testIsValidRegexp()
	{
		$this->assertTrue(SpoonFilter::isValidRegexp('/boobies/'));
	}

	public function testToCamelCase()
	{
		$this->assertEquals('SpoonLibraryRocks', SpoonFilter::toCamelCase('Spoon library rocks', ' '));
		$this->assertEquals('SpoonLibraryRocks', SpoonFilter::toCamelCase('spoon_library_Rocks'));
		$this->assertEquals('SpoonLibraryRocks', SpoonFilter::toCamelCase('spoon_libraryRocks'));
		$this->assertEquals('blaat', SpoonFilter::toCamelCase('Blaat', '_', true));
	}

	public function testReplaceURLsWithAnchors()
	{
		$tlds = array('ac', 'ad', 'ae', 'aero', 'af', 'ag', 'ai', 'al', 'am', 'an', 'ao', 'aq', 'ar', 'arpa', 'as', 'asia', 'at', 'au', 'aw', 'ax', 'az',
						'ba', 'bb', 'bd' ,'be', 'bf', 'bg', 'bh', 'bi', 'biz', 'bj', 'bm', 'bn', 'bo', 'br', 'bs', 'bt', 'bv', 'bw', 'by' ,'bz',
						'ca', 'cat', 'cc' ,'cd', 'cf', 'cg', 'ch', 'ci', 'ck', 'cl', 'cm', 'cn', 'co', 'com', 'coop', 'cr', 'cu', 'cv', 'cx', 'cy', 'cz',
						'de', 'dj', 'dk', 'dm', 'do', 'dz',
						'ec', 'edu', 'ee', 'eg', 'er', 'es', 'et', 'eu',
						'fi', 'fj', 'fk', 'fm', 'fo', 'fr',
						'ga', 'gb', 'gd', 'ge', 'gf', 'gg', 'gh', 'gi', 'gl', 'gm', 'gn', 'gov', 'gp', 'gq', 'gr', 'gs', 'gt', 'gu', 'gw', 'gy',
						'hk', 'hm', 'hn', 'hr', 'ht', 'hu',
						'id', 'ie', 'il', 'im', 'in', 'info', 'int', 'io', 'iq', 'ir', 'is', 'it',
						'je', 'jm', 'jo', 'jobs', 'jp',
						'ke', 'kg', 'kh', 'ki', 'km', 'kn', 'kp', 'kr', 'kw', 'ky', 'kz',
						'la', 'lb', 'lc', 'li', 'lk', 'local', 'lr', 'ls', 'lt', 'lu', 'lv', 'ly',
						'ma', 'mc', 'md', 'me', 'mg', 'mh', 'mil', 'mk', 'ml', 'mm', 'mn', 'mo', 'mobi', 'mp', 'mq', 'mr', 'ms', 'mt', 'mu', 'museum', 'mv', 'mw', 'mx', 'my', 'mz',
						'na', 'name', 'nc', 'ne', 'net', 'nf', 'ng', 'ni', 'nl', 'no', 'np', 'nr', 'nu', 'nz',
						'om', 'org',
						'pa', 'pe', 'pf', 'pg', 'ph', 'pk', 'pl', 'pm', 'pn', 'pr', 'pro', 'ps', 'pt', 'pw', 'py',
						'qa',
						're', 'ro', 'rs', 'ru', 'rw',
						'sa', 'sb', 'sc', 'sd', 'se', 'sg', 'sh', 'si', 'sj', 'sk', 'sl', 'sm', 'sn', 'so', 'sr', 'st', 'su', 'sv', 'sy', 'sz',
						'tc', 'td', 'tel', 'tf', 'tg', 'th', 'tj', 'tk', 'tl', 'tm', 'tn', 'to', 'tp', 'tr', 'travel','tt', 'tv', 'tw', 'tz',
						'ua', 'ug', 'uk', 'us', 'uy', 'uz',
						'va', 'vc', 've', 'vg', 'vi', 'vn', 'vu',
						'wf', 'ws',
						'ye', 'yt', 'yu',
						'za', 'zm', 'zw');

		foreach($tlds as $tld)
		{
			$this->assertEquals('verkeerde link: www.link.' . $tld . 'l', SpoonFilter::replaceURLsWithAnchors('verkeerde link: www.link.' . $tld . 'l', false));
			$this->assertEquals('zonder http: <a href="http://www.link.' . $tld . '">www.link.' . $tld . '</a>',SpoonFilter::replaceURLsWithAnchors('zonder http: www.link.' . $tld, false));

			$this->assertEquals('met http: <a href="http://www.link.' . $tld . '">http://www.link.' . $tld . '</a>', SpoonFilter::replaceURLsWithAnchors('met http: http://www.link.' . $tld, false));

			// port
			$this->assertEquals('verkeerde link: www.link.' . $tld . 'l:80', SpoonFilter::replaceURLsWithAnchors('verkeerde link: www.link.' . $tld . 'l:80', false));
			$this->assertEquals('zonder http: <a href="http://www.link.' . $tld . ':80">www.link.' . $tld . ':80</a>', SpoonFilter::replaceURLsWithAnchors('zonder http: www.link.' . $tld . ':80', false));
			$this->assertEquals('met http: <a href="http://www.link.' . $tld . ':80">http://www.link.' . $tld . ':80</a>', SpoonFilter::replaceURLsWithAnchors('met http: http://www.link.' . $tld . ':80', false));

			// querystring
			$this->assertEquals('verkeerde link: www.link.' . $tld . 'l?m=12&b=0%20d', SpoonFilter::replaceURLsWithAnchors('verkeerde link: www.link.' . $tld . 'l?m=12&b=0%20d', false));
			$this->assertEquals('zonder http: <a href="http://www.link.' . $tld . '?m=12&b=0%20d">www.link.' . $tld . '?m=12&b=0%20d</a>', SpoonFilter::replaceURLsWithAnchors('zonder http: www.link.' . $tld . '?m=12&b=0%20d', false));
			$this->assertEquals('met http: <a href="http://www.link.' . $tld . '?m=12&b=0%20d">http://www.link.' . $tld . '?m=12&b=0%20d</a>', SpoonFilter::replaceURLsWithAnchors('met http: http://www.link.' . $tld . '?m=12&b=0%20d', false));

			// folder
			$this->assertEquals('verkeerde link: www.link.' . $tld . 'l/mekker', SpoonFilter::replaceURLsWithAnchors('verkeerde link: www.link.' . $tld . 'l/mekker', false));
			$this->assertEquals('zonder http: <a href="http://www.link.' . $tld . '/mekker">www.link.' . $tld . '/mekker</a>', SpoonFilter::replaceURLsWithAnchors('zonder http: www.link.' . $tld . '/mekker', false));
			$this->assertEquals('met http: <a href="http://www.link.' . $tld . '/mekker">http://www.link.' . $tld . '/mekker</a>', SpoonFilter::replaceURLsWithAnchors('met http: http://www.link.' . $tld . '/mekker', false));
		}

		// no follow
		foreach($tlds as $tld)
		{
			$this->assertEquals('verkeerde link: www.link.' . $tld . 'l', SpoonFilter::replaceURLsWithAnchors('verkeerde link: www.link.' . $tld . 'l'));
			$this->assertEquals('zonder http: <a rel="nofollow" href="http://www.link.' . $tld . '">www.link.' . $tld . '</a>', SpoonFilter::replaceURLsWithAnchors('zonder http: www.link.' . $tld));
			$this->assertEquals('met http: <a rel="nofollow" href="http://www.link.' . $tld . '">http://www.link.' . $tld . '</a>', SpoonFilter::replaceURLsWithAnchors('met http: http://www.link.' . $tld));

			// port
			$this->assertEquals('verkeerde link: www.link.' . $tld . 'l:80', SpoonFilter::replaceURLsWithAnchors('verkeerde link: www.link.' . $tld . 'l:80'));
			$this->assertEquals('zonder http: <a rel="nofollow" href="http://www.link.' . $tld . ':80">www.link.' . $tld . ':80</a>', SpoonFilter::replaceURLsWithAnchors('zonder http: www.link.' . $tld . ':80'));
			$this->assertEquals('met http: <a rel="nofollow" href="http://www.link.' . $tld . ':80">http://www.link.' . $tld . ':80</a>', SpoonFilter::replaceURLsWithAnchors('met http: http://www.link.' . $tld . ':80'));

			// querystring
			$this->assertEquals('verkeerde link: www.link.' . $tld . 'l?m=12&b=0%20d', SpoonFilter::replaceURLsWithAnchors('verkeerde link: www.link.' . $tld . 'l?m=12&b=0%20d'));
			$this->assertEquals('zonder http: <a rel="nofollow" href="http://www.link.' . $tld . '?m=12&b=0%20d">www.link.' . $tld . '?m=12&b=0%20d</a>', SpoonFilter::replaceURLsWithAnchors('zonder http: www.link.' . $tld . '?m=12&b=0%20d'));
			$this->assertEquals('met http: <a rel="nofollow" href="http://www.link.' . $tld . '?m=12&b=0%20d">http://www.link.' . $tld . '?m=12&b=0%20d</a>', SpoonFilter::replaceURLsWithAnchors('met http: http://www.link.' . $tld . '?m=12&b=0%20d'));

			// folder
			$this->assertEquals('verkeerde link: www.link.' . $tld . 'l/mekker', SpoonFilter::replaceURLsWithAnchors('verkeerde link: www.link.' . $tld . 'l/mekker'));
			$this->assertEquals('zonder http: <a rel="nofollow" href="http://www.link.' . $tld . '/mekker">www.link.' . $tld . '/mekker</a>', SpoonFilter::replaceURLsWithAnchors('zonder http: www.link.' . $tld . '/mekker'));
			$this->assertEquals('met http: <a rel="nofollow" href="http://www.link.' . $tld . '/mekker">http://www.link.' . $tld . '/mekker</a>', SpoonFilter::replaceURLsWithAnchors('met http: http://www.link.' . $tld . '/mekker'));
		}	}

	public function testStripHTML()
	{
		$html = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Fork CMS</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
	<p>
		<a href="http://www.spoon-library.com">Spoon Library</a>
	</p>
</body>
</html>';

		$this->assertEquals('Spoon Library', SpoonFilter::stripHTML($html));
		$this->assertEquals('<a href="http://www.spoon-library.com">Spoon Library</a>', SpoonFilter::stripHTML($html, '<a>'));
		$this->assertEquals('Spoon Library (http://www.spoon-library.com)', SpoonFilter::stripHTML($html, null, true));
	}

	public function testUrlise()
	{
		$this->assertEquals(urlencode('géén-bananen'), SpoonFilter::urlise('géén bananen'));
		$this->assertEquals('tom-jerry', SpoonFilter::urlise('Tom & Jerry'));
		$this->assertEquals(urlencode('¬'), SpoonFilter::urlise('¬'));
	}
}
