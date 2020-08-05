<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class ModulesTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        self::assertAuthenticationIsNeeded($client, '/private/en/extensions/modules');
    }

    public function testIndexHasModules(Client $client): void
    {
        $this->login($client);

        self::assertPageLoadedCorrectly(
            $client,
            '/private/en/extensions/modules',
            [
                'Installed modules',
                'Upload module',
                'Find modules',
            ]
        );
        self::assertResponseDoesNotHaveContent($client->getResponse(), 'Not installed modules');
    }
}
