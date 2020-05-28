<?php

namespace Backend\Modules\Error\Tests\Action;

use Backend\Core\Language\Language;
use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;

class IndexTest extends BackendWebTestCase
{
    public function testAuthenticationIsNotNeeded(Client $client): void
    {
        $this->logout($client);

        self::assertHttpStatusCode($client, '/private/en/error/index', Response::HTTP_BAD_REQUEST);
        self::assertCurrentUrlEndsWith($client, '/private/en/error/index');
    }

    public function testModuleNotAllowed(Client $client): void
    {
        self::assertPageLoadedCorrectly(
            $client,
            '/private/en/error/index?type=module-not-allowed',
            [
                'You have insufficient rights for this module.',
            ],
            Response::HTTP_FORBIDDEN
        );
    }

    public function testActionNotAllowed(Client $client): void
    {
        self::assertPageLoadedCorrectly(
            $client,
            '/private/en/error/index?type=action-not-allowed',
            [
                'You have insufficient rights for this action.',
            ],
            Response::HTTP_FORBIDDEN
        );
    }

    public function testNotFound(Client $client): void
    {
        Language::setLocale('en');
        self::assertPageLoadedCorrectly(
            $client,
            '/private/en/error/index?type=not-found',
            [
                Language::err('NotFound', 'Error'),
            ],
            Response::HTTP_NOT_FOUND
        );
    }
}
