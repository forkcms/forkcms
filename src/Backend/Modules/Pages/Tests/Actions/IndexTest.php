<?php

namespace Backend\Modules\Pages\Tests\Actions;

use Common\WebTestCase;

final class IndexTest extends WebTestCase
{
    public function testAuthenticationIsNeeded(): void
    {
        $client = static::createClient();
        $this->logout($client);

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/pages/index');

        // we should get redirected to authentication with a reference to blog index in our url
        self::assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Fpages%2Findex',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testIndexContainsPages(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->request('GET', '/private/en/pages/index');

        self::assertContains(
            'Home',
            $client->getResponse()->getContent()
        );

        // some stuff we also want to see on the blog index
        self::assertContains(
            'Add page',
            $client->getResponse()->getContent()
        );
        self::assertContains(
            'Recently edited',
            $client->getResponse()->getContent()
        );
    }
}
