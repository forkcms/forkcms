<?php

namespace Frontend\Modules\Tags\Tests\Actions;

use Backend\Modules\Tags\DataFixtures\LoadTagsModulesTags;
use Backend\Modules\Tags\DataFixtures\LoadTagsTags;
use Frontend\Core\Tests\FrontendWebTestCase;

class IndexTest extends FrontendWebTestCase
{
    public function testTagsIndexShowsTags(): void
    {
        $client = self::createClient();
        $this->loadFixtures(
            $client,
            [
                LoadTagsTags::class,
                LoadTagsModulesTags::class,
            ]
        );

        $client->request('GET', '/en/tags');
        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        self::assertContains(
            '<a href="/en/tags/detail/most-used" rel="tag">',
            $client->getResponse()->getContent()
        );
        self::assertContains(
            'most used',
            $client->getResponse()->getContent()
        );
        self::assertContains(
            '<span class="badge hidden-phone">6</span>',
            $client->getResponse()->getContent()
        );
    }
}
