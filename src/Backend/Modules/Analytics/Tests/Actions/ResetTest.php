<?php

namespace Backend\Modules\Analytics\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class ResetTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        $this->assertAuthenticationIsNeeded($client, '/private/en/analytics/reset');
    }

    public function testAfterResetRedirectToSettings(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/analytics/reset');

        // we should have been redirected to the settings page after the reset
        self::assertContains(
            '/private/en/analytics/settings',
            $client->getHistory()->current()->getUri()
        );
    }
}
