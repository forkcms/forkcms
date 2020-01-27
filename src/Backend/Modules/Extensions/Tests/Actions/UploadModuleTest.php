<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class UploadModuleTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        $this->assertAuthenticationIsNeeded($client, '/private/en/extensions/upload_module');
    }

    public function testUploadPage(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->request('GET', '/private/en/extensions/upload_module');
        self::assertContains(
            'Install',
            $client->getResponse()->getContent()
        );

        self::assertContains(
            '<label for="file" class="control-label">',
            $client->getResponse()->getContent()
        );
    }
}
