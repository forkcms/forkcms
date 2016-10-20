<?php

namespace Api\V1\Tests\Actions;

use Common\ApiTestCase;

class CoreTest extends ApiTestCase
{
    public function testApiGivesErrorWithoutParameters()
    {
        $client = static::createClient();
        $request = $client->request('GET', '/api');
        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );

        self::assertEquals(
            '400',
            $request->filter('fork')->attr('status_code')
        );
        self::assertEquals(
            'error',
            $request->filter('fork')->attr('status')
        );
        self::assertEquals(
            trim(file_get_contents(__DIR__ . '/../../../../../VERSION.md')),
            $request->filter('fork')->attr('version')
        );
        self::assertNotEmpty(
            $request->filter('fork')->attr('endpoint')
        );
        self::assertEquals(
            'No method-parameter provided.',
            $request->filter('message')->text()
        );
    }

    public function testApiGivesOkOutput()
    {
        $client = static::createClient();
        $this->loadFixtures($client);
        $request = $this->requestWithGetParameters(
            $client,
            '/api',
            array(
                'method' => 'core.getAPIKey',
                'email' => 'noreply@fork-cms.com',
                'password' => 'fork',
            )
        );

        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );

        self::assertEquals(
            '200',
            $request->filter('fork')->attr('status_code')
        );
        self::assertEquals(
            'ok',
            $request->filter('fork')->attr('status')
        );
        self::assertNotEmpty(
            $request->filter('fork')->attr('version')
        );
        self::assertNotEmpty(
            $request->filter('fork')->attr('endpoint')
        );
    }

    public function testApiGivesErrorWitAnInvalidMethod()
    {
        $client = static::createClient();
        $this->requestWithGetParameters(
            $client,
            '/api',
            array(
                'method' => 'invalid.method'
            )
        );

        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        self::assertContains(
            'Invalid method.',
            $client->getResponse()->getContent()
        );
    }

    public function testApiGivesCorrectOutputForCoreGetAPIKey()
    {
        $client = static::createClient();
        $this->loadFixtures($client);
        $request = $this->requestWithGetParameters(
            $client,
            '/api',
            array(
                'method' => 'core.getAPIKey',
                'email' => 'noreply@fork-cms.com',
                'password' => 'fork',
            )
        );

        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        self::assertEquals(
            '54f0fb1222403',
            $request->filter('api_key')->text()
        );
    }

    public function testApiGivesCorrectOutputForCoreGetInfo()
    {
        $client = static::createClient();
        $this->loadFixtures($client);
        $data = array_merge(
            array(
                'method' => 'core.getInfo',
            ),
            $this->getAuthorizationParameters()
        );
        $request = $this->requestWithGetParameters(
            $client,
            '/api',
            $data
        );

        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        self::assertCount(
            1,
            $request->filter('languages')
        );

        self::assertGreaterThanOrEqual(
            1,
            $request->filter('languages > language')->count()
        );

        $that = $this;

        $request->filter('languages > language')->each(
            function ($language) use ($that) {
                self::assertNotEmpty(
                    $language->attr('language')
                );
                self::assertNotEmpty(
                    $language->attr('is_default')
                );
                self::assertEquals(
                    'My website',
                    $language->filter('title')->text()
                );
                self::assertNotEmpty(
                    $language->filter('url')->text()
                );
            }
        );
    }
}
