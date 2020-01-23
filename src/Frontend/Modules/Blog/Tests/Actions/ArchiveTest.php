<?php

namespace Frontend\Modules\Blog\Tests\Actions;

use Backend\Modules\Blog\DataFixtures\LoadBlogCategories;
use Backend\Modules\Blog\DataFixtures\LoadBlogPosts;
use Frontend\Core\Tests\FrontendWebTestCase;

class ArchiveTest extends FrontendWebTestCase
{
    public function testArchiveContainsBlogPosts(): void
    {
        $client = static::createClient();

        $this->loadFixtures(
            $client,
            [
                LoadBlogCategories::class,
                LoadBlogPosts::class,
            ]
        );

        $client->request('GET', '/en/blog/archive/2015/02');
        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        self::assertContains(
            'Blogpost for functional tests',
            $client->getResponse()->getContent()
        );
    }

    public function testArchiveWithOnlyYearsContainsBlogPosts(): void
    {
        $client = static::createClient();

        $client->request('GET', '/en/blog/archive/2015');
        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        self::assertContains(
            'Blogpost for functional tests',
            $client->getResponse()->getContent()
        );
    }

    public function testArchiveWithWrongMonthsGives404(): void
    {
        $client = static::createClient();

        $client->request('GET', '/en/blog/archive/1990/07');
        $this->assertIs404($client);
    }

    public function testNonExistingPageGives404(): void
    {
        $client = static::createClient();

        $client->request('GET', '/en/blog/archive/2015/02', ['page' => 34]);
        $this->assertIs404($client);
    }
}
