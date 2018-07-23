<?php

namespace Backend\Modules\Blog\Tests\Action;

use Backend\Modules\Tags\DataFixtures\LoadTagsModulesTags;
use Backend\Modules\Tags\DataFixtures\LoadTagsTags;
use Common\WebTestCase;

class EditTest extends WebTestCase
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
        $client->request('GET', '/private/en/tags/edit?id=1');

        // we should get redirected to authentication with a reference to the wanted page
        $this->assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Ftags%2Fedit%3Fid%3D1',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testWeCanGoToEditFromTheIndexPage(): void
    {
        $client = static::createClient();
        $this->login($client);

        $crawler = $client->request('GET', '/private/en/tags/index');
        $this->assertContains(
            'most used',
            $client->getResponse()->getContent()
        );

        $link = $crawler->selectLink('most used')->link();
        $client->click($link);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains(
            '&id=2',
            $client->getHistory()->current()->getUri()
        );
    }
}
