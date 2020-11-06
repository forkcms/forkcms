<?php

namespace Backend\Modules\Tags\Tests\Action;

use Backend\Modules\Tags\DataFixtures\LoadTagsModulesTags;
use Backend\Modules\Tags\DataFixtures\LoadTagsTags;
use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class MassActionTest extends BackendWebTestCase
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
        self::assertAuthenticationIsNeeded(
            $client,
            $this->appendCsrfTokenToUrl($client, '/private/en/tags/mass_action')
        );
    }

    public function testActionIsRequired(Client $client): void
    {
        $this->login($client);

        $client->setMaxRedirects(1);
        self::assertHttpStatusCode200($client, $this->appendCsrfTokenToUrl($client, '/private/en/tags/mass_action'));
        self::assertCurrentUrlEndsWith($client, '&error=no-action-selected');
    }

    public function testIdsAreRequired(Client $client): void
    {
        $this->login($client);

        $client->setMaxRedirects(1);
        self::assertHttpStatusCode200(
            $client,
            $this->appendCsrfTokenToUrl($client, '/private/en/tags/mass_action?action=delete')
        );
        self::assertCurrentUrlEndsWith($client, '&error=no-selection');
    }

    public function testDeletingOneTag(Client $client): void
    {
        $this->login($client);

        $client->setMaxRedirects(1);
        self::assertHttpStatusCode200(
            $client,
            $this->appendCsrfTokenToUrl($client, '/private/en/tags/mass_action?action=delete&id[]=2')
        );
        self::assertCurrentUrlEndsWith($client, '&report=deleted');
        $response = $client->getResponse();
        self::assertResponseHasContent(
            $response,
            'id=' . LoadTagsTags::TAGS_TAG_1_ID . '" title="">' . LoadTagsTags::TAGS_TAG_1_NAME . '</a>'
        );
        self::assertResponseDoesNotHaveContent(
            $response,
            'id=' . LoadTagsTags::TAGS_TAG_2_ID . '" title="">' . LoadTagsTags::TAGS_TAG_2_NAME . '</a>'
        );
    }

    public function testDeletingAllTags(Client $client): void
    {
        $this->login($client);

        $client->setMaxRedirects(1);
        self::assertHttpStatusCode200(
            $client,
            $this->appendCsrfTokenToUrl(
                $client,
                '/private/en/tags/mass_action?action=delete&id[]=' . LoadTagsTags::TAGS_TAG_1_ID
                . '&id[]=' . LoadTagsTags::TAGS_TAG_2_ID
            )
        );
        self::assertCurrentUrlEndsWith($client, '&report=deleted');

        $response = $client->getResponse();
        self::assertResponseHasContent($response, '<strong>There are no tags yet.</strong>');
        self::assertResponseDoesNotHaveContent(
            $response,
            'id=' . LoadTagsTags::TAGS_TAG_1_ID . '" title="">' . LoadTagsTags::TAGS_TAG_1_NAME . '</a>',
            'id=' . LoadTagsTags::TAGS_TAG_2_ID . '" title="">' . LoadTagsTags::TAGS_TAG_2_NAME . '</a>'
        );
    }
}
