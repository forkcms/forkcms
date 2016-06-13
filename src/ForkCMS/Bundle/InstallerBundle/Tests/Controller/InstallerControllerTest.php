<?php

namespace ForkCMS\Bundle\InstallerBundle\Tests\Controller;

use Common\WebTestCase;

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
        $this->assertStringEndsWith(
            '/install/1',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testInstallationProcess()
    {
        $client = static::createClient();

        // make sure we have a clean slate and our parameters file is backed up
        $this->emptyTestDatabase($client->getContainer()->get('database'));
        $this->backupParametersFile($client->getContainer()->getParameter('kernel.root_dir'));

        $crawler = $client->request('GET', '/install/2');
        $crawler = $this->runTroughStep2($crawler, $client);
        $crawler = $this->runTroughStep3($crawler, $client);
        $crawler = $this->runTroughStep4($crawler, $client);
        $crawler = $this->runTroughStep5($crawler, $client);

        // put back our parameters file
        $this->putParametersFileBack($client->getContainer()->getParameter('kernel.root_dir'));
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @param \Symfony\Bundle\FrameworkBundle\Client $client
     *
     * @return mixed
     */
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
        $this->assertStringEndsWith(
            '/install/3',
            $client->getHistory()->current()->getUri()
        );

        return $crawler;
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @param \Symfony\Bundle\FrameworkBundle\Client $client
     *
     * @return mixed
     */
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
        $this->assertStringEndsWith(
            '/install/4',
            $client->getHistory()->current()->getUri()
        );

        return $crawler;
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @param \Symfony\Bundle\FrameworkBundle\Client $client
     *
     * @return mixed
     */
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
        $this->assertStringEndsWith(
            '/install/5',
            $client->getHistory()->current()->getUri()
        );

        return $crawler;
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @param \Symfony\Bundle\FrameworkBundle\Client $client
     *
     * @return mixed
     */
    private function runTroughStep5($crawler, $client)
    {
        $form = $crawler->selectButton('Finish installation')->form();
        $container = $client->getContainer();
        $crawler = $client->submit(
            $form,
            array(
                'install_login[email]' => 'test@test.com',
                'install_login[password][first]' => 'password',
                'install_login[password][second]' => 'password',
            )
        );
        $crawler = $client->followRedirect();

        // we should be redirected to step 6
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        $this->assertStringEndsWith(
            '/install/6',
            $client->getHistory()->current()->getUri()
        );
        $this->assertGreaterThan(
            0,
            $crawler->filter('h2:contains("Installation complete")')->count()
        );

        return $crawler;
    }
}
