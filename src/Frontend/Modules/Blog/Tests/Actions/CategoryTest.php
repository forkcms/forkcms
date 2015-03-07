<?php

namespace Frontend\Modules\Blog\Actions;

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

        $crawler = $client->request('GET', '/en/blog/category/blogcategory-for-tests');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        $this->assertStringStartsWith(
            'BlogCategory for tests',
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
        $crawler = $client->request('GET', '/en/blog/category/blogcategory-for-tests');

        $this->assertContains('Blogpost for functional tests', $client->getResponse()->getContent());
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

    public function testNonExistingPageGives404()
    {
        $client = static::createClient();

        $client->request('GET', '/en/blog/category/blogcategory-for-tests', array('page' => 34));
        $this->assertIs404($client);
    }
}
