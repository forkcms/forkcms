<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Common\WebTestCase;

class ThemeTemplatesTest extends WebTestCase
{
    public function testAuthenticationIsNeeded(): void
    {
        $client = static::createClient();
        $this->logout($client);

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/extensions/theme_templates');

        // we should get redirected to authentication with a reference to blog index in our url
        self::assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Fextensions%2Ftheme_templates',
            $client->getHistory()->current()->getUri()
        );
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
