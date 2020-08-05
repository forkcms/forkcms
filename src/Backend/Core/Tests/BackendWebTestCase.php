<?php

namespace Backend\Core\Tests;

use Common\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Session\Session;

abstract class BackendWebTestCase extends WebTestCase
{
    /**
     * Make sure we are no longer logged-in
     */
    protected function tearDown(): void
    {
        $client = $this->getProvidedData()[0] ?? null;
        if ($client instanceof Client) {
            $this->logout($client);
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (!defined('APPLICATION')) {
            define('APPLICATION', 'Backend');
        }
    }

    protected function assertAuthenticationIsNeeded(Client $client, string $url, string $method = 'GET'): void
    {
        // make sure we aren't logged in with the client
        $this->logout($client);

        self::assertGetsRedirected(
            $client,
            $url,
            '/private/en/authentication?querystring=' . rawurlencode($url),
            $method
        );
    }

    protected function appendCsrfTokenToUrl(Client $client, string $url): string
    {
        $connectionSymbol = (strpos($url, '?') !== false) ? '&' : '?';

        $session = $client->getContainer()->get('session');

        if (!$session instanceof Session) {
            // no session so no csrf token
            return $url;
        }

        return $url . $connectionSymbol . 'token=' . $session->get('csrf_token');
    }
}
