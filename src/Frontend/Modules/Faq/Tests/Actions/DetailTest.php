<?php

namespace Frontend\Modules\Faq\Actions;

use Backend\Modules\Faq\DataFixtures\LoadFaqCategories;
use Backend\Modules\Faq\DataFixtures\LoadFaqQuestions;
use Common\WebTestCase;

class DetailTest extends WebTestCase
{
    public function testFaqHasDetailPage(): void
    {
        $client = static::createClient();
        $this->loadFixtures(
            $client,
            [
                LoadFaqCategories::class,
                LoadFaqQuestions::class,
            ]
        );

        $crawler = $client->request('GET', '/en/faq');
        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );

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

    public function testNonExistingFaqGives404(): void
    {
        $client = static::createClient();

        $client->request('GET', '/en/faq/detail/non-existing');
        $this->assertIs404($client);
    }
}
