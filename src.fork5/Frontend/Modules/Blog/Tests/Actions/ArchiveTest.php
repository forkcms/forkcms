<?php

namespace Frontend\Modules\Blog\Tests\Actions;

use Backend\Modules\Blog\DataFixtures\LoadBlogCategories;
use Backend\Modules\Blog\DataFixtures\LoadBlogPosts;
use Frontend\Core\Tests\FrontendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class ArchiveTest extends FrontendWebTestCase
{
    public function testArchiveContainsBlogPosts(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadBlogCategories::class,
                LoadBlogPosts::class,
            ]
        );

        self::assertPageLoadedCorrectly($client, '/en/blog/archive/2015/02', [LoadBlogPosts::BLOG_POST_TITLE]);
    }

    public function testArchiveWithOnlyYearsContainsBlogPosts(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadBlogCategories::class,
                LoadBlogPosts::class,
            ]
        );

        self::assertPageLoadedCorrectly($client, '/en/blog/archive/2015', [LoadBlogPosts::BLOG_POST_TITLE]);
    }

    public function testArchiveWithWrongMonthsGives404(Client $client): void
    {
        self::assertHttpStatusCode404($client, '/en/blog/archive/1990/07');
    }

    public function testNonExistingPageGives404(Client $client): void
    {
        self::assertHttpStatusCode404($client, '/en/blog/archive/2015/02', 'GET', ['page' => 34]);
    }
}
