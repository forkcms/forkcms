<?php

namespace Frontend\Modules\Tags\Tests\Actions;

use Backend\Modules\Tags\DataFixtures\LoadTagsModulesTags;
use Backend\Modules\Tags\DataFixtures\LoadTagsTags;
use Common\WebTestCase;

class IndexTest extends WebTestCase
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
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        $this->assertContains(
            '<a href="/en/tags/detail/most-used" rel="tag">',
            $client->getResponse()->getContent()
        );
        $this->assertContains(
            'most used',
            $client->getResponse()->getContent()
        );
        $this->assertContains(
            '<span class="badge hidden-phone">5</span>',
            $client->getResponse()->getContent()
        );
    }
}
