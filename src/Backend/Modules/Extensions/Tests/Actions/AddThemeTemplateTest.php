<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class AddThemeTemplateTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        $this->assertAuthenticationIsNeeded($client, '/private/en/extensions/add_theme_template');
    }

    public function testFormIsDisplayed(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->request('GET', '/private/en/extensions/add_theme_template');
        self::assertContains(
            'Allow the user to upload an image.',
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
