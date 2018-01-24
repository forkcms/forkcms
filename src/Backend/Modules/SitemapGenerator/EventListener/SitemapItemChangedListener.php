<?php

namespace Backend\Modules\SitemapGenerator\EventListener;

use Backend\Modules\SitemapGenerator\Domain\Command\GenerateSitemap;
use Backend\Modules\SitemapGenerator\Domain\SitemapEntry;
use Backend\Modules\SitemapGenerator\Domain\SitemapEntryRepository;
use Backend\Modules\SitemapGenerator\Domain\SitemapItemChanged;
use SimpleBus\Message\Bus\MessageBus;

final class SitemapItemChangedListener
{
    /** @var SitemapEntryRepository */
    private $sitemapEntryRepository;

    /** @var MessageBus */
    private $commandBus;

    public function __construct(SitemapEntryRepository $sitemapEntryRepository, MessageBus $commandBus)
    {
        $this->sitemapEntryRepository = $sitemapEntryRepository;
        $this->commandBus = $commandBus;
    }

    public function onSitemapItemChanged(SitemapItemChanged $event)
    {
        $currentItem = $this->getCurrentItem($event);

        if (!$currentItem instanceof SitemapEntry) {
            $this->sitemapEntryRepository->add(SitemapEntry::fromEvent($event));

            return;
        }

        $this->update($currentItem, $event);
    }

    private function update(SitemapEntry $currentItem, SitemapItemChanged $event): void
    {
        /** @var SitemapEntry[] $children */
        $children = $this->sitemapEntryRepository->getChildren($currentItem);

        foreach ($children as $child) {
            $newUrl = str_replace($currentItem->getUrl(), $event->getUrl(), $child->getUrl());
            $child->update(
                $child->getSlug(),
                $newUrl
            );
        }

        $currentItem->update(
            $event->getSlug(),
            $event->getUrl()
        );

        $this->sitemapEntryRepository->update();

        $this->commandBus->handle(new GenerateSitemap());
    }

    private function getCurrentItem(SitemapItemChanged $event): ?SitemapEntry
    {
        return $this->sitemapEntryRepository->findOneBy(
            [
                'module' => $event->getModule(),
                'entity' => $event->getEntity(),
                'otherId' => $event->getId(),
                'language' => $event->getLanguage(),
            ]
        );
    }
}
