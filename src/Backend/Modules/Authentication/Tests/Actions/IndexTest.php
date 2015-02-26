<?php

namespace Backend\Modules\Authentication\Tests\Action;

use Common\WebTestCase;

class IndexTest extends WebTestCase
{
    public function testPrivateRedirectsToAuthentication()
    {
        $client = static::createClient();
        $client->followRedirects();
        $this->loadFixtures($client);

        $client->request('GET', '/private');

        $this->assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testAuthenticationIndexWorks()
    {
        $client = static::createClient();

        $client->request('GET', '/private/en/authentication');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
    }
}
