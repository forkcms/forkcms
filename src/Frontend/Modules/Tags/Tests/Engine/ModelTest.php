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
        $this->assertSame($tag['language'], 'en');
    }

    public function testGetAllTags(): void
    {
        $this->assertTag(TagsModel::getAll()[0], ['url', 'name', 'number']);
    }

    public function testGetMostUsed(): void
    {
        $this->assertEmpty(TagsModel::getMostUsed(0), 'Most used limit isn\'t respected');
        $mostUsedTags = TagsModel::getMostUsed(2);
        $this->assertTag($mostUsedTags[0], ['url', 'name', 'number']);
        $this->assertTag($mostUsedTags[1], ['url', 'name', 'number']);
        $this->assertTrue($mostUsedTags[0]['number'] >= $mostUsedTags[1]['number'], 'Tags not sorted by usage');
    }

    public function testGetForItemWithDefaultLocale(): void
    {
        $tags = TagsModel::getForItem('Pages', 1);
        $this->assertTag($tags[0], ['name', 'full_url', 'url']);
    }

    public function testGetForItemWithSpecificLocale(): void
    {
        $tags = TagsModel::getForItem('Pages', 1, Locale::fromString('en'));
        $this->assertTag($tags[0], ['name', 'full_url', 'url']);
    }

    public function testGetForMultipleItemsWithDefaultLocale(): void
    {
        $tags = TagsModel::getForMultipleItems('Pages', [1, 2]);
        $this->assertArrayHasKey(1, $tags);
        $this->assertArrayHasKey(2, $tags);
        $this->assertTag($tags[1][0], ['name', 'other_id', 'url', 'full_url']);
        $this->assertTag($tags[2][0], ['name', 'other_id', 'url', 'full_url']);
    }

    public function testGetForMultipleItemsSpecificLocale(): void
    {
        $tags = TagsModel::getForMultipleItems('Pages', [1, 2], Locale::fromString('en'));
        $this->assertArrayHasKey(1, $tags);
        $this->assertArrayHasKey(2, $tags);
        $this->assertTag($tags[1][0], ['name', 'other_id', 'url', 'full_url']);
        $this->assertTag($tags[2][0], ['name', 'other_id', 'url', 'full_url']);
    }

    private function assertTag(array $tag, array $keys = ['id', 'language', 'name', 'number', 'url']): void
    {
        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $tag);
        }
    }
}
