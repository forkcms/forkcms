<?php

namespace ForkCMS\Modules\Backend\tests\Backend\Actions;

use ForkCMS\Modules\Backend\DataFixtures\UserFixture;
use ForkCMS\Modules\Backend\DataFixtures\UserGroupFixture;
use ForkCMS\Modules\Backend\tests\BackendWebTestCase;
use ForkCMS\Modules\ContentBlocks\DataFixtures\ContentBlockFixture;

final class ContentBlockDeleteTest extends BackendWebTestCase
{
    protected const TEST_URL = '/private/en/content-blocks/content-block-delete';

    public function testWithoutSubmitRedirectToIndex(): void
    {
        self::loadPage();
        self::getClient()->followRedirect();
        self::assertCurrentUrlEndsWith('/private/en/content-blocks/content-block-index');
        self::assertDataGridHasLink(ContentBlockFixture::CONTENT_BLOCK_VISIBLE_TITLE);
        self::assertDataGridHasLink(ContentBlockFixture::CONTENT_BLOCK_HIDDEN_TITLE);
        self::assertResponseContains('Sorry, the requested content block could not be found.');
    }

    public function testSubmittedFormDeletesContentBlock(): void
    {
        self::loadPage('/private/en/content-blocks/content-block-index');
        self::assertClickOnLink(ContentBlockFixture::CONTENT_BLOCK_VISIBLE_TITLE, []);
        self::assertCurrentUrlContains('/private/en/content-blocks/content-block-edit/');
        self::submitForm('Delete');
        self::getClient()->followRedirect();
        self::assertCurrentUrlEndsWith('/private/en/content-blocks/content-block-index');
        self::assertDataGridHasLink(ContentBlockFixture::CONTENT_BLOCK_HIDDEN_TITLE);
        self::assertDataGridNotHasLink(ContentBlockFixture::CONTENT_BLOCK_VISIBLE_TITLE);
        self::assertResponseContains('The content block "' . ContentBlockFixture::CONTENT_BLOCK_VISIBLE_TITLE . '" was deleted.');
    }

    protected static function getClassFixtures(): array
    {
        return [
            new ContentBlockFixture(),
        ];
    }
}
