<?php

namespace Backend\Modules\Sitemap\Builder;

use Backend\Core\Language\Language;
use Backend\Modules\Sitemap\Domain\SitemapRow\SitemapRow;
use Backend\Modules\Sitemap\Domain\SitemapRowCollection\SitemapRowCollection;
use Backend\Modules\Sitemap\Manager\SitemapManager;
use Symfony\Component\Filesystem\Filesystem;

final class SitemapBuilder
{
    /** @var Filesystem */
    private $filesystem;

    /** @var string */
    private $path;

    /** @var SitemapManager */
    private $sitemapManager;

    public function __construct(string $path, SitemapManager $sitemapManager)
    {
        $this->path = $path;
        $this->sitemapManager = $sitemapManager;
        $this->filesystem = new Filesystem();
    }

    public function buildCache(): void
    {
        $this->dumpSitemaps();
        $this->dumpSitemapIndex();
    }

    public function buildCacheForEntityClass(string $entityClass): void
    {
        $this->dumpSitemapsForEntityName($this->sitemapManager->getEntityName($entityClass));
        $this->dumpSitemapIndex();
    }

    private function buildSitemap(string $language, string $entityName): string
    {
        /** @var SitemapRowCollection $collection */
        $collection = $this->sitemapManager->getSitemapRowCollection($language, $entityName);

        $rootNode = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');

        /** @var SitemapRow $sitemapRow */
        foreach ($collection as $sitemapRow) {
            $itemNode = $rootNode->addChild('url');
            $itemNode->addChild('loc', SITE_URL . $sitemapRow->getUrl());
            $itemNode->addChild('changefreq', $sitemapRow->getChangeFrequency()->__toString());
            $itemNode->addChild('lastmod', $sitemapRow->getLastModifiedOn()->format('Y-m-d'));
            $itemNode->addChild('priority', $sitemapRow->getPriority()/10);
        }

        return $rootNode->asXML();
    }

    private function buildSitemapIndex(): string
    {
        $rootNode = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>');

        foreach ($this->sitemapManager->getEntityNames() as $entityName) {
            foreach (Language::getActiveLanguages() as $activeLanguage) {
                $itemNode = $rootNode->addChild('sitemap');
                $itemNode->addChild('loc', SITE_URL . '/' . $this->getSitemapFilename($activeLanguage, $entityName));
                $itemNode->addChild('lastmod', $this->sitemapManager->getSitemapRowCollectionLastModifiedOn($activeLanguage, $entityName)->format('Y-m-d'));
            }
        }

        return $rootNode->asXML();
    }

    private function dumpSitemapIndex(): void
    {
        $this->filesystem->dumpFile(
            $this->path . '/sitemap.xml',
            $this->buildSitemapIndex()
        );
    }

    private function dumpSitemaps(): void
    {
        foreach ($this->sitemapManager->getEntityNames() as $entityName) {
            $this->dumpSitemapsForEntityName($entityName);
        }
    }

    private function dumpSitemapsForEntityName(string $entityName): void
    {
        foreach (Language::getActiveLanguages() as $language) {
            $this->filesystem->dumpFile(
                $this->path . '/' . $this->getSitemapFilename($language, $entityName),
                $this->buildSitemap($language, $entityName)
            );
        }
    }

    private function getSitemapFilename($language, $entityName): string
    {
        return 'sitemap_' . $language . '_' . $entityName . '.xml';
    }
}
