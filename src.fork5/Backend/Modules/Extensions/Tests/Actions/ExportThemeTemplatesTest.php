<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class ExportThemeTemplatesTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        self::assertAuthenticationIsNeeded($client, '/private/en/extensions/export_theme_templates');
    }

    public function testExportIsReturned(Client $client): void
    {
        $this->login($client);

        self::assertPageLoadedCorrectly(
            $client,
            '/private/en/extensions/export_theme_templates',
            [
                '<template label="Default" path="Core/Layout/Templates/Default.html.twig">',
                '<?xml version="1.0" encoding="UTF-8"?>',
                '[/,/,top,top],[/,/,/,/]',
            ]
        );
    }
}
