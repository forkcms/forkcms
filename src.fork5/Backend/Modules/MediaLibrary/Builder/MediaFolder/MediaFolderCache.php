<?php

namespace Backend\Modules\MediaLibrary\Builder\MediaFolder;

use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolderRepository;
use Psr\Cache\CacheItemPoolInterface;
use stdClass;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class MediaFolderCache
{
    const CACHE_KEY = 'media_library_media_folders';

    /**
     * @var CacheItemPoolInterface|stdClass
     */
    protected $cache;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param CacheItemPoolInterface|stdClass $cache
     * @param ContainerInterface $container - We must inject the container, because otherwise we get a "circular reference exception"
     */
    public function __construct(
        $cache,
        ContainerInterface $container
    ) {
        $this->cache = $cache;
        $this->container = $container;
    }

    public function delete()
    {
        if ($this->cache instanceof CacheItemPoolInterface) {
            $this->cache->deleteItem(self::CACHE_KEY);
        }
    }

    public function get(): array
    {
        if (!$this->cache instanceof CacheItemPoolInterface) {
            return $this->buildCacheTree();
        }

        $cachedNavigation = $this->cache->getItem(self::CACHE_KEY);
        if ($cachedNavigation->isHit()) {
            return $cachedNavigation->get();
        }
        $navigation = $this->buildCacheTree();
        $cachedNavigation->set($navigation);
        $this->cache->save($cachedNavigation);

        return $navigation;
    }

    private function buildCacheTree(MediaFolder $parent = null, string $parentSlug = null): array
    {
        $navigationItems = $this->getMediaFoldersForParent($parent);
        $numberOfItemsForCurrentParent = count($navigationItems);

        if ($numberOfItemsForCurrentParent === 0) {
            return [];
        }

        return array_map(
            function (MediaFolder $navigationItem) use ($parentSlug) {
                return $this->buildCacheItem($navigationItem, $parentSlug);
            },
            $navigationItems
        );
    }

    private function buildCacheItem(MediaFolder $mediaFolder, string $parentSlug = null): MediaFolderCacheItem
    {
        $cacheItem = new MediaFolderCacheItem($mediaFolder, $parentSlug);

        $children = $this->buildCacheTree($mediaFolder, $cacheItem->slug);
        if (!empty($children)) {
            $cacheItem->setChildren($children);
        }

        return $cacheItem;
    }

    private function getMediaFoldersForParent(MediaFolder $parent = null): array
    {
        return (array) $this->container->get(MediaFolderRepository::class)->findBy(
            ['parent' => $parent],
            ['name' => 'ASC']
        );
    }
}
