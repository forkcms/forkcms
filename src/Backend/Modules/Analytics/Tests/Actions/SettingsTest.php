<?php

namespace Backend\Modules\Analytics\Tests\Action;

use Common\WebTestCase;

class SettingsTest extends WebTestCase
{
    public function testAuthenticationIsNeeded(): void
    {
        $client = static::createClient();
        $this->logout($client);

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/analytics/settings');

        // we should get redirected to authentication with a reference to the wanted page
        self::assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Fanalytics%2Fsettings',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testAnalyticsSettingsWorks(): void
    {
        $client = static::createClient();
        $this->login($client);

        $crawler = $client->request('GET', '/private/en/analytics/settings');

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertContains('How to get your secret file?', $crawler->html());
    }
}
