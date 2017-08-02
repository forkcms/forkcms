<?php

namespace Frontend\Modules\Faq\Actions;

use Common\WebTestCase;

class CategoryTest extends WebTestCase
{
    public function testCategoryHasPage(): void
    {
        $client = static::createClient();
        $this->loadFixtures(
            $client,
            [
                'Backend\Modules\Faq\DataFixtures\LoadFaqCategories',
                'Backend\Modules\Faq\DataFixtures\LoadFaqQuestions',
            ]
        );

        $crawler = $client->request('GET', '/en/faq/category/faqcategory-for-tests');
        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        self::assertStringStartsWith(
            'Faq for tests',
            $crawler->filter('title')->text()
        );
    }

    public function testNonExistingCategoryPostGives404(): void
    {
        $client = static::createClient();

        $client->request('GET', '/en/faq/category/non-existing');
        $this->assertIs404($client);
    }

    public function testCategoryPageContainsQuestion(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/faq/category/faqcategory-for-tests');

        self::assertContains('Is this a working test?', $client->getResponse()->getContent());
        $link = $crawler->selectLink('Is this a working test?')->link();
        $crawler = $client->click($link);

        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        self::assertStringEndsWith(
            '/en/faq/detail/is-this-a-working-test',
            $client->getHistory()->current()->getUri()
        );
        self::assertStringStartsWith(
            'Is this a working test?',
            $crawler->filter('title')->text()
        );
    }

    public function testNonExistingPageGives404(): void
    {
        $client = static::createClient();

        $client->request('GET', '/en/blog/category/blogcategory-for-tests', ['page' => 34]);
        $this->assertIs404($client);
    }
}
