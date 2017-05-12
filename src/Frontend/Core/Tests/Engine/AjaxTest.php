<?php

namespace Frontend\Core\Tests;

use Common\WebTestCase;

class AjaxTest extends WebTestCase
{
    public function testAjaxWithoutModuleAndAction(): void
    {
        $client = static::createClient();

        $client->request('GET', '/frontend/ajax');
        self::assertEquals(
            500,
            $client->getResponse()->getStatusCode()
        );
        self::assertContains(
            'Module not correct',
            $client->getResponse()->getContent()
        );
    }

    public function testAjaxWithoutModule(): void
    {
        $client = static::createClient();

        $client->request('GET', '/frontend/ajax?action=Test');
        self::assertEquals(
            500,
            $client->getResponse()->getStatusCode()
        );
        self::assertContains(
            'Module not correct',
            $client->getResponse()->getContent()
        );
    }

    public function testAjaxWithInvalidModule(): void
    {
        $client = static::createClient();

        $client->request('GET', '/frontend/ajax?module=Test');
        self::assertEquals(
            500,
            $client->getResponse()->getStatusCode()
        );
        self::assertContains(
            'Module not correct',
            $client->getResponse()->getContent()
        );
    }

    public function testAjaxWithoutAction(): void
    {
        $client = static::createClient();

        $client->request('GET', '/frontend/ajax?module=Blog');
        self::assertEquals(
            500,
            $client->getResponse()->getStatusCode()
        );
        self::assertContains(
            'Action class Frontend\\\\Modules\\\\Blog\\\\Ajax\\\\ does not exist',
            $client->getResponse()->getContent()
        );
    }

    public function testAjaxWithInvalidAction(): void
    {
        $client = static::createClient();

        $client->request('GET', '/frontend/ajax?module=Blog&action=Test');
        self::assertEquals(
            500,
            $client->getResponse()->getStatusCode()
        );
        self::assertContains(
            'Action class Frontend\\\\Modules\\\\Blog\\\\Ajax\\\\Test does not exist',
            $client->getResponse()->getContent()
        );
    }
}
