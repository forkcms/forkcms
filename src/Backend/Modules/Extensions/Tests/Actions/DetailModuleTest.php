<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class DetailModuleTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        $this->assertAuthenticationIsNeeded($client, '/private/en/extensions/detail_module?module=Blog');
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
