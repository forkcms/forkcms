<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Common\WebTestCase;

class IndexTest extends WebTestCase
{
    public function testAuthenticationIsNeeded(): void
    {
        $client = static::createClient();
        $this->logout($client);

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/content_blocks/index');

        // we should get redirected to authentication with a reference to blog index in our url
        self::assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Fcontent_blocks%2Findex',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testIndexHasNoItems(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->request('GET', '/private/en/content_blocks/index');
        self::assertContains(
            'There are no items yet.',
            $client->getResponse()->getContent()
        );

        // some stuff we also want to see on the content block index
        self::assertContains(
            'Add content block',
            $client->getResponse()->getContent()
        );
    }
}
