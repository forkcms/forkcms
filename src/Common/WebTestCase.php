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
use Symfony\Component\HttpFoundation\Response;

/**
 * WebTestCase is the base class for functional tests.
 */
abstract class WebTestCase extends BaseWebTestCase
{
    protected $preserveGlobalState = false;
    protected $runTestInSeparateProcess = true;

    protected function setUp(): void
    {
        parent::setUp();

        if (!defined('LANGUAGE')) {
            define('LANGUAGE', 'en');
        }

        if (!defined('FRONTEND_LANGUAGE')) {
            define('FRONTEND_LANGUAGE', 'en');
        }

        // Inject the client in the data
        $client = static::createClient();
        $data = $this->getProvidedData();
        $data[] = $client;
        $this->__construct($this->getName(), $data, $this->dataName());

        $this->resetDataBase($client);
    }

    /**
     * Attempts to guess the kernel location.
     *
     * When the Kernel is located, the file is required.
     *
     * @return string The Kernel class name
     * @throws \RuntimeException
     *
     * @todo Remove this when Fork has no custom Kernel class anymore
     *
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

        if (!array_key_exists('environment', $options)) {
            $options['environment'] = 'test';
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
    protected function emptyTestDatabase(SpoonDatabase $database): void
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
    protected function importSQL(SpoonDatabase $database, string $sql): void
    {
        $database->execute(trim($sql));
    }

    protected function resetDataBase(Client $client): void
    {
        $database = $client->getContainer()->get('database');

        // make sure our database has a clean state (freshly installed Fork)
        $this->emptyTestDatabase($database);
        $kernelDir = $client->getContainer()->getParameter('kernel.project_dir') . '/app';
        $this->importSQL(
            $database,
            file_get_contents($kernelDir . '/../tests/data/test_db.sql')
        );
    }

    protected function loadFixtures(Client $client, array $fixtureClasses = []): void
    {
        $database = $client->getContainer()->get('database');

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
    protected function backupParametersFile(Filesystem $filesystem, string $kernelDir): void
    {
        if ($filesystem->exists($kernelDir . '/config/parameters.yml')) {
            $filesystem->copy(
                $kernelDir . '/config/parameters.yml',
                $kernelDir . '/config/parameters.yml~backup'
            );
        }

        if ($filesystem->exists($kernelDir . '/../var/cache/test')) {
            $filesystem->remove($kernelDir . '/../var/cache/test');
        }

        if ($filesystem->exists($kernelDir . '/../var/cache/test_install')) {
            $filesystem->remove($kernelDir . '/../var/cache/test_install');
        }
    }

    /**
     * Puts the backed up parameters.yml file back
     *
     * @param string $kernelDir
     * @param Filesystem $filesystem
     */
    protected function putParametersFileBack(Filesystem $filesystem, string $kernelDir): void
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

    protected static function assertIs404(Client $client): void
    {
        self::assertEquals(
            Response::HTTP_NOT_FOUND,
            $client->getResponse()->getStatusCode()
        );
    }

    protected static function assertIs200(Client $client): void
    {
        self::assertEquals(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode()
        );
    }

