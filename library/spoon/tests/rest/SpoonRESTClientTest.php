<?php

if(!defined('SPOON_CHARSET')) define('SPOON_CHARSET', 'utf-8');

$includePath = dirname(dirname(dirname(dirname(__FILE__))));
set_include_path(get_include_path() . PATH_SEPARATOR . $includePath);

require_once 'spoon/spoon.php';
require_once 'PHPUnit/Framework/TestCase.php';

class SpoonRESTClientTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var SpoonRESTClient
	 */
	protected  $SpoonRESTClient;

	protected function setUp()
	{
		// create instance
		$this->SpoonRESTClient = new SpoonRESTClient();
	}

	public function testExecute()
	{
		// parameters
		$parameters['method'] = 'artist.getinfo';
		$parameters['artist'] = 'Northern kings';
		$parameters['api_key'] = 'b25b959554ed76058ac220b7b2e0a026';

		// claa
		$response = $this->SpoonRESTClient->execute('http://ws.audioscrobbler.com/2.0/', $parameters);

		// load response as XML
		$xml = simplexml_load_string($response);

		$this->assertEquals('lfm', $xml->getName());
	}

	public function testHeaders()
	{
		// init var
		$headers = array('X-Test' => 'test');

		// set
		$this->SpoonRESTClient->setCustomHeader($headers);

		// get
		$this->assertEquals($headers, $this->SpoonRESTClient->getCustomHeaders());
	}

	public function testPort()
	{
		$port = 8080;

		// set
		$this->SpoonRESTClient->setPort($port);

		// get
		$this->assertEquals($port, $this->SpoonRESTClient->getPort());
	}

	public function testTimeOut()
	{
		$seconds = 20;

		// set
		$this->SpoonRESTClient->setTimeOut($seconds);

		// get
		$this->assertEquals($seconds, $this->SpoonRESTClient->getTimeOut());
	}

	public function testUserAgent()
	{
		$userAgent = 'Tijs';

		// set
		$this->SpoonRESTClient->setUserAgent($userAgent);

		// get
		$this->assertEquals('Spoon ' . SPOON_VERSION . '/' . $userAgent, $this->SpoonRESTClient->getUserAgent());
	}
}
