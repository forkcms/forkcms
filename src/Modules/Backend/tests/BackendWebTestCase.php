<?php

namespace ForkCMS\Modules\Backend\tests;

use ForkCMS\Core\tests\WebTestCase;
use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Backend\Domain\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\DataCollectorTranslator;
use Throwable;

abstract class BackendWebTestCase extends WebTestCase
{
    public function testAuthenticationIsNeeded(): void
    {
        if (defined(static::class . '::TEST_URL') === true) {
            self::loadPage(loginBackendUser: false);
            self::assertAuthenticationIsNeeded(static::TEST_URL);
        }
    }

    public function testTranslations(): void
    {
        if (defined(static::class . '::TEST_URL') === true) {
            self::loadPage(enableProfiler: true);

            $dataCollector = self::getContainer()->get('translator.data_collector');
            $missingTranslations = array_filter($dataCollector->getCollectedMessages(), static fn (array $message): bool => $message['state'] === DataCollectorTranslator::MESSAGE_MISSING);
            self::assertSame([], $missingTranslations, 'Missing translations found.');
        }
    }

    final protected static function loadPage(?string $url = null, bool $enableProfiler = false, bool $loginBackendUser = true): void
    {
        if ($loginBackendUser) {
            self::loginBackendUser();
        }

        if (defined(static::class . '::TEST_URL') === true) {
            $url = $url ?? static::TEST_URL;

            if ($enableProfiler) {
                $url .= str_contains($url, '?') ? '&enable-framework-profiler=1' : '?enable-framework-profiler=1';
            }

            self::request(Request::METHOD_GET, $url);

            return;
        }

        if ($url === null) {
            static::fail('No URL defined.');
        }

        self::request(Request::METHOD_GET, $url);
    }

    final protected static function loginBackendUser(string $email = 'test@fork-cms.com'): User
    {
        try {
            $userRespository = static::getContainer()->get(UserRepository::class);
        } catch (Throwable) {
            static::fail('User repository not found.');
        }

        $user = $userRespository->findOneBy(['email' => $email]);
        static::assertNotNull($user, 'User with email "' . $email . '" not found.');
        static::getClient()->loginUser($user, 'backend');
        static::request(Request::METHOD_GET, static::TEST_URL);

        return $user;
    }

    final protected static function filterDataGrid(string $filter, string $value): void
    {
        $filterForm = static::getCrawler()
            ->filter('#content .fork-data-grid table input[name=filterField][value="' . $filter . '"]')
            ->closest('form')
            ?->form(['filterValue' => $value]);
        self::assertNotNull($filterForm, 'Filter ' . $filter . ' not found in data grid with value ' . $value . '.');

        static::getClient()->submit($filterForm);
    }

    final protected static function assertAuthenticationIsNeeded(
        string $url,
        string $method = Request::METHOD_GET
    ): void {
        static::assertRedirect($url, '/private/en/backend/authentication-login', $method);
    }

    final protected static function assertDataGridHasLink(string $text, ?string $url = null): void
    {
        $crawler = static::getCrawler()
            ->filter('#content .fork-data-grid table')
            ->selectLink($text);

        self::assertMinCount(1, $crawler, 'Link "' . $text . '" not found in data grid.');

        if ($url !== null) {
            self::assertSame($url, $crawler->attr('href'), 'Link "' . $text . '" has wrong URL.');
        }
    }

    final protected static function assertDataGridNotHasLink(string $text): void
    {
        $crawler = static::getCrawler()
            ->filter('#content .fork-data-grid table')
            ->selectLink($text);

        self::assertCount(0, $crawler, 'Found link "' . $text . '" in data grid, but it should not be there.');
    }

    final protected static function assertDataGridIsEmpty(): void
    {
        static::assertCount(
            1,
            static::getCrawler()->filter('#content .fork-data-grid + .empty-state'),
            'Data grid is not empty.'
        );
    }

    final protected static function assertDataGridNotEmpty(): void
    {
        static::assertCount(
            0,
            static::getCrawler()->filter('#content .fork-data-grid + .empty-state'),
            'Data grid is empty'
        );
    }
}
