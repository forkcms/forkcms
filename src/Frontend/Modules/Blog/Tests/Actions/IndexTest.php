<?php

namespace Frontend\Modules\Blog\Tests\Actions;

use Frontend\Core\Tests\FrontendWebTestCase;
use Backend\Modules\Blog\DataFixtures\LoadBlogCategories;
use Backend\Modules\Blog\DataFixtures\LoadBlogPosts;
use Symfony\Bundle\FrameworkBundle\Client;

class IndexTest extends FrontendWebTestCase
{
    public function testIndexContainsBlogPosts(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadBlogCategories::class,
                LoadBlogPosts::class,
            ]
        );

        $this->assertPageLoadedCorrectly($client, '/en/blog', [LoadBlogPosts::BLOG_POST_TITLE]);
    }

    public function testNonExistingPageGives404(Client $client): void
    {
        $this->assertHttpStatusCode200($client, '/en/blog');
        $this->assertHttpStatusCode404($client, '/en/blog', 'GET', ['page' => 34]);
    }
}
