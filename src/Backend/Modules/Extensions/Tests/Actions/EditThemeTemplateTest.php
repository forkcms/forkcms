<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Common\WebTestCase;

class EditThemeTemplateTest extends WebTestCase
{
    public function testAuthenticationIsNeeded(): void
    {
        $client = static::createClient();
        $this->logout($client);

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/extensions/edit_theme_template?token=68ozixmy4j&id=3');

        // we should get redirected to authentication with a reference to blog index in our url
        self::assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Fextensions%2Fedit_theme_template%3Ftoken%3D68ozixmy4j%26id%3D3',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testFormIsDisplayed(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->request('GET', '/private/en/extensions/edit_theme_template?token=68ozixmy4j&id=3');
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
