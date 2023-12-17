<?php

namespace ForkCMS\Modules\Backend\tests\Backend\Actions;

use ForkCMS\Modules\Backend\tests\BackendWebTestCase;
use ForkCMS\Modules\ContentBlocks\DataFixtures\ContentBlockFixture;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;

final class ContentBlockEditTest extends BackendWebTestCase
{
    protected const TEST_URL = '/private/en/content-blocks/content-block-edit/';

    private function loadRevisionForId(int $id): ContentBlock
    {
        $contentBlock = self::getContainer()->get(ContentBlockRepository::class)->findForIdAndLocale(
            $id,
            Locale::ENGLISH
        );
        $url = self::TEST_URL . $contentBlock->getRevisionId();
        self::loadPage($url);

        return $contentBlock;
    }

    public function testPageLoads(): void
    {
        $contentBlock = $this->loadRevisionForId(ContentBlockFixture::CONTENT_BLOCK_VISIBLE_ID);
        self::assertPageLoadedCorrectly(
            self::TEST_URL . $contentBlock->getRevisionId(),
            ContentBlockFixture::CONTENT_BLOCK_VISIBLE_TITLE . ' | Edit | Content blocks | Modules | Fork CMS | Fork CMS',
            [
                'Visible on site',
                $contentBlock->getTitle(),
                $contentBlock->getText(),
            ]
        );

        self::assertHasLink('Content blocks', '/private/en/content-blocks/content-block-index');
    }

    public function testEditWithoutChanges(): void
    {
        $contentBlock = $this->loadRevisionForId(ContentBlockFixture::CONTENT_BLOCK_VISIBLE_ID);
        self::assertEmptyFormSubmission('ContentBlock', 0, 'Save');
        self::assertCurrentUrlEndsWith('/private/en/content-blocks/content-block-index');
        self::assertDataGridHasLink($contentBlock->getTitle());
        self::assertResponseContains('The content block "' . $contentBlock->getTitle() . '" was saved.');
    }

    public function testRevisionsCanBeReloaded(): void
    {
        $contentBlock = $this->loadRevisionForId(ContentBlockFixture::CONTENT_BLOCK_VISIBLE_ID);
        $originalRevisionId = $contentBlock->getRevisionId();
        self::assertResponseContains('There are no previous versions yet.');
        self::submitForm(
            'Save',
            [
                'content_block[contentBlock][tab_Content][title]' => 'I<3ForkCMS',
                'content_block[contentBlock][tab_Content][text]' => 'It is simply amazing, you should try it too!',
            ],
        );
        self::getClient()->followRedirect();
        self::assertCurrentUrlEndsWith('/private/en/content-blocks/content-block-index');
        self::assertDataGridNotHasLink(ContentBlockFixture::CONTENT_BLOCK_VISIBLE_TITLE);
        self::assertResponseContains('The content block "I<3ForkCMS" was saved.');
        self::assertClickOnLink('I<3ForkCMS', [htmlentities('I<3ForkCMS'), ContentBlockFixture::CONTENT_BLOCK_VISIBLE_TITLE]);
        self::assertStringEndsNotWith('/' . $originalRevisionId, self::getRequest()->getUri());
        self::assertResponseDoesNotHaveContent('There are no previous versions yet.');
        self::assertClickOnLink(
            ContentBlockFixture::CONTENT_BLOCK_VISIBLE_TITLE,
            [htmlentities('You\'re using an older version. Save to overwrite the current version.')]
        );
        self::assertEmptyFormSubmission('ContentBlock', 0, 'Save');
        self::assertDataGridHasLink(ContentBlockFixture::CONTENT_BLOCK_VISIBLE_TITLE);
    }

    protected static function getClassFixtures(): array
    {
        return [
            new ContentBlockFixture(),
        ];
    }
}
