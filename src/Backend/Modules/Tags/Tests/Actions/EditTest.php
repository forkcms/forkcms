<?php

namespace Backend\Modules\Tags\Tests\Action;

use Backend\Modules\Tags\DataFixtures\LoadTagsModulesTags;
use Backend\Modules\Tags\DataFixtures\LoadTagsTags;
use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class EditTest extends BackendWebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $client = self::createClient();
        $this->loadFixtures(
            $client,
            [
                LoadTagsTags::class,
                LoadTagsModulesTags::class,
            ]
        );
    }

    public function testAuthenticationIsNeeded(Client $client): void
    {
        $this->assertAuthenticationIsNeeded($client, '/private/en/tags/edit?id=1');
    }

    public function testWeCanGoToEditFromTheIndexPage(): void
    {
        $client = static::createClient();
        $this->login($client);

        $crawler = $client->request('GET', '/private/en/tags/index');
        self::assertContains(
            'most used',
            $client->getResponse()->getContent()
        );

        $link = $crawler->selectLink('most used')->link();
        $client->click($link);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertContains(
            '&id=2',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testEditingOurTag(): void
    {
        $client = static::createClient();
        $this->login($client);

        $crawler = $client->request('GET', '/private/en/tags/edit?id=1');
        self::assertContains(
            'form method="post" action="/private/en/tags/edit?id=1" id="edit"',
            $client->getResponse()->getContent()
        );

        $form = $crawler->selectButton('Save')->form();

        $client->setMaxRedirects(1);
        $this->submitEditForm($client, $form, [
            'name' => 'Edited tag for functional tests',
        ]);

        // we should get a 200 and be redirected to the index page
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertContains(
            '/private/en/tags/index',
            $client->getHistory()->current()->getUri()
        );

        // our url and our page should contain the new title of our blogpost
        self::assertContains(
            '&report=edited&var=Edited%20tag%20for%20functional%20tests&highlight=row-1',
            $client->getHistory()->current()->getUri()
        );
        self::assertContains(
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
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertContains(
            '/private/en/tags/edit',
            $client->getHistory()->current()->getUri()
        );

        // our page shows an overal error message and a specific one
        self::assertContains(
            'Something went wrong',
            $client->getResponse()->getContent()
        );
        self::assertContains(
            'Please provide a name.',
            $client->getResponse()->getContent()
        );
    }

    public function testInvalidIdShouldShowAnError(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->request('GET', '/private/en/tags/edit?id=12345678');
        $client->followRedirect();

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertContains(
            '/private/en/tags/index',
            $client->getHistory()->current()->getUri()
        );
        self::assertContains(
            'error=non-existing',
            $client->getHistory()->current()->getUri()
        );
    }
}
