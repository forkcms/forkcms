<?php

namespace Frontend\Modules\Blog\Actions;

use Backend\Modules\Blog\DataFixtures\LoadBlogCategories;
use Backend\Modules\Blog\DataFixtures\LoadBlogPosts;
use Frontend\Core\Tests\FrontendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class DetailTest extends FrontendWebTestCase
{
    public function testBlogPostHasDetailPage(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadBlogCategories::class,
                LoadBlogPosts::class,
            ]
        );

        $this->assertPageLoadedCorrectly($client, '/en/blog', [LoadBlogPosts::BLOG_POST_TITLE]);
        $this->assertClickOnLink($client, LoadBlogPosts::BLOG_POST_TITLE, [LoadBlogPosts::BLOG_POST_TITLE]);
        $this->assertCurrentUrlEndsWith($client, '/en/blog/detail/' . LoadBlogPosts::BLOG_POST_SLUG);
    }

    public function testNonExistingBlogPostGives404(Client $client): void
    {
        $this->assertHttpStatusCode404($client, '/en/blog/detail/non-existing');
    }
}
