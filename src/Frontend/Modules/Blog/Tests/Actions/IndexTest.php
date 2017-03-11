<?php

namespace Frontend\Modules\Blog\Tests\Actions;

use Common\WebTestCase;

class IndexTest extends WebTestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testIndexContainsBlogPosts()
    {
        $client = static::createClient();

        $this->loadFixtures(
            $client,
            array(
                'Backend\Modules\Blog\DataFixtures\LoadBlogCategories',
                'Backend\Modules\Blog\DataFixtures\LoadBlogPosts',
            )
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

    /**
     * @runInSeparateProcess
     */
    public function testNonExistingPageGives404()
    {
        $client = static::createClient();

        $client->request('GET', '/en/blog', array('page' => 34));
        $this->assertIs404($client);
    }
}
