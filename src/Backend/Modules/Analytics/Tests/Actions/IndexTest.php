<?php

namespace Backend\Modules\Analytics\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class IndexTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        $this->assertAuthenticationIsNeeded($client, '/private/en/analytics/index');
    }

    public function testRedirectToSettingsActionWhenTheAnalyticsModuleIsNotConfigured(Client $client): void
    {
        $this->login($client);

        $this->assertGetsRedirected(
            $client,
            '/private/en/analytics/index',
            '/private/en/analytics/settings'
        );
    }
}
