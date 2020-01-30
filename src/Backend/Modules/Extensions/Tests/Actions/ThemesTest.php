<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class ThemesTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        $this->assertAuthenticationIsNeeded($client, '/private/en/extensions/themes');
    }

    public function testIndexHasModules(Client $client): void
    {
        $this->login($client);

        $this->assertPageLoadedCorrectly(
            $client,
            '/private/en/extensions/themes',
            [
                'Installed themes',
                'Upload theme',
                'Find themes',
            ]
        );
        $this->assertResponseDoesNotHaveContent($client->getResponse(), 'Not installed themes');
    }
}
