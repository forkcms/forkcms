<?php

namespace ForkCMS\Bundle\InstallerBundle\Tests\Controller;

use Common\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @group installer
 */
class InstallerControllerTest extends WebTestCase
{
    /** @var string */
    private $kernelDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kernelDir = $this->getProvidedData()[0]->getContainer()->getParameter('kernel.project_dir') . '/app';
    }

    protected function onNotSuccessfulTest(Throwable $throwable): void
    {
        // put back our parameters file
        $this->putParametersFileBack(new Filesystem(), $this->kernelDir);

        parent::onNotSuccessfulTest($throwable);
    }

    public function testNoStepActionAction(): void
    {
        $client = static::createClient(['environment' => 'test_install']);

        $client->request('GET', '/install');
        $client->followRedirect();

        // we should be redirected to the first step
        self::assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
        self::assertCurrentUrlEndsWith($client, '/install/1');
    }

    public function testInstallationProcess(Client $client): void
    {
        $container = $client->getContainer();
        $filesystem = new Filesystem();
        $installDatabaseConfig = [
            'install_database[databaseHostname]' => $container->getParameter('database.host'),
            'install_database[databasePort]' => $container->getParameter('database.port'),
            'install_database[databaseName]' => $container->getParameter('database.name') . '_test',
            'install_database[databaseUsername]' => $container->getParameter('database.user'),
            'install_database[databasePassword]' => $container->getParameter('database.password'),
        ];

        // make sure we have a clean slate and our parameters file is backed up
        $this->emptyTestDatabase($container->get('database'));

        // recreate the client with the empty database because we need this in our installer checks

        $this->backupParametersFile($filesystem, $this->kernelDir);
        $client = static::createClient(['environment' => 'test_install']);

        self::assertGetsRedirected($client, '/install', '/install/2');
        $this->runTroughStep2($client);
        $this->runTroughStep3($client);
        $this->runTroughStep4($client, $installDatabaseConfig);
        $this->runTroughStep5($client);

        // put back our parameters file
        $this->putParametersFileBack($filesystem, $this->kernelDir);
    }

    private function runTroughStep2(Client $client): void
    {
        self::assertCurrentUrlEndsWith($client, '/install/2');

        $form = $this->getFormForSubmitButton($client, 'Next');
        $form['install_languages[languages][0]']->tick();
        $form['install_languages[languages][1]']->tick();
        $form['install_languages[languages][2]']->tick();
        $this->submitForm(
            $client,
            $form,
            [
                'install_languages[language_type]' => 'multiple',
                'install_languages[default_language]' => 'en',
            ]
        );

        // we should be redirected to step 3
        self::assertIs200($client);
        self::assertCurrentUrlEndsWith($client, '/install/3');
    }

    private function runTroughStep3(Client $client): void
    {
        $form = $this->getFormForSubmitButton($client, 'Next');
        $form['install_modules[modules][0]']->tick();
        $form['install_modules[modules][1]']->tick();
        $form['install_modules[modules][2]']->tick();
        $form['install_modules[modules][3]']->tick();
        $form['install_modules[modules][4]']->tick();
        $form['install_modules[modules][5]']->tick();
        $form['install_modules[modules][6]']->tick();
        $form['install_modules[modules][7]']->tick();
        $this->submitForm($client, $form);

        // we should be redirected to step 4
        self::assertIs200($client);
        self::assertCurrentUrlEndsWith($client, '/install/4');
    }

    private function runTroughStep4(Client $client, array $installDatabaseConfig): void
    {
        // first submit with incorrect data
        $form = $this->getFormForSubmitButton($client, 'Next');
        $this->submitForm($client, $form);
        self::assertGreaterThan(
            0,
            $client->getCrawler()->filter('div.alert-danger:contains("Problem with database credentials")')->count()
        );

        // submit with correct database credentials
        $form = $this->getFormForSubmitButton($client, 'Next');
        $this->submitForm($client, $form, $installDatabaseConfig, true);

        // we should be redirected to step 5
        self::assertIs200($client);
        self::assertCurrentUrlEndsWith($client, '/install/5');
    }

    private function runTroughStep5(Client $client): void
    {
        $form = $this->getFormForSubmitButton($client, 'Finish installation');
        $this->submitForm(
            $client,
            $form,
            [
                'install_login[email]' => 'test@test.com',
                'install_login[password][first]' => 'password',
                'install_login[password][second]' => 'password',
            ],
            true
        );

        // we should be redirected to step 6
        self::assertIs200($client);
        self::assertCurrentUrlEndsWith($client, '/install/6');
        self::assertGreaterThan(
            0,
            $client->getCrawler()->filter('h3:contains("Installation complete")')->count()
        );
    }
}
