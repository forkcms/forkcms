<?php

namespace Backend\Modules\Error\Tests\Action;

use Common\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class IndexTest extends WebTestCase
{
    public function testAuthenticationIsNotNeeded(): void
    {
        $client = static::createClient();
        $this->logout($client);

        $client->request('GET', '/private/en/error/index');

        // we should get redirected to authentication with a reference to blog index in our url
        self::assertStringEndsWith(
            '/private/en/error/index',
            $client->getHistory()->current()->getUri()
        );

        self::assertEquals(
            Response::HTTP_BAD_REQUEST,
            $client->getResponse()->getStatusCode()
        );
    }

    public function testModuleNotAllowed(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->request('GET', '/private/en/error/index?type=module-not-allowed');
        self::assertContains(
            'You have insufficient rights for this module.',
            $client->getResponse()->getContent()
        );
        self::assertEquals(
            Response::HTTP_FORBIDDEN,
            $client->getResponse()->getStatusCode()
        );
    }

    public function testActionNotAllowed(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->request('GET', '/private/en/error/index?type=action-not-allowed');
        self::assertContains(
            'You have insufficient rights for this action.',
            $client->getResponse()->getContent()
        );
        self::assertEquals(
            Response::HTTP_FORBIDDEN,
            $client->getResponse()->getStatusCode()
        );
    }
    public function testNotFound(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->request('GET', '/private/en/error/index?type=not-found');
        self::assertEquals(
            Response::HTTP_NOT_FOUND,
            $client->getResponse()->getStatusCode()
        );
    }
}
