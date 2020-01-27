<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class ThemesTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        $this->assertAuthenticationIsNeeded($client, '/private/en/extensions/themes');
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
