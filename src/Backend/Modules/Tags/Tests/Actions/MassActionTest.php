<?php

namespace Backend\Modules\Blog\Tests\Action;

use Backend\Modules\Tags\DataFixtures\LoadTagsModulesTags;
use Backend\Modules\Tags\DataFixtures\LoadTagsTags;
use Common\WebTestCase;

class MassActionTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (!defined('APPLICATION')) {
            define('APPLICATION', 'Backend');
        }

        $client = self::createClient();
        $this->loadFixtures(
            $client,
            [
                LoadTagsTags::class,
                LoadTagsModulesTags::class,
            ]
        );
    }

    public function testAuthenticationIsNeeded(): void
    {
        $client = static::createClient();
        $this->logout($client);

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/tags/mass_action');

        // we should get redirected to authentication with a reference to the wanted page
        $this->assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Ftags%2Fmass_action',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testActionIsRequired(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/tags/mass_action');

        $this->assertStringEndsWith(
            '&error=no-action-selected',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testIdsAreRequired(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/tags/mass_action?action=delete');

        $this->assertStringEndsWith(
            '&error=no-selection',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testDeletingOneTag(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/tags/mass_action?action=delete&id[]=2');

        $this->assertStringEndsWith(
            '&report=deleted',
            $client->getHistory()->current()->getUri()
        );
        $this->assertNotContains('id=2" title="">most used</a>', $client->getResponse()->getContent());
        $this->assertContains('id=1" title="">test</a>', $client->getResponse()->getContent());
    }
}