    /**
     * Submits the form and mimics the GET parameters, since they aren't added
     * by default in the functional tests
     *
     * @param Client $client
     * @param Form $form
     * @param array $data
     * @param bool $setValues set to true for symfony @TODO set default true in Fork 6
     */
    protected function submitForm(Client $client, Form $form, array $data = [], bool $setValues = false): void
    {
        $values = $data;
        // @TODO remove this once SpoonForm has been removed
        if (!$setValues) {
            // Get parameters should be set manually. Symfony uses the request object,
            // but spoon still checks the $_GET and $_POST parameters
            foreach ($data as $key => $value) {
                $_GET[$key] = $value;
                $_POST[$key] = $value;
            }

            $values = [];
        }

        $client->submit($form, $values);

        // @TODO remove this once SpoonForm has been removed
        if (!$setValues) {
            foreach ($data as $key => $value) {
                unset($_GET[$key]);
                unset($_POST[$key]);
            }
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
        $this->logout($client);
        self::assertHttpStatusCode200($client, '/private/en/authentication');
        $form = $this->getFormForSubmitButton($client, 'login');
        $this->submitForm(
            $client,
            $form,
            [
                'form' => 'authenticationIndex',
                'backend_email' => 'noreply@fork-cms.com',
                'backend_password' => 'fork',
                'form_token' => $form['form_token']->getValue(),
            ]
        );
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

    protected static function assertGetsRedirected(
        Client $client,
        string $initialUrl,
        string $expectedUrl,
        string $requestMethod = 'GET',
        array $requestParameters = [],
        int $maxRedirects = null,
        int $expectedHttpResponseCode = Response::HTTP_OK
    ): void {
        $maxRedirects !== null ? $client->setMaxRedirects($maxRedirects) : $client->followRedirects();

        $client->request($requestMethod, $initialUrl, $requestParameters);

        $response = $client->getResponse();
        self::assertNotNull($response, 'No response received');

        self::assertCurrentUrlContains($client, $expectedUrl);
        self::assertEquals($expectedHttpResponseCode, $response->getStatusCode());
    }

    /**
     * @param Client $client
     * @param string $url
     * @param string[] $expectedContent
     * @param int $httpStatusCode
     * @param string $requestMethod
     * @param array $requestParameters
     */
    protected static function assertPageLoadedCorrectly(
        Client $client,
        string $url,
        array $expectedContent,
        int $httpStatusCode = Response::HTTP_OK,
        string $requestMethod = 'GET',
        array $requestParameters = []
    ): void {
        self::assertHttpStatusCode($client, $url, $httpStatusCode, $requestMethod, $requestParameters);
        $response = $client->getResponse();

        self::assertNotNull($response, 'No response received');
        self::assertResponseHasContent($response, ...$expectedContent);
    }

    /**
     * @param Client $client
     * @param string $linkText
     * @param string[] $expectedContent
     * @param int $httpStatusCode
     * @param string $requestMethod
     * @param array $requestParameters
     */
    protected static function assertClickOnLink(
        Client $client,
        string $linkText,
        array $expectedContent,
        int $httpStatusCode = Response::HTTP_OK,
        string $requestMethod = 'GET',
        array $requestParameters = []
    ): void {
        self::assertPageLoadedCorrectly(
            $client,
            $client->getCrawler()->selectLink($linkText)->link()->getUri(),
            $expectedContent,
            $httpStatusCode,
            $requestMethod,
            $requestParameters
        );
    }

    protected static function assertResponseHasContent(Response $response, string ...$content): void
    {
        foreach ($content as $expectedContent) {
            self::assertStringContainsString($expectedContent, $response->getContent());
        }
    }

    protected static function assertResponseDoesNotHaveContent(Response $response, string ...$content): void
    {
        foreach ($content as $notExpectedContent) {
            self::assertStringNotContainsString($notExpectedContent, $response->getContent());
        }
    }

    protected static function assertCurrentUrlContains(Client $client, string ...$partialUrls): void
    {
        foreach ($partialUrls as $partialUrl) {
            self::assertStringContainsString($partialUrl, $client->getHistory()->current()->getUri());
        }
    }

    protected static function assertCurrentUrlEndsWith(Client $client, string $partialUrl): void
    {
        self::assertStringEndsWith($partialUrl, $client->getHistory()->current()->getUri());
    }

    protected static function assertHttpStatusCode(
        Client $client,
        string $url,
        int $httpStatusCode,
        string $requestMethod = 'GET',
        array $requestParameters = []
    ): void {
        $client->request($requestMethod, $url, $requestParameters);
        $response = $client->getResponse();
        self::assertNotNull($response, 'No response received');
        self::assertEquals($httpStatusCode, $response->getStatusCode());
    }

    protected static function assertHttpStatusCode200(
        Client $client,
        string $url,
        string $requestMethod = 'GET',
        array $requestParameters = []
    ): void {
        self::assertHttpStatusCode(
            $client,
            $url,
            Response::HTTP_OK,
            $requestMethod,
            $requestParameters
        );
    }

    protected static function assertHttpStatusCode404(
        Client $client,
        string $url,
        string $requestMethod = 'GET',
        array $requestParameters = []
    ): void {
        self::assertHttpStatusCode(
            $client,
            $url,
            Response::HTTP_NOT_FOUND,
            $requestMethod,
            $requestParameters
        );
    }

    protected function getFormForSubmitButton(Client $client, string $buttonText, string $filterSelector = null): Form
    {
        $crawler = $client->getCrawler();

        if ($filterSelector !== null) {
            $crawler = $crawler->filter($filterSelector);
        }

        return $crawler->selectButton($buttonText)->form();
    }
}
