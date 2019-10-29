<?php

namespace Frontend\Modules\Tags\Actions;

use Backend\Modules\Tags\DataFixtures\LoadTagsModulesTags;
use Backend\Modules\Tags\DataFixtures\LoadTagsTags;
use Common\WebTestCase;

class DetailTest extends WebTestCase
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
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );

        $link = $crawler->selectLink('most used')->link();
        $crawler = $client->click($link);

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        $this->assertStringEndsWith(
            '/en/tags/detail/most-used',
            $client->getHistory()->current()->getUri()
        );
        $this->assertStringStartsWith(
            'most used - Tags',
            $crawler->filter('title')->text()
        );
        $this->assertContains('<a href="/en/tags" title="To tags overview">', $crawler->html());
        $this->assertContains('<h2>Pages</h2>', $crawler->html());
        $this->assertContains('<a href="/en/sitemap" rel="tag">', $crawler->html());
    }

    public function testNonExistingFaqGives404(): void
    {
        $client = static::createClient();

        $client->request('GET', '/en/faq/detail/non-existing');
        $this->assertIs404($client);
    }
}
