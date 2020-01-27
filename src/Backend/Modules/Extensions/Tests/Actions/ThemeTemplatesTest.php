<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class ThemeTemplatesTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        $this->assertAuthenticationIsNeeded($client, '/private/en/extensions/theme_templates');
    }

    public function testIndexHasTemplates(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->request('GET', '/private/en/extensions/theme_templates');
        self::assertContains(
            'Templates for',
            $client->getResponse()->getContent()
        );
        self::assertNotContains(
            '<a href="/private/en/extensions/edit_theme_template?token=68ozixmy4j&amp;id=3" title="">Default</a>',
            $client->getResponse()->getContent()
        );

        self::assertContains(
            'Add template',
            $client->getResponse()->getContent()
        );
        self::assertContains(
            'Export',
            $client->getResponse()->getContent()
        );
    }
}
