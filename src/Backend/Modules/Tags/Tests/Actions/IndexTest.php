<?php

namespace Backend\Modules\Tags\Tests\Action;

use Backend\Modules\Tags\DataFixtures\LoadTagsModulesTags;
use Backend\Modules\Tags\DataFixtures\LoadTagsTags;
use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class IndexTest extends BackendWebTestCase
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
        $this->assertAuthenticationIsNeeded($client, '/private/en/tags/index');
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
