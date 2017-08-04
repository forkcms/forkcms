<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Common\WebTestCase;

class ThemesTest extends WebTestCase
{
    public function testAuthenticationIsNeeded(): void
    {
        $client = static::createClient();
        $this->logout($client);

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/extensions/themes');

        // we should get redirected to authentication with a reference to blog index in our url
        self::assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Fextensions%2Fthemes',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testIndexHasModules(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->request('GET', '/private/en/extensions/themes');
        self::assertContains(
            'Installed themes',
            $client->getResponse()->getContent()
        );
        self::assertNotContains(
            'Not installed themes',
            $client->getResponse()->getContent()
        );

        self::assertContains(
            'Upload theme',
            $client->getResponse()->getContent()
        );

        self::assertContains(
            'Find themes',
            $client->getResponse()->getContent()
        );
    }
}
