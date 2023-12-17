<?php

namespace ForkCMS\Modules\Backend\tests\Backend\Actions;

use ForkCMS\Modules\Backend\tests\BackendWebTestCase;

final class ContentBlockAddTest extends BackendWebTestCase
{
    protected const TEST_URL = '/private/en/content-blocks/content-block-add';

    public function testPageLoads(): void
    {
        self::loginBackendUser();
        self::assertPageLoadedCorrectly(
            self::TEST_URL,
            'Add | Content blocks | Modules | Fork CMS | Fork CMS',
            [
                'Title',
                'Content',
                'Visible on site',
            ]
        );
        self::assertHasLink('Cancel', '/private/en/content-blocks/content-block-index');
    }

    public function testEmptyFormShowsValidationErrors(): void
    {
        self::loadPage();
        self::assertEmptyFormSubmission('content_block', 2, 'Add');
    }

    public function testSubmittedFormRedirectsToIndex(): void
    {
        self::loadPage();

        self::submitForm(
            'Add',
            [
                'content_block[title]' => 'I<3ForkCMS',
                'content_block[text]' => 'It is simply amazing, you should try it too!',
            ],
        );
        self::getClient()->followRedirect();
        self::assertCurrentUrlEndsWith('/private/en/content-blocks/content-block-index');
        self::assertDataGridHasLink('I<3ForkCMS');
        self::assertResponseContains('The content block "I<3ForkCMS" was added.');
    }
}
