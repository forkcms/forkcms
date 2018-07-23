<?php

namespace Backend\Modules\Blog\Tests\Action;

use Backend\Modules\Tags\DataFixtures\LoadTagsModulesTags;
use Backend\Modules\Tags\DataFixtures\LoadTagsTags;
use Common\WebTestCase;

class IndexTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (!defined('APPLICATION')) {
            define('APPLICATION', 'Backend');
        }

        $client = self::createClient();
        $this->loadFixtures(
            $client,
            [
                LoadTagsTags::class,
                LoadTagsModulesTags::class,
            ]
        );
    }

    public function testAuthenticationIsNeeded(): void
    {
        $client = static::createClient();
        $this->logout($client);

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/tags/index');

        // we should get redirected to authentication with a reference to the wanted page
        $this->assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Ftags%2Findex',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testIndexContainsTags(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->request('GET', '/private/en/tags/index');
        $this->assertContains(
            'test',
            $client->getResponse()->getContent()
        );

        $this->assertContains(
            'most used',
            $client->getResponse()->getContent()
        );

        $this->assertContains(
            '<a href="/private/en/tags/index?offset=0&order=num_tags',
            $client->getResponse()->getContent()
        );
    }
}
