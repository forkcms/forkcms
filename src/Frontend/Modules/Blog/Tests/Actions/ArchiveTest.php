<?php

namespace Frontend\Modules\Blog\Tests\Actions;

use Common\WebTestCase;

class ArchiveTest extends WebTestCase
{
    public function testArchiveContainsBlogPosts(): void
    {
        $client = static::createClient();

        $this->loadFixtures(
            $client,
            [
                'Backend\Modules\Blog\DataFixtures\LoadBlogCategories',
                'Backend\Modules\Blog\DataFixtures\LoadBlogPosts',
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
