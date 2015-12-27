<?php

namespace Frontend\Core\Tests;

use Common\WebTestCase;

class AjaxTest extends WebTestCase
{
    public function testAjaxWithoutModuleAndAction()
    {
        $client = static::createClient();

        $client->request('GET', '/frontend/ajax');
        $this->assertEquals(
            500,
            $client->getResponse()->getStatusCode()
        );
        $this->assertContains(
            'Module not correct',
            $client->getResponse()->getContent()
        );
    }

    public function testAjaxWithoutModule()
    {
        $client = static::createClient();

        $client->request('GET', '/frontend/ajax?action=Test');
        $this->assertEquals(
            500,
            $client->getResponse()->getStatusCode()
        );
        $this->assertContains(
            'Module not correct',
            $client->getResponse()->getContent()
        );
    }

    public function testAjaxWithInvalidModule()
    {
        $client = static::createClient();

        $client->request('GET', '/frontend/ajax?module=Test');
        $this->assertEquals(
            500,
            $client->getResponse()->getStatusCode()
        );
        $this->assertContains(
            'Module not correct',
            $client->getResponse()->getContent()
        );
    }

    public function testAjaxWithoutAction()
    {
        $client = static::createClient();

        $client->request('GET', '/frontend/ajax?module=Blog');
        $this->assertEquals(
            500,
            $client->getResponse()->getStatusCode()
        );
        $this->assertContains(
            'Internal error',
            $client->getResponse()->getContent()
        );
    }

    public function testAjaxWithInvalidAction()
    {
        $client = static::createClient();

        $client->request('GET', '/frontend/ajax?module=Blog&action=Test');
        $this->assertEquals(
            500,
            $client->getResponse()->getStatusCode()
        );
        $this->assertContains(
            'Internal error',
            $client->getResponse()->getContent()
        );
    }
}
