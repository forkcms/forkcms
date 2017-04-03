<?php

namespace Backend\Modules\MediaLibrary\Builder\MediaFolder;

use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolderRepository;
use Psr\Cache\CacheItemPoolInterface;
use stdClass;

final class MediaFolderCache
{
    const CACHE_KEY = 'media_library_media_folders';

    /**
     * @var CacheItemPoolInterface|stdClass
     */
    protected $cache;

    /**
     * @var MediaFolderRepository
     */
    protected $mediaFolderRepository;

    /**
     * @param CacheItemPoolInterface|stdClass $cache
     * @param MediaFolderRepository $mediaFolderRepository
     */
    public function __construct(
        $cache,
        MediaFolderRepository $mediaFolderRepository
    ) {
        $this->cache = $cache;
        $this->mediaFolderRepository = $mediaFolderRepository;
    }

    public function delete()
    {
        if ($this->cache instanceof CacheItemPoolInterface) {
            $this->cache->deleteItem(self::CACHE_KEY);
        }
    }

    /**
     * @return array
     */
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

    /**
     * @param MediaFolder|null $parent
     * @param string|null $parentSlug
     * @return array
     */
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

    /**
     * @param MediaFolder $mediaFolder
     * @param string|null $parentSlug
     * @return MediaFolderCacheItem
     */
    private function buildCacheItem(MediaFolder $mediaFolder, string $parentSlug = null): MediaFolderCacheItem
    {
        $cacheItem = new MediaFolderCacheItem($mediaFolder, $parentSlug);

        $children = $this->buildCacheTree($mediaFolder, $cacheItem->slug);
        if (!empty($children)) {
            $cacheItem->setChildren($children);
        }

        return $cacheItem;
    }

    /**
     * @param MediaFolder $parent
     * @return array
     */
    private function getMediaFoldersForParent(MediaFolder $parent = null): array
    {
        return (array) $this->mediaFolderRepository->findBy(['parent' => $parent], ['name' => 'ASC']);
    }
}
