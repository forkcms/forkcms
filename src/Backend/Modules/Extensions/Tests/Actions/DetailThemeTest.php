<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class DetailThemeTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        $this->assertAuthenticationIsNeeded($client, '/private/en/extensions/detail_theme?theme=Fork');
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
