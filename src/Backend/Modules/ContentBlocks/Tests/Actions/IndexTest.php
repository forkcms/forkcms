<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class IndexTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        $this->assertAuthenticationIsNeeded($client, '/private/en/content_blocks/index');
    }

    public function testIndexHasNoItems(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->request('GET', '/private/en/content_blocks/index');
        self::assertContains(
            'There are no items yet.',
            $client->getResponse()->getContent()
        );

        // some stuff we also want to see on the content block index
        self::assertContains(
            'Add content block',
            $client->getResponse()->getContent()
        );
    }
}
