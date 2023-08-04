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

        $this->loadFixtures(
            $this->getProvidedData()[0],
            [
                LoadTagsTags::class,
                LoadTagsModulesTags::class,
            ]
        );
    }

    public function testAuthenticationIsNeeded(Client $client): void
    {
        self::assertAuthenticationIsNeeded($client, '/private/en/tags/index');
    }

    public function testIndexContainsTags(Client $client): void
    {
        $this->login($client);

        self::assertPageLoadedCorrectly(
            $client,
            '/private/en/tags/index',
            [
                LoadTagsTags::TAGS_TAG_2_NAME,
                LoadTagsTags::TAGS_TAG_1_NAME,
                '<a href="/private/en/tags/index?offset=0&order=num_tags'
            ]
        );
    }
}
