<?php

namespace Backend\Modules\SitemapGenerator\Domain\Command;

use Backend\Modules\SitemapGenerator\Domain\SitemapEntry;
use Backend\Modules\SitemapGenerator\Domain\SitemapEntryRepository;
use Symfony\Component\Filesystem\Filesystem;
use Thepixeldeveloper\Sitemap\Output;
use Thepixeldeveloper\Sitemap\Url;
use Thepixeldeveloper\Sitemap\Urlset;

final class GenerateSitemapHandler
{
    /** @var Filesystem */
    private $filesystem;

    /** @var SitemapEntryRepository */
    private $sitemapEntryRepository;

    /** @var string */
    private $rootPath;

    public function __construct(
        Filesystem $filesystem,
        SitemapEntryRepository $sitemapEntryRepository,
        string $rootPath
    ) {
        $this->filesystem = $filesystem;
        $this->sitemapEntryRepository = $sitemapEntryRepository;
        $this->rootPath = $rootPath;
    }

    public function handle(GenerateSitemap $command): void
    {
        /** @var SitemapEntry[] $entries */
        $entries = $this->sitemapEntryRepository->findAll();

        $urlSet = new Urlset();

        foreach ($entries as $entry) {
            $urlSet->addUrl(new Url($entry->getUrl()));
        }

        $output = new Output();

        $this->filesystem->dumpFile($this->rootPath . '/sitemap.xml', $output->getOutput($urlSet));
    }
}
