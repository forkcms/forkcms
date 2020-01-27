<?php

namespace Backend\Modules\Blog\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Backend\Modules\Blog\DataFixtures\LoadBlogCategories;
use Backend\Modules\Blog\DataFixtures\LoadBlogPosts;
use Symfony\Bundle\FrameworkBundle\Client;

class IndexTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        $this->assertAuthenticationIsNeeded($client, '/private/en/blog/index');
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
