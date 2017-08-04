<?php

namespace Frontend\Modules\Blog\Actions;

use Common\WebTestCase;

class CategoryTest extends WebTestCase
{
    public function testCategoryHasPage(): void
    {
        $client = static::createClient();
        $this->loadFixtures(
            $client,
            [
                'Backend\Modules\Blog\DataFixtures\LoadBlogCategories',
                'Backend\Modules\Blog\DataFixtures\LoadBlogPosts',
            ]
        );

        $crawler = $client->request('GET', '/en/blog/category/blogcategory-for-tests');
        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        self::assertStringStartsWith(
            'BlogCategory for tests',
            $crawler->filter('title')->text()
        );
    }

    public function testNonExistingCategoryPostGives404(): void
    {
        $client = static::createClient();

        $client->request('GET', '/en/blog/category/non-existing');
        $this->assertIs404($client);
    }

    public function testCategoryPageContainsBlogPost(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/blog/category/blogcategory-for-tests');

        self::assertContains('Blogpost for functional tests', $client->getResponse()->getContent());
        $link = $crawler->selectLink('Blogpost for functional tests')->link();
        $crawler = $client->click($link);

        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        self::assertStringEndsWith(
            '/en/blog/detail/blogpost-for-functional-tests',
            $client->getHistory()->current()->getUri()
        );
        self::assertStringStartsWith(
            'Blogpost for functional tests',
            $crawler->filter('title')->text()
        );
    }

    public function testNonExistingPageGives404(): void
    {
        $client = static::createClient();

        $client->request('GET', '/en/blog/category/blogcategory-for-tests', ['page' => 34]);
        $this->assertIs404($client);
    }
}
