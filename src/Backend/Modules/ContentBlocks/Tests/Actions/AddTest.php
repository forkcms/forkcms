<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class AddTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        $this->assertAuthenticationIsNeeded($client, '/private/en/content_blocks/index');
    }

    public function testFormIsDisplayed(Client $client): void
    {
        $this->login($client);

        $this->assertPageLoadedCorrectly(
            $client,
            '/private/en/content_blocks/add',
            [
                'Title<abbr data-toggle="tooltip" aria-label="Required field" title="Required field">*</abbr>',
                'Visible on site',
                'Add content block',
            ]
        );
    }
}
