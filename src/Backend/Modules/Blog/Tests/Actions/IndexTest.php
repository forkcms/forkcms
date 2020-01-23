<?php

namespace Backend\Modules\Blog\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Backend\Modules\Blog\DataFixtures\LoadBlogCategories;
use Backend\Modules\Blog\DataFixtures\LoadBlogPosts;

class IndexTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(): void
    {
        $client = static::createClient();
        $this->logout($client);
        $this->loadFixtures(
            $client,
            [
                LoadBlogCategories::class,
                LoadBlogPosts::class,
            ]
        );

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/blog/index');

        // we should get redirected to authentication with a reference to blog index in our url
        self::assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Fblog%2Findex',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testIndexContainsBlogPosts(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->request('GET', '/private/en/blog/index');
        self::assertContains(
            'Blogpost for functional tests',
            $client->getResponse()->getContent()
        );

        // some stuff we also want to see on the blog index
        self::assertContains(
            'Add article',
            $client->getResponse()->getContent()
        );
        self::assertContains(
            'Published articles',
            $client->getResponse()->getContent()
        );
    }
}
