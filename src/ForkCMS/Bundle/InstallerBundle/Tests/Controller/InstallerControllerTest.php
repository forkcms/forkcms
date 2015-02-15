<?php

namespace ForkCMS\Bundle\InstallerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class InstallerControllerTest extends WebTestCase
{
    public function testnoStepActionAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/install');
        $crawler = $client->followRedirect();

        // we should be redirected to the first step
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        $this->assertRegExp(
            '/\/install\/1(\/|)$/',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testInstallationProcess()
    {
        $client = static::createClient();
        $this->emptyTestDatabase($client->getContainer()->get('database'));

        $crawler = $client->request('GET', '/install/2');
        $crawler = $this->runTroughStep2($crawler, $client);
        $crawler = $this->runTroughStep3($crawler, $client);
        $crawler = $this->runTroughStep4($crawler, $client);
    }

    private function emptyTestDatabase($database)
    {
        foreach ($database->getTables() as $table) {
            $database->drop($table);
        }
    }

    private function runTroughStep2($crawler, $client)
    {
        $form = $crawler->selectButton('Next')->form();
        $form['install_languages[languages][0]']->tick();
        $form['install_languages[languages][1]']->tick();
        $form['install_languages[languages][2]']->tick();
        $crawler = $client->submit(
            $form,
            array(
                'install_languages[language_type]' => 'multiple',
                'install_languages[default_language]' => 'en',
            )
        );

        $crawler = $client->followRedirect();

        // we should be redirected to step 3
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        $this->assertRegExp(
            '/\/install\/3(\/|)$/',
            $client->getHistory()->current()->getUri()
        );

        return $crawler;
    }

    private function runTroughStep3($crawler, $client)
    {
        $form = $crawler->selectButton('Next')->form();
        $crawler = $client->submit($form, array());
        $crawler = $client->followRedirect();

        // we should be redirected to step 4
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        $this->assertRegExp(
            '/\/install\/4(\/|)$/',
            $client->getHistory()->current()->getUri()
        );

        return $crawler;
    }

    private function runTroughStep4($crawler, $client)
    {
        // first submit with incorrect data
        $form = $crawler->selectButton('Next')->form();
        $crawler = $client->submit($form, array());
        $this->assertGreaterThan(
            0,
            $crawler->filter('div.errorMessage:contains("Problem with database credentials")')->count()
        );

        // submit with correct database credentials
        $form = $crawler->selectButton('Next')->form();
        $container = $client->getContainer();
        $crawler = $client->submit(
            $form,
            array(
                'install_database[dbHostname]' => $container->getParameter('database.host'),
                'install_database[dbPort]' => $container->getParameter('database.port'),
                'install_database[dbDatabase]' => $container->getParameter('database.name') . '_test',
                'install_database[dbUsername]' => $container->getParameter('database.user'),
                'install_database[dbPassword]' => $container->getParameter('database.password'),
            )
        );
        $crawler = $client->followRedirect();

        // we should be redirected to step 5
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        $this->assertRegExp(
            '/\/install\/5(\/|)$/',
            $client->getHistory()->current()->getUri()
        );

        return $crawler;
    }
}
