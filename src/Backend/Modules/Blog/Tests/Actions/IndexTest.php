<?php

namespace Backend\Modules\Blog\Tests\Action;

use Common\WebTestCase;

class IndexTest extends WebTestCase
{
    public function testAuthenticationIsNeeded()
    {
        $this->logout();
        $client = static::createClient();
        $this->loadFixtures(
            $client,
            array(
                'Backend\Modules\Blog\DataFixtures\LoadBlogCategories',
                'Backend\Modules\Blog\DataFixtures\LoadBlogPosts',
            )
        );

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/blog/index');

        // we should get redirected to authentication with a reference to blog index in our url
        $this->assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Fblog%2Findex',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testIndexContainsBlogPosts()
    {
        $client = static::createClient();
        $this->login();

        $client->request('GET', '/private/en/blog/index');
        $this->assertContains(
            'Blogpost for functional tests',
            $client->getResponse()->getContent()
        );

        // some stuff we also want to see on the blog index
        $this->assertContains(
            'Add article',
            $client->getResponse()->getContent()
        );
        $this->assertContains(
            'Published articles',
            $client->getResponse()->getContent()
        );
    }
}
