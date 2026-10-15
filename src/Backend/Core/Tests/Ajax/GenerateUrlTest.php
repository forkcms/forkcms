<?php

namespace Backend\Core\Tests\Ajax;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

/**
 * Regression test for the unsafe-reflection vulnerability (CWE-470) in the Core/GenerateUrl AJAX action:
 * the client-supplied class/method must no longer be usable to reach arbitrary services or methods
 * (e.g. the public "database" service -> SpoonDatabase::update to self-promote to is_god).
 */
class GenerateUrlTest extends BackendWebTestCase
{
    private const AJAX_URL = '/backend/ajax';

    public function testLegitimateModuleCallbackStillGeneratesAUrl(KernelBrowser $client): void
    {
        $this->login($client);

        $response = $this->postGenerateUrl(
            $client,
            [
                'className' => \Backend\Modules\Blog\Engine\Model::class,
                'methodName' => 'getUrl',
                'url' => 'Some Example Title',
                'parameters' => '',
            ]
        );

        self::assertSame(200, $response->getStatusCode());

        $payload = json_decode($response->getContent(), true);
        self::assertSame(200, $payload['code']);
        self::assertSame('some-example-title', $payload['data']);
    }

    public function testArbitraryServiceCallbackIsRejected(KernelBrowser $client): void
    {
        $this->login($client);

        // the original exploit: className=database, methodName=update, with the serialized arguments
        // [ ['is_god' => 1], 'id = 2' ] -> would run UPDATE users SET is_god = 1 WHERE id = 2
        $response = $this->postGenerateUrl(
            $client,
            [
                'className' => 'database',
                'methodName' => 'update',
                'url' => 'users',
                'parameters' => 'a:2:{i:0;a:1:{s:6:"is_god";i:1;}i:1;s:6:"id = 2";}',
            ]
        );

        self::assertNotSame(200, $response->getStatusCode());
    }

    public function testArbitraryStaticMethodCallbackIsRejected(KernelBrowser $client): void
    {
        $this->login($client);

        // a non-URL method on a real module model may not be invoked either
        $response = $this->postGenerateUrl(
            $client,
            [
                'className' => \Backend\Modules\Blog\Engine\Model::class,
                'methodName' => 'delete',
                'url' => '1',
                'parameters' => '',
            ]
        );

        self::assertNotSame(200, $response->getStatusCode());
    }

    private function postGenerateUrl(KernelBrowser $client, array $parameters)
    {
        // render a backend page so a csrf token is present in the session
        $client->request('GET', '/private/en/dashboard');
        $token = $client->getRequest()->getSession()->get('csrf_token');

        $client->request(
            'POST',
            self::AJAX_URL,
            array_merge(
                [
                    'fork' => ['module' => 'Core', 'action' => 'GenerateUrl', 'language' => 'en'],
                ],
                $parameters
            ),
            [],
            [
                'HTTP_X_CSRF_TOKEN' => $token,
                'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
            ]
        );

        return $client->getResponse();
    }
}
