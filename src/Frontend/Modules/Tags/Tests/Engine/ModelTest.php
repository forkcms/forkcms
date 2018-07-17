<?php

namespace Frontend\Modules\Tags\Tests\Engine;

use Frontend\Core\Engine\Exception as FrontendException;
use Frontend\Core\Engine\Model as FrontendModel;
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
}
