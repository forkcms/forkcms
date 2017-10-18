<?php

namespace Backend\Modules\Sitemap\Provider;

use Backend\Modules\Sitemap\Domain\SitemapRowCollection\SitemapRowCollection;

interface SitemapProviderInterface
{
    public function getEntityClass(): string;
    public function getRows(string $locale): SitemapRowCollection;
}
