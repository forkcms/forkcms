<?php

require_once __DIR__ . '/../../autoload.php';


use Backend\Core\Engine\Model as BackendModel;


/**
 * Tests the building of the regular expression in invalidateFrontendCache
 *
 * @author <per@wijs.be>
 * @author <wouter@wijs.be>
 */
class InvalidateCacheRegexTest extends PHPUnit_Framework_TestCase
{
	private $siteId = 7;
	private $module = 'mymodule';
	private $language = 'nl';

	function test_GivenModuleLanguageAndSiteId_ReturnFullRegex()
	{
		$expected = '/'
			. $this->siteId . '_'
			. $this->language . '_'
			. $this->module
			. '(.*)_cache\.tpl/i';
		$this->assertEquals(
			$expected,
			BackendModel::buildInvalidateCacheRegex(
				$this->module, $this->language, $this->siteId
			)
		);
	}

	function test_GivenModuleAndLanguage_ReturnCorrectRegex()
	{
		$expected = '/(.*)'
			. $this->language . '_'
			. $this->module
			. '(.*)_cache\.tpl/i';
		$this->assertEquals(
			$expected,
			BackendModel::buildInvalidateCacheRegex(
				$this->module, $this->language, null
			)
		);
	}

	function test_GivenModuleAndSiteId_ReturnCorrectRegex()
	{
		$expected = '/'
			. $this->siteId
			. '(.*)'
			. $this->module
			. '(.*)_cache\.tpl/i';
		$this->assertEquals(
			$expected,
			BackendModel::buildInvalidateCacheRegex(
				$this->module, null, $this->siteId
			)
		);
	}

	function test_GivenOnlyModule_ReturnCorrectRegex()
	{
		$expected = '/'
			. '(.*)'
			. $this->module
			. '(.*)_cache\.tpl/i';
		$this->assertEquals(
			$expected,
			BackendModel::buildInvalidateCacheRegex(
				$this->module, null, null
			)
		);
	}

	function test_GivenOnlyLanguage_ReturnCorrectRegex()
	{
		$expected = '/'
			. '(.*)'
			. $this->language
			. '(.*)_cache\.tpl/i';
		$this->assertEquals(
			$expected,
			BackendModel::buildInvalidateCacheRegex(
				null, $this->language, null
			)
		);
	}

	function test_GivenOnlySiteId_ReturnCorrectRegex()
	{
		$expected = '/'
			. $this->siteId
			. '(.*)_cache\.tpl/i';
		$this->assertEquals(
			$expected,
			BackendModel::buildInvalidateCacheRegex(
				null, null, $this->siteId
			)
		);
	}
}

