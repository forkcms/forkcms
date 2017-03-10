<?php

namespace Frontend\Modules\Search\Tests\Actions;

use Common\WebTestCase;

class IndexTest extends WebTestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testSearchIndexWorks()
    {
        $client = static::createClient();

        $this->loadFixtures(
            $client,
            array(
                'Backend\Modules\Blog\DataFixtures\LoadBlogCategories',
                'Backend\Modules\Blog\DataFixtures\LoadBlogPosts',
            )
        );

        $client->request('GET', '/en/search');
        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testNotSubmittedSearchIndexDoesNotContainData()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/en/search');
        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );

        // result should not yet be found
        self::assertEquals(
            0,
            $crawler->filter('html:contains("Blogpost for functional tests")')->count()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testSubmittedSearchValidatesData()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/en/search');
        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );

        $form = $crawler->selectButton('Search')->form();

        // $_GET parameters should be set manually, since Fork uses them.
        $this->submitForm($client, $form, array('form' => 'search'));

        // result should not yet be found
        self::assertContains(
            'The searchterm is required.',
            $client->getResponse()->getContent()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testSubmittedSearchIndexContainsData()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/en/search');
        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );

        $form = $crawler->selectButton('Search')->form();

        $this->submitForm($client, $form, array(
            'q' => 'Blogpost',
            'submit' => 'Search',
            'form' => 'search',
        ));

        // result should not yet be found
        self::assertContains(
            'Blogpost for functional tests',
            $client->getResponse()->getContent()
        );
    }
}
