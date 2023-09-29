<?php

namespace ForkCMS\Core\tests;

use Countable;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function func_num_args;

abstract class WebTestCase extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        static::createClient();
        static::loadFixture(...static::getClassFixtures());
    }

    /** @return FixtureInterface[] */
    protected static function getClassFixtures(): array
    {
        return [];
    }

    final protected static function loadFixture(FixtureInterface ...$fixture): void
    {
        static $executor;

        if ($executor === null) {
            $executor = new ORMExecutor(self::getContainer()->get('doctrine.orm.entity_manager'));
        }

        $executor->execute($fixture, true);
    }

    /**
     * @param string[] $expectedContent
     * @param array<string, mixed> $requestParameters
     */
    final protected static function assertPageLoadedCorrectly(
        string $url,
        ?string $pageTitle = null,
        array $expectedContent = [],
        int $httpStatusCode = Response::HTTP_OK,
        string $requestMethod = Request::METHOD_GET,
        array $requestParameters = []
    ): void {
        static::assertHttpStatusCode($url, $httpStatusCode, $requestMethod, $requestParameters);
        if ($pageTitle !== null) {
            self::assertPageTitleSame($pageTitle, 'Page title is not "' . $pageTitle . '".');
        }
        static::assertPageHasContent(...$expectedContent);
    }

    final protected static function assertHasLink(string $text, string $url): void
    {
        static::assertMinCount(
            1,
            static::getCrawler()->filter('a:contains("' . $text . '")[href="' . $url . '"]')
        );
    }

    public static function assertMinCount(int $expectedCount, Countable|iterable $haystack, string $message = ''): void
    {
        static::assertGreaterThanOrEqual($expectedCount, count($haystack), $message);
    }

    public static function assertMaxCount(int $expectedCount, Countable|iterable $haystack, string $message = ''): void
    {
        static::assertLessThanOrEqual($expectedCount, $haystack, $message);
    }

    /**
     * @param string[] $expectedContent
     * @param array<string, mixed> $requestParameters
     */
    final protected static function assertClickOnLink(
        string $linkText,
        array $expectedContent,
        int $httpStatusCode = Response::HTTP_OK,
        string $requestMethod = Request::METHOD_GET,
        array $requestParameters = []
    ): void {
        static::assertPageLoadedCorrectly(
            static::getClient()->getCrawler()->selectLink($linkText)->link()->getUri(),
            null,
            $expectedContent,
            $httpStatusCode,
            $requestMethod,
            $requestParameters
        );
    }

    final protected static function assertPageHasContent(string ...$content): void
    {
        $response = static::getResponse();

        foreach ($content as $expectedContent) {
            static::assertStringContainsString($expectedContent, $response->getContent(), 'Page does not contain "' . $expectedContent . '".');
        }
    }

    final protected static function assertResponseDoesNotHaveContent(string ...$content): void
    {
        $response = static::getResponse();

        foreach ($content as $notExpectedContent) {
            static::assertStringNotContainsStringIgnoringCase($notExpectedContent, $response->getContent());
        }
    }

    final protected static function assertCurrentUrlContains(string ...$partialUrls): void
    {
        $currentUrl = static::getClient()->getHistory()->current()->getUri();
        foreach ($partialUrls as $partialUrl) {
            static::assertStringContainsString($partialUrl, $currentUrl);
        }
    }

    final protected static function assertCurrentUrlEndsWith(string $partialUrl): void
    {
        static::assertStringEndsWith($partialUrl, static::getClient()->getHistory()->current()->getUri());
    }

    final protected static function assertRedirect(
        string $initialUrl,
        string $expectedUrl,
        string $requestMethod = Request::METHOD_GET,
        array $requestParameters = [],
        int $maxRedirects = null,
        int $expectedHttpResponseCode = Response::HTTP_OK
    ): void {
        $client = static::getClient();
        $originalMaxRedirects = $client->getMaxRedirects();
        $maxRedirects !== null ? $client->setMaxRedirects($maxRedirects) : $client->followRedirects();
        try {
            static::request($requestMethod, $initialUrl, $requestParameters);
        } finally {
            $client->setMaxRedirects($originalMaxRedirects);
        }

        static::assertResponseStatusCodeSame($expectedHttpResponseCode);
        static::assertCurrentUrlContains($expectedUrl);
    }

    /** @param array<string, mixed> $requestParameters */
    final protected static function assertHttpStatusCode(
        string $url,
        int $httpStatusCode,
        string $requestMethod = Request::METHOD_GET,
        array $requestParameters = []
    ): void {
        static::request($requestMethod, $url, $requestParameters);
        static::assertResponseStatusCodeSame($httpStatusCode);
    }

    /** @param array<string, mixed> $requestParameters */
    final protected static function assertHttpStatusCode200(
        string $url,
        string $requestMethod = Request::METHOD_GET,
        array $requestParameters = []
    ): void {
        static::assertHttpStatusCode(
            $url,
            Response::HTTP_OK,
            $requestMethod,
            $requestParameters
        );
    }

    /** @param array<string, mixed> $requestParameters */
    final protected static function assertHttpStatusCode404(
        string $url,
        string $requestMethod = Request::METHOD_GET,
        array $requestParameters = []
    ): void {
        static::assertHttpStatusCode(
            $url,
            Response::HTTP_NOT_FOUND,
            $requestMethod,
            $requestParameters
        );
    }

    final protected static function getClient(KernelBrowser $newClient = null): KernelBrowser
    {
        static $client;

        if (0 < func_num_args()) {
            // avoid having to add null to the return type when clearing the client
            $oldClient = $client;
            if ($oldClient === null && $newClient === null) {
                static::fail(
                    'You are trying to clear the client, but no client has been set yet. '
                    . 'Did you forget to call "' . __CLASS__ . '::createClient()"?'
                );
            }
            $client = $newClient;

            return $client ?? $oldClient;
        }

        if (!$client instanceof KernelBrowser) {
            static::fail(
                sprintf(
                    'A client must be set to make assertions on it. Did you forget to call "%s::createClient()"?',
                    __CLASS__
                )
            );
        }

        return $client;
    }

    final protected static function getResponse(): Response
    {
        if (!$response = static::getClient()->getResponse()) {
            static::fail(
                'A client must have an HTTP Response to make assertions. Did you forget to make an HTTP request?'
            );
        }

        return $response;
    }

    /**
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $files
     * @param array<string, mixed> $server
     */
    final protected static function request(
        string $method,
        string $uri,
        array $parameters = [],
        array $files = [],
        array $server = [],
        string $content = null,
        bool $changeHistory = true
    ): Crawler {
        return static::getClient()->request(
            $method,
            $uri,
            $parameters,
            $files,
            $server,
            $content,
            $changeHistory
        );
    }

    final protected static function getRequest(): Request
    {
        if (!$request = static::getClient()->getRequest()) {
            static::fail(
                'A client must have an HTTP Request to make assertions. Did you forget to make an HTTP request?'
            );
        }

        return $request;
    }

    final protected static function getCrawler(): Crawler
    {
        if (!$crawler = static::getClient()->getCrawler()) {
            static::fail('A client must have a crawler to make assertions. Did you forget to make an HTTP request?');
        }

        return $crawler;
    }

    final protected static function createClient(array $options = [], array $server = []): KernelBrowser
    {
        return static::getClient(parent::createClient($options, $server));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        static::getClient(null);
    }
}
