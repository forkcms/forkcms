<?php

namespace Backend\Modules\Pages\Provider;

use Backend\Modules\Pages\Engine\Model;
use Backend\Modules\Sitemap\Domain\SitemapRow\ChangeFrequency;
use Backend\Modules\Sitemap\Domain\SitemapRow\SitemapRow;
use Backend\Modules\Sitemap\Domain\SitemapRowCollection\SitemapRowCollection;
use Backend\Modules\Sitemap\Provider\SitemapProviderInterface;
use Frontend\Core\Engine\Navigation;

class PageSitemapProvider implements SitemapProviderInterface
{
    public function getEntityClass(): string
    {
        return 'Pages';
    }

    public function getRows(string $locale): SitemapRowCollection
    {
        $navigation = Navigation::getNavigation($locale);
        $collection = new SitemapRowCollection();

        /** @var string $navigationType - meta, root, page or footer*/
        foreach ($navigation as $navigationType => $items) {
            foreach ($items as $parentId => $children) {
                foreach ($children as $pageId => $page) {
                    $pageInfo = Model::get($page['page_id']);
                    $lastModifiedOn = date_timestamp_set(new \DateTime(), (int) $pageInfo['edited_on']);
                    $priority = 9;
                    $changeFrequency = ChangeFrequency::weekly();

                    // Home page
                    if (in_array($pageId, [0, 1])) {
                        $priority = 10;
                        $changeFrequency = ChangeFrequency::always();
                    }

                    $collection->add(new SitemapRow(
                        $page['full_url'],
                        $lastModifiedOn,
                        $changeFrequency,
                        $priority
                    ));
                }
            }
        }

        return $collection;
    }
}
