<?php

namespace Common;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\Finder\Finder;
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
    protected static function getKernelClass()
    {
        $dir = isset($_SERVER['KERNEL_DIR']) ? $_SERVER['KERNEL_DIR'] : static::getPhpUnitXmlDir();

        $finder = new Finder();
        $finder->name('AppKernel.php')->depth(0)->in($dir);
        $results = iterator_to_array($finder);
        if (!count($results)) {
            throw new \RuntimeException(
                'Either set KERNEL_DIR in your phpunit.xml according to http://symfony.com/doc/current/book/testing.html#your-first-functional-test or override the WebTestCase::createKernel() method.'
            );
        }

        $file = current($results);
        $class = $file->getBasename('.php');

        require_once $file;

        return $class;
    }

    /**
     * Creates a Client.
     *
     * @param array $options An array of options to pass to the createKernel class
     * @param array $server  An array of server parameters
     *
     * @return Client A Client instance
     */
    protected static function createClient(array $options = array(), array $server = array())
    {
        if (null !== static::$kernel) {
            static::$kernel->shutdown();
        }

        static::$kernel = static::createKernel($options);
        \BaseModel::setContainer(static::$kernel->getContainer());
        $client = static::$kernel->getContainer()->get('test.client');
        $client->setServerParameters($server);

        return $client;
    }

    /**
     * Fully empties the test database
     *
     * @param \SpoonDatabase $database
     */
    protected function emptyTestDatabase($database)
    {
        foreach ($database->getTables() as $table) {
            $database->drop($table);
        }
    }

    /**
     * Executes sql in the database
     *
     * @param \SpoonDatabase $database
     * @param string $sql
     */
    protected function importSQL($database, $sql)
    {
        $database->execute(trim($sql));
    }

    /**
     * @param Client $client
     * @param array $fixtureClasses
     */
    protected function loadFixtures($client, $fixtureClasses = array())
    {
        $database = $client->getContainer()->get('database');

        // make sure our database has a clean state (freshly installed Fork)
        $this->emptyTestDatabase($database);
        $kernelDir = $client->getContainer()->getParameter('kernel.root_dir');
        $this->importSQL(
            $client->getContainer()->get('database'),
            file_get_contents($kernelDir . '/../tools/test_db.sql')
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
     */
    protected function backupParametersFile($kernelDir)
    {
        $filesystem = new Filesystem();
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
     */
    protected function putParametersFileBack($kernelDir)
    {
        $filesystem = new Filesystem();
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

    /**
     * @param Client $client
     */
    protected function assertIs404($client)
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
     * @param  Client $client
     * @param  Form   $form
     * @param  array  $data
     */
    protected function submitForm(Client $client, Form $form, array $data = array())
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
     * @param  Client $client
     * @param  Form   $form
     * @param  array  $data
     */
    protected function submitEditForm(Client $client, Form $form, array $data = array())
    {
        $originalData = array();
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
     * @param array  $data
     *
     * @return Crawler
     */
    protected function requestWithGetParameters(
        Client $client,
        $url,
        $data = array()
    ) {
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
    protected function setGetParameters($data = array())
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
    protected function unsetGetParameters($data = array())
    {
        if (empty($data)) {
            $_GET = array();
        } else {
            foreach ($data as $key => $value) {
                unset($_GET[$key]);
            }
        }
    }

    /**
     * Logs in a user. We do this directly in the authentication class because
     * this is a lot faster than submitting forms and following redirects
     *
     * Logging in using the forms is tested in the Authentication module
     */
    protected function login()
    {
        Authentication::tearDown();
        Authentication::loginUser('noreply@fork-cms.com', 'fork');
    }

    /**
     * Log out a user
     */
    protected function logout()
    {
        Authentication::tearDown();
    }
}
