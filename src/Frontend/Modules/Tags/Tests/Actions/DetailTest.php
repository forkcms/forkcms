<?php

namespace Frontend\Modules\Tags\Actions;

use Backend\Modules\Tags\DataFixtures\LoadTagsModulesTags;
use Backend\Modules\Tags\DataFixtures\LoadTagsTags;
use Frontend\Core\Tests\FrontendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class DetailTest extends FrontendWebTestCase
{
    public function testTagsHaveDetailPage(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadTagsTags::class,
                LoadTagsModulesTags::class,
            ]
        );

        $this->assertHttpStatusCode200($client, '/en/tags');
        $this->assertClickOnLink(
            $client,
            LoadTagsTags::TAGS_TAG_2_NAME,
            [
                '<a href="/en/tags" title="To tags overview">',
                '<h2>Pages</h2>',
                '<a href="/en/sitemap" rel="tag">',
                '<title>most used - Tags',
            ]
        );
        $this->assertCurrentUrlEndsWith($client, '/en/tags/detail/most-used');
    }

    public function testNonExistingFaqGives404(Client $client): void
    {
        $this->assertHttpStatusCode404($client, '/en/faq/detail/non-existing');
    }
}
