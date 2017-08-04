<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Common\WebTestCase;

class AddThemeTemplateTest extends WebTestCase
{
    public function testAuthenticationIsNeeded(): void
    {
        $client = static::createClient();
        $this->logout($client);

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/extensions/add_theme_template');

        // we should get redirected to authentication with a reference to blog index in our url
        self::assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Fextensions%2Fadd_theme_template',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testFormIsDisplayed(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->request('GET', '/private/en/extensions/add_theme_template');
        self::assertContains(
            'The user can upload a file.',
            $client->getResponse()->getContent()
        );
        self::assertContains(
            'Positions',
            $client->getResponse()->getContent()
        );
        self::assertContains(
            'If you want a position to display wider or higher in it\'s graphical representation',
            $client->getResponse()->getContent()
        );
    }
}
