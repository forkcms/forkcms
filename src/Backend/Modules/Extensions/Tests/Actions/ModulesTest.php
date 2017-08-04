<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Common\WebTestCase;

class ModulesTest extends WebTestCase
{
    public function testAuthenticationIsNeeded(): void
    {
        $client = static::createClient();
        $this->logout($client);

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/extensions/modules');

        // we should get redirected to authentication with a reference to blog index in our url
        self::assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Fextensions%2Fmodules',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testIndexHasModuels(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->request('GET', '/private/en/extensions/modules');
        self::assertContains(
            'Installed modules',
            $client->getResponse()->getContent()
        );
        self::assertNotContains(
            'Not installed modules',
            $client->getResponse()->getContent()
        );

        self::assertContains(
            'Upload module',
            $client->getResponse()->getContent()
        );

        self::assertContains(
            'Find modules',
            $client->getResponse()->getContent()
        );
    }
}
