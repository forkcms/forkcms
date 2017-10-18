<?php

namespace Backend\Modules\Sitemap\EventListener;

use Backend\Modules\Sitemap\Builder\SitemapBuilder;
use Backend\Modules\Sitemap\Manager\SitemapManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class SitemapSubscriber implements EventSubscriber
{
    /** @var SitemapBuilder */
    private $sitemapBuilder;

    /** @var SitemapManager */
    private $sitemapManager;

    public function __construct(SitemapManager $sitemapManager, SitemapBuilder $sitemapBuilder)
    {
        $this->sitemapManager = $sitemapManager;
        $this->sitemapBuilder = $sitemapBuilder;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
        ];
    }

    public function invalidate(LifecycleEventArgs $eventArgs): void
    {
        $entityClass = get_class($eventArgs->getObject());

        if ($this->sitemapManager->existsEntityClass($entityClass)) {
            $this->sitemapBuilder->buildCacheForEntityClass($entityClass);
        }
    }

    public function postPersist(LifecycleEventArgs $eventArgs): void
    {
        $this->invalidate($eventArgs);
    }

    public function postRemove(LifecycleEventArgs $eventArgs): void
    {
        $this->invalidate($eventArgs);
    }

    public function postUpdate(LifecycleEventArgs $eventArgs): void
    {
        $this->invalidate($eventArgs);
    }
}
