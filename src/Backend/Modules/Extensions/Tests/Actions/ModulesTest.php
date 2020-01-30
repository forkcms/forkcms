<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class ModulesTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        $this->assertAuthenticationIsNeeded($client, '/private/en/extensions/modules');
    }

    public function testIndexHasModules(Client $client): void
    {
        $this->login($client);

        $this->assertPageLoadedCorrectly(
            $client,
            '/private/en/extensions/modules',
            [
                'Installed modules',
                'Upload module',
                'Find modules',
            ]
        );
        $this->assertResponseDoesNotHaveContent($client->getResponse(), 'Not installed modules');
    }
}
