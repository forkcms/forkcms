<?php

namespace Frontend\Modules\Blog\Actions;

use Common\WebTestCase;

class DetailTest extends WebTestCase
{
    public function testBlogPostHasDetailPage()
    {
        $client = static::createClient();
        $this->loadFixtures(
            $client,
            array(
                'Backend\Modules\Blog\DataFixtures\LoadBlogCategories',
                'Backend\Modules\Blog\DataFixtures\LoadBlogPosts',
            )
        );

        $crawler = $client->request('GET', '/en/blog');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );

        $link = $crawler->selectLink('Blogpost for functional tests')->link();
        $crawler = $client->click($link);

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        $this->assertStringEndsWith(
            '/en/blog/detail/blogpost-for-functional-tests',
            $client->getHistory()->current()->getUri()
        );
        $this->assertStringStartsWith(
            'Blogpost for functional tests',
            $crawler->filter('title')->text()
        );
    }

    public function testNonExistingBlogPostGives404()
    {
        $client = static::createClient();

        $client->request('GET', '/en/blog/detail/non-existing');
        $this->assertIs404($client);
    }
}
