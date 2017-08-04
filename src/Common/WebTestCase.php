<?php

namespace Common;

use ForkCMS\App\AppKernel;
use ForkCMS\App\BaseModel;
use SpoonDatabase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\FileSystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\DomCrawler\Crawler;
use Backend\Core\Engine\Authentication;

/**
 * WebTestCase is the base class for functional tests.
 */
abstract class WebTestCase extends BaseWebTestCase
{
    /**
     * Attempts to guess the kernel location.
     *
     * When the Kernel is located, the file is required.
     *
     * @todo Remove this when Fork has no custom Kernel class anymore
     *
     * @throws \RuntimeException
     *
     * @return string The Kernel class name
     */
    protected static function getKernelClass(): string
    {
        return AppKernel::class;
    }

    /**
     * Creates a Client.
     *
     * @param array $options An array of options to pass to the createKernel class
     * @param array $server An array of server parameters
     *
     * @return Client A Client instance
     */
    protected static function createClient(array $options = [], array $server = []): Client
    {
        if (null !== static::$kernel) {
            static::$kernel->shutdown();
        }

        $client = parent::createClient($options, $server);
        static::$kernel = $client->getKernel();
        BaseModel::setContainer(static::$kernel->getContainer());

        return $client;
    }

    /**
     * Fully empties the test database
     *
     * @param SpoonDatabase $database
     */
    protected function emptyTestDatabase(SpoonDatabase $database)
    {
        foreach ($database->getTables() as $table) {
            $database->execute(
                'SET FOREIGN_KEY_CHECKS = 0; DROP TABLE IF EXISTS ' . $table . '; SET FOREIGN_KEY_CHECKS = 1;'
            );
        }
    }

    /**
     * Executes sql in the database
     *
     * @param SpoonDatabase $database
     * @param string $sql
     */
    protected function importSQL(SpoonDatabase $database, string $sql)
    {
        $database->execute(trim($sql));
    }

    protected function loadFixtures(Client $client, array $fixtureClasses = [])
    {
        $database = $client->getContainer()->get('database');

        // make sure our database has a clean state (freshly installed Fork)
        $this->emptyTestDatabase($database);
        $kernelDir = $client->getContainer()->getParameter('kernel.project_dir') . '/app';
        $this->importSQL(
            $client->getContainer()->get('database'),
            file_get_contents($kernelDir . '/../tests/data/test_db.sql')
        );

        // load all the fixtures
        foreach ($fixtureClasses as $class) {
            $fixture = new $class();
            $fixture->load($database);
        }
    }

    /**
     * Copies the parameters.yml file to a backup version
     *
     * @param string $kernelDir
     * @param Filesystem $filesystem
     */
    protected function backupParametersFile(Filesystem $filesystem, string $kernelDir)
    {
        if ($filesystem->exists($kernelDir . '/config/parameters.yml')) {
            $filesystem->copy(
                $kernelDir . '/config/parameters.yml',
                $kernelDir . '/config/parameters.yml~backup'
            );
        }
        if ($filesystem->exists($kernelDir . '/cache/test')) {
            $filesystem->remove($kernelDir . '/cache/test');
        }
    }

    /**
     * Puts the backed up parameters.yml file back
     *
     * @param string $kernelDir
     * @param Filesystem $filesystem
     */
    protected function putParametersFileBack(Filesystem $filesystem, string $kernelDir)
    {
        if ($filesystem->exists($kernelDir . '/config/parameters.yml~backup')) {
            $filesystem->copy(
                $kernelDir . '/config/parameters.yml~backup',
                $kernelDir . '/config/parameters.yml',
                true
            );
            $filesystem->remove($kernelDir . '/config/parameters.yml~backup');
        }
        if ($filesystem->exists($kernelDir . '/cache/test')) {
            $filesystem->remove($kernelDir . '/cache/test');
        }
    }

    protected function assertIs404(Client $client)
    {
        $client->followRedirect();
        self::assertEquals(
            404,
            $client->getResponse()->getStatusCode()
        );
        self::assertStringEndsWith(
            '404',
            $client->getHistory()->current()->getUri()
        );
    }

    /**
     * Submits the form and mimics the GET parameters, since they aren't added
     * by default in the functional tests
     *
     * @param Client $client
     * @param Form $form
     * @param array $data
     */
    protected function submitForm(Client $client, Form $form, array $data = []): void
    {
        // Get parameters should be set manually. Symfony uses the request object,
        // but spoon still checks the $_GET and $_POST parameters
        foreach ($data as $key => $value) {
            $_GET[$key] = $value;
            $_POST[$key] = $value;
        }

        $client->submit($form);

        foreach ($data as $key => $value) {
            unset($_GET[$key]);
            unset($_POST[$key]);
        }
    }

    /**
     * Edits the data of a form
     *
     * @param Client $client
     * @param Form $form
     * @param array $data
     */
    protected function submitEditForm(Client $client, Form $form, array $data = []): void
    {
        $originalData = [];
        foreach ($form->all() as $fieldName => $formField) {
            $originalData[$fieldName] = $formField->getValue();
        }

        $data = array_merge($originalData, $data);

        $this->submitForm($client, $form, $data);
    }

    /**
     * Do a request with the given GET parameters
     *
     * @param Client $client
     * @param string $url
     * @param array $data
     *
     * @return Crawler
     */
    protected function requestWithGetParameters(
        Client $client,
        string $url,
        array $data = []
    ): Crawler {
        $this->setGetParameters($data);
        $request = $client->request('GET', $url, $data);
        $this->unsetGetParameters($data);

        return $request;
    }

    /**
     * Set the GET parameters, as some of the old code relies on GET
     *
     * @param array $data
     */
    protected function setGetParameters(array $data = []): void
    {
        foreach ((array) $data as $key => $value) {
            $_GET[$key] = $value;
        }
    }

    /**
     * Unset the GET parameters, as some of the old code relies on GET
     *
     * @param array $data
     */
    protected function unsetGetParameters(array $data = []): void
    {
        if (empty($data)) {
            $_GET = [];

            return;
        }

        foreach ($data as $key => $value) {
            unset($_GET[$key]);
        }
    }

    /**
     * Logs the client in
     *
     * Logging in using the forms is tested in the Authentication module
     *
     * @param Client $client
     */
    protected function login(Client $client): void
    {
        Authentication::tearDown();
        $crawler = $client->request('GET', '/private/en/authentication');

        $form = $crawler->selectButton('login')->form();
        $this->submitForm($client, $form, [
            'form' => 'authenticationIndex',
            'backend_email' => 'noreply@fork-cms.com',
            'backend_password' => 'fork',
            'form_token' => $form['form_token']->getValue(),
        ]);
    }

    /**
     * Logs the client out
     *
     * @param Client $client
     */
    protected function logout(Client $client): void
    {
        $client->setMaxRedirects(-1);
        $client->request('GET', '/private/en/authentication/logout');
        Authentication::tearDown();
    }
}
