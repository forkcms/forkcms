<?php

namespace Api\V1\Tests\Action;

use Common\WebTestCase;

class CoreTest extends WebTestCase
{
    public function testErrorOutput()
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
        $this->assertNotEmpty(
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

    public function testNoMethod()
    {
        $client = static::createClient();

        $client->request('GET', '/api');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        $this->assertContains(
            'No method-parameter provided',
            $client->getResponse()->getContent()
        );
    }

    public function testInvalidMethod()
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
}
