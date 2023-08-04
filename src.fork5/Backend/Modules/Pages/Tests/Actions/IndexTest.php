<?php

namespace Backend\Modules\Pages\Tests\Actions;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

final class IndexTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        self::assertAuthenticationIsNeeded($client, '/private/en/pages/index');
    }

    public function testIndexContainsPages(Client $client): void
    {
        $this->login($client);

        self::assertPageLoadedCorrectly(
            $client,
            '/private/en/pages/page_index',
            [
                'Home',
                'Add page',
                'Recently edited',
            ]
        );
    }
}
