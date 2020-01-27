<?php

namespace Backend\Modules\Dashboard\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class IndexTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        $this->assertAuthenticationIsNeeded($client, '/private/en/dashboard/index');
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
