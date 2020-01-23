<?php

namespace Frontend\Modules\Faq\Tests\Actions;

use Common\WebTestCase;
use Backend\Modules\Faq\DataFixtures\LoadFaqCategories;
use Backend\Modules\Faq\DataFixtures\LoadFaqQuestions;

class IndexTest extends WebTestCase
{
    public function testFaqIndexContainsCategories(): void
    {
        $client = static::createClient();

        $this->loadFixtures(
            $client,
            [
                LoadFaqCategories::class,
                LoadFaqQuestions::class,
            ]
        );

        $client->request('GET', '/en/faq');
        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        self::assertContains(
            'Faq for tests',
            $client->getResponse()->getContent()
        );
    }

    public function testFaqIndexContainsQuestions(): void
    {
        $client = static::createClient();

        $client->request('GET', '/en/faq');
        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        self::assertContains(
            'Is this a working test?',
            $client->getResponse()->getContent()
        );
    }
}
