<?php

namespace Frontend\Modules\Blog\Tests\Actions;

use Frontend\Core\Tests\FrontendWebTestCase;
use Backend\Modules\Blog\DataFixtures\LoadBlogCategories;
use Backend\Modules\Blog\DataFixtures\LoadBlogPosts;

class IndexTest extends FrontendWebTestCase
{
    public function testIndexContainsBlogPosts(): void
    {
        $client = static::createClient();

        $this->loadFixtures(
            $client,
            [
                LoadBlogCategories::class,
                LoadBlogPosts::class,
            ]
        );

        $client->request('GET', '/en/blog');
        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        self::assertContains(
            'Blogpost for functional tests',
            $client->getResponse()->getContent()
        );
    }

    public function testNonExistingPageGives404(): void
    {
        $client = static::createClient();

        $client->request('GET', '/en/blog', ['page' => 34]);
        $this->assertIs404($client);
    }
}
