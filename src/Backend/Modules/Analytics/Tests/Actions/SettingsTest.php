<?php

namespace Backend\Modules\Analytics\Tests\Action;

use Common\WebTestCase;

class SettingsTest extends WebTestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testAuthenticationIsNeeded()
    {
        $this->logout();
        $client = static::createClient();

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/analytics/settings');

        // we should get redirected to authentication with a reference to the wanted page
        self::assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Fanalytics%2Fsettings',
            $client->getHistory()->current()->getUri()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testAnalyticsSettingsWorks()
    {
        $client = static::createClient();
        $this->login();

        $crawler = $client->request('GET', '/private/en/analytics/settings');

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertContains('How to get your secret file?', $crawler->html());
    }
}
