<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class ThemesTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        self::assertAuthenticationIsNeeded($client, '/private/en/extensions/themes');
    }

    public function testIndexHasModules(Client $client): void
    {
        $this->login($client);

        self::assertPageLoadedCorrectly(
            $client,
            '/private/en/extensions/themes',
            [
                'Installed themes',
                'Upload theme',
                'Find themes',
            ]
        );
        self::assertResponseDoesNotHaveContent($client->getResponse(), 'Not installed themes');
    }
}
