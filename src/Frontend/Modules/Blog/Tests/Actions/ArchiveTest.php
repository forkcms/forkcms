<?php

namespace Frontend\Modules\Blog\Tests\Actions;

use Common\WebTestCase;

class ArchiveTest extends WebTestCase
{
    public function testArchiveContainsBlogPosts()
    {
        $client = static::createClient();

        $this->loadFixtures(
            $client,
            array(
                'Backend\Modules\Blog\DataFixtures\LoadBlogCategories',
                'Backend\Modules\Blog\DataFixtures\LoadBlogPosts',
            )
        );

        $client->request('GET', '/en/blog/archive/2015/02');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        $this->assertContains(
            'Blogpost for functional tests',
            $client->getResponse()->getContent()
        );
    }

    public function testArchiveWithOnlyYearsContainsBlogPosts()
    {
        $client = static::createClient();

        $client->request('GET', '/en/blog/archive/2015');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        $this->assertContains(
            'Blogpost for functional tests',
            $client->getResponse()->getContent()
        );
    }

    public function testArchiveWithWrongMonthsGives404()
    {
        $client = static::createClient();

        $client->request('GET', '/en/blog/archive/1990/07');
        $this->assertIs404($client);
    }

    public function testNonExistingPageGives404()
    {
        $client = static::createClient();

        $client->request('GET', '/en/blog/archive/2015/02', array('page' => 34));
        $this->assertIs404($client);
    }
}
