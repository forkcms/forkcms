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

        self::assertHttpStatusCode200($client, '/en/tags');
        self::assertClickOnLink(
            $client,
            LoadTagsTags::TAGS_TAG_2_NAME,
            [
                '<a class="page-link" href="/en/tags">',
                '← To tags overview',
                '<h2 class="h3">Pages</h2>',
                '<a href="/en/sitemap" rel="tag">',
                '<title>most used - Tags',
            ]
        );
        self::assertCurrentUrlEndsWith($client, '/en/tags/detail/most-used');
    }

    public function testNonExistingFaqGives404(Client $client): void
    {
        self::assertHttpStatusCode404($client, '/en/faq/detail/non-existing');
    }
}
