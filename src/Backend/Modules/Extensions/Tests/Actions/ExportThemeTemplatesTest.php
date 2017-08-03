<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Common\WebTestCase;

class ExportThemeTemplatesTest extends WebTestCase
{
    public function testAuthenticationIsNeeded(): void
    {
        $client = static::createClient();
        $this->logout($client);

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/extensions/export_theme_templates');

        // we should get redirected to authentication with a reference to blog index in our url
        self::assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Fextensions%2Fexport_theme_templates',
            $client->getHistory()->current()->getUri()
        );
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
