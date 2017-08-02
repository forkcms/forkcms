<?php

namespace Frontend\Modules\Blog\Tests\Actions;

use Common\WebTestCase;

class IndexTest extends WebTestCase
{
    public function testIndexContainsBlogPosts(): void
    {
        $client = static::createClient();

        $this->loadFixtures(
            $client,
            [
                'Backend\Modules\Blog\DataFixtures\LoadBlogCategories',
                'Backend\Modules\Blog\DataFixtures\LoadBlogPosts',
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
