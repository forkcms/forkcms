<?php

namespace Backend\Modules\Dashboard\Tests\Action;

use Common\WebTestCase;

class IndexTest extends WebTestCase
{
    public function testAuthenticationIsNeeded(): void
    {
        $client = static::createClient();
        $this->logout($client);

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/dashboard/index');

        // we should get redirected to authentication with a reference to blog index in our url
        self::assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Fdashboard%2Findex',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testIndexHasWidgets(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->request('GET', '/private/en/dashboard/index');
        self::assertContains(
            'Blog: Latest comments',
            $client->getResponse()->getContent()
        );
        self::assertContains(
            'FAQ: Feedback',
            $client->getResponse()->getContent()
        );
        self::assertContains(
            'Analysis',
            $client->getResponse()->getContent()
        );
        self::assertContains(
            'Users: Statistics',
            $client->getResponse()->getContent()
        );
    }
}
