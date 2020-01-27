<?php

namespace Backend\Modules\Pages\Tests\Actions;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

final class IndexTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        $this->assertAuthenticationIsNeeded($client, '/private/en/pages/index');
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
