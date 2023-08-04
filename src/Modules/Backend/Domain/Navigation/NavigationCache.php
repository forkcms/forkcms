<?php

namespace ForkCMS\Modules\Backend\Domain\Navigation;

use ForkCMS\Modules\Backend\Domain\Action\ActionSlug;
use ForkCMS\Modules\Backend\Domain\NavigationItem\NavigationItem;
use ForkCMS\Modules\Backend\Domain\NavigationItem\NavigationItemRepository;
use Psr\Cache\CacheItemPoolInterface;

final class NavigationCache
{
    private const CACHE_KEY = 'backend_navigation';

    public function __construct(
        private readonly CacheItemPoolInterface $cache,
        private readonly NavigationItemRepository $navigationItemRepository,
    ) {
    }

    /** @return array<int, array<string, mixed>> */
    public function get(bool $invalidateCache = false): array
    {
        $cachedNavigation = $this->cache->getItem(self::CACHE_KEY);
        if (!$invalidateCache && $cachedNavigation->isHit()) {
            return $cachedNavigation->get();
        }

        $navigation = $this->buildNavigationTree();
        $cachedNavigation->set($navigation);
        $this->cache->save($cachedNavigation);

        return $navigation;
    }

    /** @return array<int, array<string, mixed>> */
    private function buildNavigationTree(): array
    {
        $navigationItems = $this->navigationItemRepository->findChildrenForParentId(null);

        if (count($navigationItems) === 0) {
            return [];
        }

        return array_filter(
            array_map(
                function (NavigationItem $navigationItem) {
                    return $this->buildNavigationItem($navigationItem);
                },
                $navigationItems
            )
        );
    }

    /** @return array<string, mixed>|null */
    private function buildNavigationItem(NavigationItem $navigationItemEntity): ?array
    {
        if (!$navigationItemEntity->isVisibleInNavigationMenu()) {
            return null;
        }

        $navigationItem = [
            'slug' => $navigationItemEntity->getFirstAvailableSlug(),
            'label' => $navigationItemEntity->getLabel(),
            'selected_for' => array_filter(
                $navigationItemEntity->getChildren()
                    ->filter(
                        static fn (NavigationItem $item): bool => !$item->isVisibleInNavigationMenu()
                            && $item->getSlug() instanceof ActionSlug
                    )
                    ->map(static fn (NavigationItem $item): ?string => $item->getSlug()?->getSlug())
                    ->toArray()
            ),
            'children' => array_filter(
                $navigationItemEntity->getChildren()->map(
                    function (NavigationItem $navigationItem) {
                        return $this->buildNavigationItem($navigationItem);
                    }
                )->toArray()
            ),
        ];
        $slug = $navigationItemEntity->getSlug();
        if ($slug instanceof ActionSlug) {
            $navigationItem['selected_for'][] = $slug->getSlug();
        }

        return $navigationItem;
    }
}
