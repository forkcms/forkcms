<?php

namespace Frontend\Modules\Faq\Actions;

use Common\WebTestCase;

class DetailTest extends WebTestCase
{
    public function testBlogPostHasDetailPage()
    {
        $client = static::createClient();
        $this->loadFixtures(
            $client,
            array(
                'Backend\Modules\Faq\DataFixtures\LoadFaqCategories',
                'Backend\Modules\Faq\DataFixtures\LoadFaqQuestions',
            )
        );

        $crawler = $client->request('GET', '/en/faq');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );

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

    public function testNonExistingBlogPostGives404()
    {
        $client = static::createClient();

        $client->request('GET', '/en/faq/detail/non-existing');
        $this->assertIs404($client);
    }
}
