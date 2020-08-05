<?php

namespace Backend\Modules\Analytics\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class ResetTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        self::assertAuthenticationIsNeeded($client, '/private/en/analytics/reset');
    }

    public function testAfterResetRedirectToSettings(Client $client): void
    {
        $this->login($client);

        self::assertGetsRedirected(
            $client,
            '/private/en/analytics/reset',
            '/private/en/analytics/settings'
        );
    }
}
