<?php

namespace Frontend\Modules\Faq\Tests\Actions;

use Common\WebTestCase;

class IndexTest extends WebTestCase
{
    public function testFaqIndexContainsCategories()
    {
        $client = static::createClient();

        $this->loadFixtures(
            $client,
            array(
                'Backend\Modules\Faq\DataFixtures\LoadFaqCategories',
                'Backend\Modules\Faq\DataFixtures\LoadFaqQuestions',
            )
        );

        $client->request('GET', '/en/faq');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        $this->assertContains(
            'Faq for tests',
            $client->getResponse()->getContent()
        );
    }

    public function testFaqIndexContainsQuestions()
    {
        $client = static::createClient();

        $client->request('GET', '/en/faq');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        $this->assertContains(
            'Is this a working test?',
            $client->getResponse()->getContent()
        );
    }
}
