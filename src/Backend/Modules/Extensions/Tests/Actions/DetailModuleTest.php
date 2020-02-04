<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class DetailModuleTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        self::assertAuthenticationIsNeeded($client, '/private/en/extensions/detail_module?module=Blog');
    }

    public function testIndexHasModules(Client $client): void
    {
        $this->login($client);

        self::assertPageLoadedCorrectly(
            $client,
            '/private/en/extensions/detail_module?module=Blog',
            [
                'The Blog (you could also call it \'News\') module features',
                'Version',
                'automatic recommendation of related articles',
            ]
        );
        self::assertResponseDoesNotHaveContent($client->getResponse(), 'Authors ');
    }
}
