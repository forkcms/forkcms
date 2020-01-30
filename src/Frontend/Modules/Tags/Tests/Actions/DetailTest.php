<?php

namespace Frontend\Modules\Tags\Actions;

use Backend\Modules\Tags\DataFixtures\LoadTagsModulesTags;
use Backend\Modules\Tags\DataFixtures\LoadTagsTags;
use Frontend\Core\Tests\FrontendWebTestCase;

class DetailTest extends FrontendWebTestCase
{
    public function testTagsHaveDetailPage(): void
    {
        $client = static::createClient();
        $this->loadFixtures(
            $client,
            [
                LoadTagsTags::class,
                LoadTagsModulesTags::class,
            ]
        );

        $crawler = $client->request('GET', '/en/tags');
        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );

        $link = $crawler->selectLink('most used')->link();
        $crawler = $client->click($link);

        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        self::assertStringEndsWith(
            '/en/tags/detail/most-used',
            $client->getHistory()->current()->getUri()
        );
        $this->assertStringStartsWith(
            'most used - Tags',
            $crawler->filter('title')->text()
        );
        self::assertContains('<a href="/en/tags" title="To tags overview">', $crawler->html());
        self::assertContains('<h2>Pages</h2>', $crawler->html());
        self::assertContains('<a href="/en/sitemap" rel="tag">', $crawler->html());
    }

    public function testNonExistingFaqGives404(): void
    {
        $client = static::createClient();

        $client->request('GET', '/en/faq/detail/non-existing');
        $this->assertIs404($client);
    }
}
