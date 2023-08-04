<?php

namespace Backend\Core\Engine;

use Psr\Cache\CacheItemPoolInterface;
use SpoonDatabase;

final class NavigationCache
{
    const CACHE_KEY = 'backend_navigation';

    /**
     * @var SpoonDatabase
     */
    protected $database;

    /**
     * @var CacheItemPoolInterface
     */
    protected $cache;

    public function __construct(SpoonDatabase $database, CacheItemPoolInterface $cache)
    {
        $this->database = $database;
        $this->cache = $cache;
    }

    public function delete(): void
    {
        $this->cache->deleteItem(self::CACHE_KEY);
    }

    public function get(): array
    {
        $cachedNavigation = $this->cache->getItem(self::CACHE_KEY);
        if ($cachedNavigation->isHit()) {
            return $cachedNavigation->get();
        }

        $navigation = $this->buildNavigationTree();
        $cachedNavigation->set($navigation);
        $this->cache->save($cachedNavigation);

        return $navigation;
    }

    private function buildNavigationTree(int $parentId = 0): array
    {
        $navigationItems = $this->getNavigationItemsForParent($parentId);
        $numberOfItemsForCurrentParent = count($navigationItems);

        if ($numberOfItemsForCurrentParent === 0) {
            return [];
        }

        return array_map(
            function (array $navigationItem) {
                return $this->buildNavigationItem($navigationItem);
            },
            $navigationItems
        );
    }

    private function buildNavigationItem(array $navigationItemRecord): array
    {
        if (empty($navigationItemRecord['url'])) {
            $navigationItemRecord['url'] = $this->getNavigationUrl($navigationItemRecord['id']);
        }

        $navigationItem = [
            'url' => $navigationItemRecord['url'],
            'label' => $navigationItemRecord['label'],
        ];

        if ($navigationItemRecord['selected_for'] !== null) {
            $navigationItem['selected_for'] = unserialize($navigationItemRecord['selected_for'], ['allowed_classes' => false]);
        }

        $children = $this->buildNavigationTree($navigationItemRecord['id']);

        if (!empty($children)) {
            $navigationItem['children'] = $children;
        }

        return $navigationItem;
    }

    private function getNavigationItemsForParent(int $parentId): array
    {
        return (array) $this->database->getRecords(
            'SELECT bn.*
             FROM backend_navigation AS bn
             WHERE bn.parent_id = ?
             ORDER BY bn.sequence ASC',
            [$parentId]
        );
    }

    /**
     * Get the url of a navigation item.
     * If the item doesn't have an id, it will search recursively until it finds one.
     *
     * @param int $id The id to search for.
     *
     * @return string
     */
    private function getNavigationUrl(int $id): string
    {
        $url = (array) Model::getContainer()->get('database')->getRecord(
            'SELECT id, url FROM backend_navigation WHERE id = ?',
            [$id]
        );

        if (empty($url)) {
            return '';
        }

        if (!empty($url['url'])) {
            return $url['url'];
        }

        // get the first child as fallback
        $childId = (int) Model::getContainer()->get('database')->getVar(
            'SELECT id FROM backend_navigation WHERE parent_id = ? ORDER BY sequence ASC LIMIT 1',
            [$id]
        );

        return $this->getNavigationUrl($childId);
    }
}
