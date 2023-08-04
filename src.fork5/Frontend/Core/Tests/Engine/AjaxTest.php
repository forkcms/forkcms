<?php

namespace Frontend\Core\Tests\Engine;

use Frontend\Core\Tests\FrontendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;

class AjaxTest extends FrontendWebTestCase
{
    public function testAjaxWithoutModuleAndAction(Client $client): void
    {
        self::assertPageLoadedCorrectly(
            $client,
            '/frontend/ajax',
            ['Module not allowed'],
            Response::HTTP_FORBIDDEN
        );
    }

    public function testAjaxWithoutModule(Client $client): void
    {
        self::assertPageLoadedCorrectly(
            $client,
            '/frontend/ajax?action=Test',
            ['Module not allowed'],
            Response::HTTP_FORBIDDEN
        );
    }

    public function testAjaxWithInvalidModule(Client $client): void
    {
        self::assertPageLoadedCorrectly(
            $client,
            '/frontend/ajax?module=Test',
            ['Module not allowed'],
            Response::HTTP_FORBIDDEN
        );
    }

    public function testAjaxWithoutAction(Client $client): void
    {
        self::assertPageLoadedCorrectly(
            $client,
            '/frontend/ajax?module=Blog',
            ['Action class Frontend\\\\Modules\\\\Blog\\\\Ajax\\\\ does not exist'],
            Response::HTTP_BAD_REQUEST
        );
    }

    public function testAjaxWithInvalidAction(Client $client): void
    {
        self::assertPageLoadedCorrectly(
            $client,
            '/frontend/ajax?module=Blog&action=Test',
            ['Action class Frontend\\\\Modules\\\\Blog\\\\Ajax\\\\Test does not exist'],
            Response::HTTP_BAD_REQUEST
        );
    }

    public function testAjaxWithValidAction(Client $client): void
    {
        self::assertPageLoadedCorrectly(
            $client,
            '/frontend/ajax?module=Search&action=Autosuggest',
            ['"title":"Search","url":"search"'],
            Response::HTTP_OK,
            'POST',
            [
                'term' => 'Sear',
            ]
        );
    }
}
