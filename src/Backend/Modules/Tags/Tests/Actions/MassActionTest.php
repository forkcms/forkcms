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
        $this->assertAuthenticationIsNeeded($client, '/private/en/tags/mass_action');
    }

    public function testActionIsRequired(Client $client): void
    {
        $this->login($client);

        $client->setMaxRedirects(1);
        $this->assertHttpStatusCode200($client, '/private/en/tags/mass_action');
        $this->assertCurrentUrlEndsWith($client, '&error=no-action-selected');
    }

    public function testIdsAreRequired(Client $client): void
    {
        $this->login($client);

        $client->setMaxRedirects(1);
        $this->assertHttpStatusCode200($client, '/private/en/tags/mass_action?action=delete');
        $this->assertCurrentUrlEndsWith($client, '&error=no-selection');
    }

    public function testDeletingOneTag(Client $client): void
    {
        $this->login($client);

        $client->setMaxRedirects(1);
        $this->assertHttpStatusCode200($client, '/private/en/tags/mass_action?action=delete&id[]=2');
        $this->assertCurrentUrlEndsWith($client, '&report=deleted');
        $response = $client->getResponse();
        $this->assertResponseHasContent(
            $response,
            'id=' . LoadTagsTags::TAGS_TAG_1_ID . '" title="">' . LoadTagsTags::TAGS_TAG_1_NAME . '</a>'
        );
        $this->assertResponseDoesNotHaveContent(
            $response,
            'id=' . LoadTagsTags::TAGS_TAG_2_ID . '" title="">' . LoadTagsTags::TAGS_TAG_2_NAME . '</a>'
        );
    }

    public function testDeletingAllTags(Client $client): void
    {
        $this->login($client);

        $client->setMaxRedirects(1);
        $this->assertHttpStatusCode200(
            $client,
            '/private/en/tags/mass_action?action=delete&id[]=' . LoadTagsTags::TAGS_TAG_1_ID
            . '&id[]=' . LoadTagsTags::TAGS_TAG_2_ID
        );
        $this->assertCurrentUrlEndsWith($client, '&report=deleted');

        $response = $client->getResponse();
        $this->assertResponseHasContent($response, '<p>There are no tags yet.</p>');
        $this->assertResponseDoesNotHaveContent(
            $response,
            'id=' . LoadTagsTags::TAGS_TAG_1_ID . '" title="">' . LoadTagsTags::TAGS_TAG_1_NAME . '</a>',
            'id=' . LoadTagsTags::TAGS_TAG_2_ID . '" title="">' . LoadTagsTags::TAGS_TAG_2_NAME . '</a>'
        );
    }
}
