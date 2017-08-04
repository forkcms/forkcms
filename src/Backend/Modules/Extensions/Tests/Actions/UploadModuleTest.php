<?php

namespace Backend\Modules\ContentBlocks\Tests\Action;

use Common\WebTestCase;

class UploadModuleTest extends WebTestCase
{
    public function testAuthenticationIsNeeded(): void
    {
        $client = static::createClient();
        $this->logout($client);

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/extensions/upload_module');

        // we should get redirected to authentication with a reference to blog index in our url
        self::assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Fextensions%2Fupload_module',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testUploadPage(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->request('GET', '/private/en/extensions/upload_module');
        self::assertContains(
            'Install',
            $client->getResponse()->getContent()
        );

        self::assertContains(
            '<label for="file" class="control-label">',
            $client->getResponse()->getContent()
        );
    }
}
