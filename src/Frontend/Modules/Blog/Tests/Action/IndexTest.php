<?php

namespace Frontend\Modules\Blog\Tests\Action;

use Common\WebTestCase;

class IndexTest extends WebTestCase
{
    public function testIndexContainsBlogPosts()
    {
        $client = static::createClient();

        $this->loadFixtures(
            $client,
            array(
                'Backend\Modules\Blog\DataFixtures\LoadBlogCategories',
                'Backend\Modules\Blog\DataFixtures\LoadBlogPosts',
            )
        );

        $client->request('GET', '/en/blog');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        $this->assertContains(
            'Lorem ipsum',
            $client->getResponse()->getContent()
        );
    }

    public function testBlogPostHasDetailPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/blog');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );

        $link = $crawler->selectLink('Lorem ipsum')->link();
        $crawler = $client->click($link);

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        $this->assertStringEndsWith(
            '/en/blog/detail/lorem-ipsum',
            $client->getHistory()->current()->getUri()
        );
        $this->assertStringStartsWith(
            'Lorem ipsum',
            $crawler->filter('title')->text()
        );
    }
}
