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

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/analytics/index');

        // we should have been redirected to the settings page because the module isn't configured
        self::assertContains(
            '/private/en/analytics/settings',
            $client->getHistory()->current()->getUri()
        );
    }
}
