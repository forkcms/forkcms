<?php

namespace Backend\Modules\Analytics\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class SettingsTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        $this->assertAuthenticationIsNeeded($client, '/private/en/analytics/settings');
    }

    public function testAnalyticsSettingsWorks(Client $client): void
    {
        $this->login($client);

        $crawler = $client->request('GET', '/private/en/analytics/settings');

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertContains('How to get your secret file?', $crawler->html());
    }
}
