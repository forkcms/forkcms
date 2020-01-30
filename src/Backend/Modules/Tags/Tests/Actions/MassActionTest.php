<?php

namespace Backend\Modules\Tags\Tests\Action;

use Backend\Modules\Tags\DataFixtures\LoadTagsModulesTags;
use Backend\Modules\Tags\DataFixtures\LoadTagsTags;
use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class MassActionTest extends BackendWebTestCase
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
        $this->assertAuthenticationIsNeeded($client, '/private/en/tags/mass_action');
    }

    public function testActionIsRequired(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/tags/mass_action');

        self::assertStringEndsWith(
            '&error=no-action-selected',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testIdsAreRequired(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/tags/mass_action?action=delete');

        self::assertStringEndsWith(
            '&error=no-selection',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testDeletingOneTag(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/tags/mass_action?action=delete&id[]=2');

        self::assertStringEndsWith(
            '&report=deleted',
            $client->getHistory()->current()->getUri()
        );
        self::assertNotContains('id=2" title="">most used</a>', $client->getResponse()->getContent());
        self::assertContains('id=1" title="">test</a>', $client->getResponse()->getContent());
    }

    public function testDeletingAllTags(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/tags/mass_action?action=delete&id[]=2&id[]=1');

        self::assertStringEndsWith(
            '&report=deleted',
            $client->getHistory()->current()->getUri()
        );
        self::assertNotContains('id=2" title="">most used</a>', $client->getResponse()->getContent());
        self::assertNotContains('id=1" title="">test</a>', $client->getResponse()->getContent());
        self::assertContains('<p>There are no tags yet.</p>', $client->getResponse()->getContent());
    }
}
