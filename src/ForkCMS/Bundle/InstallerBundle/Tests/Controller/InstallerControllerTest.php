<?php

namespace ForkCMS\Bundle\InstallerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InstallerControllerTest extends WebTestCase
{
    public function testnoStepActionAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/install');
        $crawler = $client->followRedirect();

        // we should be redirected to the first step
        $this->assertRegExp(
            '/\/install\/1(\/|)$/',
            $client->getHistory()->current()->getUri()
        );
    }
}
