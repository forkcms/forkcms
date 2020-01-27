<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class EditThemeTemplateTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        $this->assertAuthenticationIsNeeded($client, '/private/en/extensions/edit_theme_template?token=68ozixmy4j&id=3');
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
