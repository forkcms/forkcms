<?php

namespace Api\V1\Tests\Actions;

use Common\ApiTestCase;

class CoreTest extends ApiTestCase
{
    public function testApiGivesErrorWithoutParameters()
    {
        $client = static::createClient();
        $request = $client->request('GET', '/api');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );

        $this->assertEquals(
            '400',
            $request->filter('fork')->attr('status_code')
        );
        $this->assertEquals(
            'error',
            $request->filter('fork')->attr('status')
        );
        $this->assertEquals(
            trim(file_get_contents(__DIR__ . '/../../../../../VERSION.md')),
            $request->filter('fork')->attr('version')
        );
        $this->assertNotEmpty(
            $request->filter('fork')->attr('endpoint')
        );
        $this->assertEquals(
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

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );

        $this->assertEquals(
            '200',
            $request->filter('fork')->attr('status_code')
        );
        $this->assertEquals(
            'ok',
            $request->filter('fork')->attr('status')
        );
        $this->assertNotEmpty(
            $request->filter('fork')->attr('version')
        );
        $this->assertNotEmpty(
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

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        $this->assertContains(
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

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        $this->assertEquals(
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

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        $this->assertCount(
            1,
            $request->filter('languages')
        );

        $this->assertGreaterThanOrEqual(
            1,
            $request->filter('languages > language')->count()
        );

        $that = $this;

        $request->filter('languages > language')->each(
            function ($language) use ($that) {
                $that->assertNotEmpty(
                    $language->attr('language')
                );
                $that->assertNotEmpty(
                    $language->attr('is_default')
                );
                $that->assertEquals(
                    'My website',
                    $language->filter('title')->text()
                );
                $that->assertNotEmpty(
                    $language->filter('url')->text()
                );
            }
        );
    }
}
