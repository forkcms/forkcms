<?php

namespace Backend\Modules\Tags\Tests\Action;

use Backend\Modules\Tags\DataFixtures\LoadTagsModulesTags;
use Backend\Modules\Tags\DataFixtures\LoadTagsTags;
use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class EditTest extends BackendWebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures(
            $this->getProvidedData()[0],
            [
                LoadTagsTags::class,
                LoadTagsModulesTags::class,
            ]
        );
    }

    public function testAuthenticationIsNeeded(Client $client): void
    {
        self::assertAuthenticationIsNeeded($client, '/private/en/tags/edit?id=' . LoadTagsTags::TAGS_TAG_1_ID);
    }

    public function testWeCanGoToEditFromTheIndexPage(Client $client): void
    {
        $this->login($client);

        self::assertPageLoadedCorrectly($client, '/private/en/tags/index', [LoadTagsTags::TAGS_TAG_2_NAME]);
        self::assertClickOnLink($client, LoadTagsTags::TAGS_TAG_2_NAME, [LoadTagsTags::TAGS_TAG_2_NAME]);
        self::assertCurrentUrlContains($client, '&id=' . LoadTagsTags::TAGS_TAG_2_ID);
    }

    public function testEditingOurTag(Client $client): void
    {
        $this->login($client);

        self::assertPageLoadedCorrectly(
            $client,
            '/private/en/tags/edit?id=' . LoadTagsTags::TAGS_TAG_1_ID,
            [
                'form method="post" action="/private/en/tags/edit?id=' . LoadTagsTags::TAGS_TAG_1_ID . '" id="edit"',
            ]
        );

        $form = $this->getFormForSubmitButton($client, 'Save');
        $client->setMaxRedirects(1);
        $this->submitEditForm(
            $client,
            $form,
            [
                'name' => 'Edited tag for functional tests',
            ]
        );

        // we should get a 200 and be redirected to the index page
        self::assertIs200($client);
        // our url and our page should contain the new title of our blogpost
        self::assertCurrentUrlContains(
            $client,
            '/private/en/tags/index',
            '&report=edited&var=Edited%20tag%20for%20functional%20tests&highlight=row-1'
        );
        self::assertResponseHasContent($client->getResponse(), 'Edited tag for functional tests');
    }

    public function testSubmittingInvalidData(Client $client): void
    {
        $this->login($client);

        self::assertPageLoadedCorrectly(
            $client,
            '/private/en/tags/edit?id=' . LoadTagsTags::TAGS_TAG_1_ID,
            [
                'Save',
                LoadTagsTags::TAGS_TAG_1_NAME,
            ]
        );

        $form = $this->getFormForSubmitButton($client, 'Save');
        $this->submitEditForm(
            $client,
            $form,
            [
                'name' => '',
            ]
        );

        // we should get a 200 and be redirected to the index page
        self::assertIs200($client);
        self::assertCurrentUrlContains($client, '/private/en/tags/edit');

        // our page shows an overal error message and a specific one
        self::assertResponseHasContent($client->getResponse(), 'Something went wrong', 'Please provide a name.');
    }

    public function testInvalidIdShouldShowAnError(Client $client): void
    {
        $this->login($client);
        self::assertGetsRedirected($client, '/private/en/tags/edit?id=12345678', '/private/en/tags/index');
        self::assertCurrentUrlContains($client, 'error=non-existing');
    }
}
