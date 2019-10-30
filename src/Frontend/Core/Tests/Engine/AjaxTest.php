<?php

namespace Frontend\Core\Tests\Engine;

use Common\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AjaxTest extends WebTestCase
{
    public function testAjaxWithoutModuleAndAction(): void
    {
        $client = static::createClient();

        $client->request('GET', '/frontend/ajax');

        self::assertEquals(
            Response::HTTP_FORBIDDEN,
            $client->getResponse()->getStatusCode()
        );
        self::assertContains(
            'Module not allowed',
            $client->getResponse()->getContent()
        );
    }

    public function testAjaxWithoutModule(): void
    {
        $client = static::createClient();

        $client->request('GET', '/frontend/ajax?action=Test');
        self::assertEquals(
            Response::HTTP_FORBIDDEN,
            $client->getResponse()->getStatusCode()
        );
        self::assertContains(
            'Module not allowed',
            $client->getResponse()->getContent()
        );
    }

    public function testAjaxWithInvalidModule(): void
    {
        $client = static::createClient();

        $client->request('GET', '/frontend/ajax?module=Test');
        self::assertEquals(
            Response::HTTP_FORBIDDEN,
            $client->getResponse()->getStatusCode()
        );
        self::assertContains(
            'Module not allowed',
            $client->getResponse()->getContent()
        );
    }

    public function testAjaxWithoutAction(): void
    {
        $client = static::createClient();

        $client->request('GET', '/frontend/ajax?module=Blog');
        self::assertEquals(
            Response::HTTP_BAD_REQUEST,
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
            Response::HTTP_BAD_REQUEST,
            $client->getResponse()->getStatusCode()
        );
        self::assertContains(
            'Action class Frontend\\\\Modules\\\\Blog\\\\Ajax\\\\Test does not exist',
            $client->getResponse()->getContent()
        );
    }
}
