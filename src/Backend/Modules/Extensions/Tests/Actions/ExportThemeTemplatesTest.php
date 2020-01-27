<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class ExportThemeTemplatesTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        $this->assertAuthenticationIsNeeded($client, '/private/en/extensions/export_theme_templates');
    }

    public function testExportIsReturned(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->request('GET', '/private/en/extensions/export_theme_templates');
        self::assertContains(
            '<template label="Default" path="Core/Layout/Templates/Default.html.twig">',
            $client->getResponse()->getContent()
        );
        self::assertContains(
            '<?xml version="1.0" encoding="UTF-8"?>',
            $client->getResponse()->getContent()
        );
        self::assertContains(
            '[/,/,top,top],[/,/,/,/]',
            $client->getResponse()->getContent()
        );
    }
}
