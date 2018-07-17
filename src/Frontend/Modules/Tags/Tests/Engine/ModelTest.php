<?php

namespace Frontend\Modules\Tags\Tests\Engine;

use Frontend\Core\Engine\Exception as FrontendException;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Language\Locale;
use Frontend\Modules\Search\Engine\Model as SearchModel;
use Frontend\Modules\Pages\Engine\Model as PagesModel;
use Frontend\Modules\Tags\Engine\Model as TagsModel;
use Common\WebTestCase;

final class ModelTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (!defined('APPLICATION')) {
            define('APPLICATION', 'Frontend');
        }

        $client = self::createClient();
        $this->loadFixtures($client);

        if (!defined('LANGUAGE')) {
            define('LANGUAGE', $client->getContainer()->getParameter('site.default_language'));
        }

        if (!defined('FRONTEND_LANGUAGE')) {
            define('FRONTEND_LANGUAGE', $client->getContainer()->getParameter('site.default_language'));
        }
    }

    public function testCallFromInterfaceOnModuleThatDoesNotImplementIt(): void
    {
        $module = 'Search';
        $this->expectException(FrontendException::class);
        $this->expectExceptionMessage(
            'To use the tags module you need
            to implement the FrontendTagsInterface
            in the model of your module
            (' . $module . ').'
        );
        TagsModel::callFromInterface($module, SearchModel::class, 'getIdForTags', null);
    }

    public function testCallFromInterfaceOnModuleThatDoesImplementIt(): void
    {
        $module = 'Pages';
        $pages = TagsModel::callFromInterface($module, PagesModel::class, 'getForTags', [1]);

        $this->assertSame($pages[0]['title'], 'Home');
    }

    public function testGettingATagWithTheDefaultLocale(): void
    {
        $url = 'test';
        $tag = TagsModel::get($url);
        $this->assertTag($tag);
        $this->assertSame($tag['url'], $url);
    }

    public function testGettingATagWithASpecificLocale(): void
    {
        $url = 'test';
        $tag = TagsModel::get($url, Locale::fromString('en'));
        $this->assertTag($tag);
        $this->assertSame($tag['url'], $url);
    }

    private function assertTag(array $tag): void
    {
        $this->assertArrayHasKey('id', $tag);
        $this->assertArrayHasKey('language', $tag);
        $this->assertArrayHasKey('name', $tag);
        $this->assertArrayHasKey('number', $tag);
        $this->assertArrayHasKey('url', $tag);
    }
}
