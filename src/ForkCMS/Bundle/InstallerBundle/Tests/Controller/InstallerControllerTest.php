<?php

namespace ForkCMS\Bundle\InstallerBundle\Tests\Controller;

use Common\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Filesystem;

class InstallerControllerTest extends WebTestCase
{
    public function testNoStepActionAction(): void
    {
        $client = static::createClient(['environment' => 'test_install']);

        $client->request('GET', '/install');
        $client->followRedirect();

        // we should be redirected to the first step
        self::assertEquals(
            302,
            $client->getResponse()->getStatusCode()
        );
        self::assertStringEndsWith(
            '/install/1',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testInstallationProcess(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();

        // make sure we have a clean slate and our parameters file is backed up
        $this->emptyTestDatabase($client->getContainer()->get('database'));

        $installDatabaseConfig = [
            'install_database[databaseHostname]' => $container->getParameter('database.host'),
            'install_database[databasePort]' => $container->getParameter('database.port'),
            'install_database[databaseName]' => $container->getParameter('database.name') . '_test',
            'install_database[databaseUsername]' => $container->getParameter('database.user'),
            'install_database[databasePassword]' => $container->getParameter('database.password'),
        ];

        // recreate the client with the empty database because we need this in our installer checks
        $client = static::createClient(['environment' => 'test_install']);
        $filesystem = new Filesystem();
        $this->backupParametersFile($filesystem, $client->getContainer()->getParameter('kernel.project_dir') . '/app');

        $crawler = $client->request('GET', '/install/2');
        $crawler = $this->runTroughStep2($crawler, $client);
        $crawler = $this->runTroughStep3($crawler, $client);
        $crawler = $this->runTroughStep4($crawler, $client, $installDatabaseConfig);
        $this->runTroughStep5($crawler, $client);

        // put back our parameters file
        $this->putParametersFileBack($filesystem, $client->getContainer()->getParameter('kernel.project_dir') . '/app');
    }

    private function runTroughStep2(Crawler $crawler, Client $client): Crawler
    {
        $form = $crawler->selectButton('Next')->form();
        $form['install_languages[languages][0]']->tick();
        $form['install_languages[languages][1]']->tick();
        $form['install_languages[languages][2]']->tick();
        $client->submit(
            $form,
            [
                'install_languages[language_type]' => 'multiple',
                'install_languages[default_language]' => 'en',
            ]
        );

        $crawler = $client->followRedirect();

        // we should be redirected to step 3
        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        self::assertStringEndsWith(
            '/install/3',
            $client->getHistory()->current()->getUri()
        );

        return $crawler;
    }

    private function runTroughStep3(Crawler $crawler, Client $client): Crawler
    {
        $form = $crawler->selectButton('Next')->form();
        $form['install_modules[modules][9]']->tick();
        $form['install_modules[modules][10]']->tick();
        $form['install_modules[modules][11]']->tick();
        $form['install_modules[modules][12]']->tick();
        $form['install_modules[modules][13]']->tick();
        $form['install_modules[modules][14]']->tick();
        $form['install_modules[modules][15]']->tick();
        $form['install_modules[modules][16]']->tick();
        $form['install_modules[modules][17]']->tick();
        $client->submit($form, []);
        $crawler = $client->followRedirect();

        // we should be redirected to step 4
        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        self::assertStringEndsWith(
            '/install/4',
            $client->getHistory()->current()->getUri()
        );

        return $crawler;
    }

    private function runTroughStep4(Crawler $crawler, Client $client, array $installDatabaseConfig): Crawler
    {
        // first submit with incorrect data
        $form = $crawler->selectButton('Next')->form();
        $crawler = $client->submit($form, []);
        self::assertGreaterThan(
            0,
            $crawler->filter('div.errorMessage:contains("Problem with database credentials")')->count()
        );

        // submit with correct database credentials
        $form = $crawler->selectButton('Next')->form();
        $client->submit(
            $form,
            $installDatabaseConfig
        );
        $crawler = $client->followRedirect();

        // we should be redirected to step 5
        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        self::assertStringEndsWith(
            '/install/5',
            $client->getHistory()->current()->getUri()
        );

        return $crawler;
    }

    private function runTroughStep5(Crawler $crawler, Client $client): Crawler
    {
        $form = $crawler->selectButton('Finish installation')->form();
        $client->submit(
            $form,
            [
                'install_login[email]' => 'test@test.com',
                'install_login[password][first]' => 'password',
                'install_login[password][second]' => 'password',
            ]
        );
        $crawler = $client->followRedirect();

        // we should be redirected to step 6
        self::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
        self::assertStringEndsWith(
            '/install/6',
            $client->getHistory()->current()->getUri()
        );
        self::assertGreaterThan(
            0,
            $crawler->filter('h2:contains("Installation complete")')->count()
        );

        return $crawler;
    }
}
