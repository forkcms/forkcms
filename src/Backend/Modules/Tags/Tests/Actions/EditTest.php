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

    public function testEditingOurTag(): void
    {
        $client = static::createClient();
        $this->login($client);

        $crawler = $client->request('GET', '/private/en/tags/edit?id=1');
        $this->assertContains(
            'form method="post" action="/private/en/tags/edit?id=1" id="edit"',
            $client->getResponse()->getContent()
        );

        $form = $crawler->selectButton('Save')->form();

        $client->setMaxRedirects(1);
        $this->submitEditForm($client, $form, [
            'name' => 'Edited tag for functional tests',
        ]);

        // we should get a 200 and be redirected to the index page
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains(
            '/private/en/tags/index',
            $client->getHistory()->current()->getUri()
        );

        // our url and our page should contain the new title of our blogpost
        $this->assertContains(
            '&report=edited&var=Edited%20tag%20for%20functional%20tests&highlight=row-1',
            $client->getHistory()->current()->getUri()
        );
        $this->assertContains(
            'Edited tag for functional tests',
            $client->getResponse()->getContent()
        );
    }

    public function testSubmittingInvalidData(): void
    {
        $client = static::createClient();
        $this->login($client);

        $crawler = $client->request('GET', '/private/en/tags/edit?id=1');

        $form = $crawler->selectButton('Save')->form();
        $this->submitEditForm($client, $form, [
            'name' => '',
        ]);

        // we should get a 200 and be redirected to the index page
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains(
            '/private/en/tags/edit',
            $client->getHistory()->current()->getUri()
        );

        // our page shows an overal error message and a specific one
        $this->assertContains(
            'Something went wrong',
            $client->getResponse()->getContent()
        );
        $this->assertContains(
            'Please provide a name.',
            $client->getResponse()->getContent()
        );
    }
}
