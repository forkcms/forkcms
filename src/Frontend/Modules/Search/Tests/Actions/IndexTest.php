<?php

namespace Frontend\Modules\Search\Tests\Actions;

use Frontend\Core\Tests\FrontendWebTestCase;
use Backend\Modules\Blog\DataFixtures\LoadBlogCategories;
use Backend\Modules\Blog\DataFixtures\LoadBlogPosts;

class IndexTest extends FrontendWebTestCase
{
    public function testSearchIndexWorks(): void
    {
        $client = static::createClient();

        $this->loadFixtures(
            $client,
            [
                LoadBlogCategories::class,
                LoadBlogPosts::class,
            ]
        );

        $client->request('GET', '/en/search');
        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
    }

    public function testNotSubmittedSearchIndexDoesNotContainData(): void
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

    public function testSubmittedSearchValidatesData(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/en/search');
        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );

        $form = $crawler->selectButton('Search')->form();

        // $_GET parameters should be set manually, since Fork uses them.
        $this->submitForm($client, $form, ['form' => 'search']);

        // result should not yet be found
        self::assertContains(
            'The searchterm is required.',
            $client->getResponse()->getContent()
        );
    }

    public function testSubmittedSearchIndexContainsData(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/en/search');
        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );

        $form = $crawler->selectButton('Search')->form();

        $this->submitForm($client, $form, [
            'q' => 'Blogpost',
            'submit' => 'Search',
            'form' => 'search',
        ]);

        // result should not yet be found
        self::assertContains(
            'Blogpost for functional tests',
            $client->getResponse()->getContent()
        );
    }
}
