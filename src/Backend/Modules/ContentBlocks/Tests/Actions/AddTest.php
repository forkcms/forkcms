<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class AddTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        $client = static::createClient();
        $this->assertAuthenticationIsNeeded($client, '/private/en/content_blocks/index');
    }

    public function testFormIsDisplayed(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->request('GET', '/private/en/content_blocks/add');
        self::assertContains(
            'Title<abbr data-toggle="tooltip" aria-label="Required field" title="Required field">*</abbr>',
            $client->getResponse()->getContent()
        );
        self::assertContains(
            'Visible on site',
            $client->getResponse()->getContent()
        );
        self::assertContains(
            'Add content block',
            $client->getResponse()->getContent()
        );
    }
}
