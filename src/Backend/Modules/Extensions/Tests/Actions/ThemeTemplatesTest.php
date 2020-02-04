<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class ThemeTemplatesTest extends BackendWebTestCase
{
    public function testAuthenticationIsNeeded(Client $client): void
    {
        self::assertAuthenticationIsNeeded($client, '/private/en/extensions/theme_templates');
    }

    public function testIndexHasTemplates(Client $client): void
    {
        $this->login($client);

        $client->request('GET', '/private/en/extensions/theme_templates');
        self::assertPageLoadedCorrectly(
            $client,
            '/private/en/extensions/theme_templates',
            [
                'Templates for',
                'Add template',
                'Export',
            ]
        );
        self::assertResponseDoesNotHaveContent(
            $client->getResponse(),
            '<a href="/private/en/extensions/edit_theme_template?token=68ozixmy4j&amp;id=3" title="">Default</a>'
        );
    }
}
