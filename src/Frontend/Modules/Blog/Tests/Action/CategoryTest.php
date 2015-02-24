<?php

namespace Frontend\Modules\Blog\Action;

use Common\WebTestCase;

class CategoryTest extends WebTestCase
{
    public function testCategoryHasPage()
    {
        $client = static::createClient();
        $this->loadFixtures(
            $client,
            array(
                'Backend\Modules\Blog\DataFixtures\LoadBlogCategories',
                'Backend\Modules\Blog\DataFixtures\LoadBlogPosts',
            )
        );

        $crawler = $client->request('GET', '/en/blog/category/default');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        $this->assertStringStartsWith(
            'Default',
            $crawler->filter('title')->text()
        );
    }

    public function testNonExistingCategoryPostGives404()
    {
        $client = static::createClient();

        $client->request('GET', '/en/blog/category/non-existing');
        $this->assertIs404($client);
    }

    public function testCategoryPageContainsBlogPost()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/blog/category/default');

        $this->assertContains('Lorem ipsum', $client->getResponse()->getContent());
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
