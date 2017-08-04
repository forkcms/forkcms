<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Common\WebTestCase;

class DetailModuleTest extends WebTestCase
{
    public function testAuthenticationIsNeeded(): void
    {
        $client = static::createClient();
        $this->logout($client);

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/extensions/detail_module?module=Blog');

        // we should get redirected to authentication with a reference to blog index in our url
        self::assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Fextensions%2Fdetail_module%3Fmodule%3DBlog',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testIndexHasModules(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->request('GET', '/private/en/extensions/detail_module?module=Blog');
        self::assertContains(
            'The Blog (you could also call it \'News\') module features',
            $client->getResponse()->getContent()
        );
        self::assertNotContains(
            'Authors ',
            $client->getResponse()->getContent()
        );

        self::assertContains(
            'Version',
            $client->getResponse()->getContent()
        );

        self::assertContains(
            'automatic recommendation of related articles',
            $client->getResponse()->getContent()
        );
    }
}
