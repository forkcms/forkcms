<?php

namespace Frontend\Modules\Faq\Actions;

use Common\WebTestCase;

class CategoryTest extends WebTestCase
{
    public function testCategoryHasPage()
    {
        $client = static::createClient();
        $this->loadFixtures(
            $client,
            array(
                'Backend\Modules\Faq\DataFixtures\LoadFaqCategories',
                'Backend\Modules\Faq\DataFixtures\LoadFaqQuestions',
            )
        );

        $crawler = $client->request('GET', '/en/faq/category/faqcategory-for-tests');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        $this->assertStringStartsWith(
            'Faq for tests',
            $crawler->filter('title')->text()
        );
    }

    public function testNonExistingCategoryPostGives404()
    {
        $client = static::createClient();

        $client->request('GET', '/en/faq/category/non-existing');
        $this->assertIs404($client);
    }

    public function testCategoryPageContainsQuestion()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/faq/category/faqcategory-for-tests');

        $this->assertContains('Is this a working test?', $client->getResponse()->getContent());
        $link = $crawler->selectLink('Is this a working test?')->link();
        $crawler = $client->click($link);

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        $this->assertStringEndsWith(
            '/en/faq/detail/is-this-a-working-test',
            $client->getHistory()->current()->getUri()
        );
        $this->assertStringStartsWith(
            'Is this a working test?',
            $crawler->filter('title')->text()
        );
    }

    public function testNonExistingPageGives404()
    {
        $client = static::createClient();

        $client->request('GET', '/en/blog/category/blogcategory-for-tests', array('page' => 34));
        $this->assertIs404($client);
    }
}
