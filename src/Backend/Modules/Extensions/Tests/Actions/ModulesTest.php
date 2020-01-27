<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class ModulesTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        $this->assertAuthenticationIsNeeded($client, '/private/en/extensions/modules');
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
