<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Common\WebTestCase;

class DetailThemeTest extends WebTestCase
{
    public function testAuthenticationIsNeeded(): void
    {
        $client = static::createClient();
        $this->logout($client);

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/extensions/detail_theme?theme=Fork');

        // we should get redirected to authentication with a reference to blog index in our url
        self::assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Fextensions%2Fdetail_theme%3Ftheme%3DFork',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testIndexHasModules(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->request('GET', '/private/en/extensions/detail_theme?theme=Fork');
        self::assertContains(
            'Core/Layout/Templates/Home.html.twig',
            $client->getResponse()->getContent()
        );
        self::assertContains(
            'class="positions">top, main',
            $client->getResponse()->getContent()
        );

        self::assertContains(
            'Version',
            $client->getResponse()->getContent()
        );

        self::assertContains(
            'Description',
            $client->getResponse()->getContent()
        );
    }
}
