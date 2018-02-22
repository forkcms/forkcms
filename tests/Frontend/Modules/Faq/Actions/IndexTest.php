<?php

namespace App\Tests\Frontend\Modules\Faq\Actions;

use App\Tests\WebTestCase;

class IndexTest extends WebTestCase
{
    public function testFaqIndexContainsCategories(): void
    {
        $client = static::createClient();

        $this->loadFixtures(
            $client,
            [
                'Backend\Modules\Faq\DataFixtures\LoadFaqCategories',
                'Backend\Modules\Faq\DataFixtures\LoadFaqQuestions',
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
