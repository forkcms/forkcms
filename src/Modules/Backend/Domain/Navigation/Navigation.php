<?php

namespace ForkCMS\Modules\Backend\Domain\Navigation;

use ForkCMS\Core\Domain\Header\Breadcrumb\Breadcrumb;
use ForkCMS\Core\Domain\Header\Breadcrumb\BreadcrumbCollection;
use ForkCMS\Modules\Backend\Domain\Action\ActionSlug;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Environment;

final class Navigation
{
    public function __construct(
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly NavigationCache $navigationCache,
        private readonly RequestStack $requestStack,
        private readonly RouterInterface $router,
    ) {
    }

    public function parse(Environment $twig): void
    {
        $navigation = $this->getNavigationForAllowedModulesAndActions();
        $twig->addGlobal('navigation', $navigation);
        $selectedKey = $this->getSelectedKey($navigation);
        if ($selectedKey !== null && isset($navigation[$selectedKey])) {
            $twig->addGlobal('activeParent', $navigation[$selectedKey]['label']);
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function getNavigationForAllowedModulesAndActions(): array
    {
        static $navigation;
        if ($navigation !== null) {
            return $navigation;
        }

        $navigation = $this->addActiveStateToNavigation(
            array_filter(array_map($this->getPermissionCheckerFunction(), $this->navigationCache->get()))
        );

        return $navigation;
    }

    private function getPermissionCheckerFunction(): callable
    {
        return function (array $navigationItem) {
            if (
                !is_array($navigationItem['children'])
                || empty($navigationItem['children'])
            ) {
                return $this->getNavigationItemForCurrentlyAuthenticatedUser($navigationItem);
            }

            $navigationItem['children'] = array_filter(
                array_map($this->getPermissionCheckerFunction(), $navigationItem['children'])
            );

            if (empty($navigationItem['children'])) {
                unset($navigationItem['children']);

                return $this->getNavigationItemForCurrentlyAuthenticatedUser($navigationItem);
            }

            // reset the base url to the first allowed url
            $navigationItem['slug'] = reset($navigationItem['children'])['slug'];

            return $this->getNavigationItemForCurrentlyAuthenticatedUser($navigationItem);
        };
    }

    /**
     * @param array<string, mixed> $navigationItem
     *
     * @return array<string, mixed>
     */
    private function getNavigationItemForCurrentlyAuthenticatedUser(array $navigationItem): array
    {
        if (
            !isset($navigationItem['slug'], $navigationItem['label'])
            || !$navigationItem['slug'] instanceof ActionSlug
            || empty($navigationItem['label'])
        ) {
            return [];
        }

        if (
            $this->authorizationChecker->isGranted($navigationItem['slug']->getModuleName()->asRole())
            && $this->authorizationChecker->isGranted($navigationItem['slug']->asModuleAction()->asRole())
        ) {
            return $navigationItem;
        }

        return [];
    }

    /**
     * @param array<int, array<string, mixed>> $navigation
     *
     * @return array<int, array<string, mixed>>
     */
    private function addActiveStateToNavigation(array $navigation): array
    {
        $selectedKey = $this->getSelectedKey($navigation);

        if ($selectedKey === null) {
            return $navigation;
        }

        return array_map(
            function ($key, $navigationItem) use ($selectedKey) {
                if ($key !== $selectedKey) {
                    return $navigationItem;
                }

                $navigationItem['active'] = true;

                if (!empty($navigationItem['children'])) {
                    $navigationItem['children'] = $this->addActiveStateToNavigation($navigationItem['children']);
                }

                return $navigationItem;
            },
            array_keys($navigation),
            $navigation
        );
    }

    /**
     * Try to determine the selected state.
     *
     * @param array<string,mixed> $navigationItem
     */
    private function navigationItemMatchesActiveUrl(array $navigationItem, string $activeUrl): bool
    {
        if (
            $navigationItem['slug'] === $activeUrl
            || (isset($navigationItem['selected_for']) && in_array($activeUrl, $navigationItem['selected_for'], true))
        ) {
            return true;
        }

        if (!isset($navigationItem['children'])) {
            return false;
        }

        foreach ($navigationItem['children'] as $childNavigationItem) {
            if ($this->navigationItemMatchesActiveUrl($childNavigationItem, $activeUrl)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the selected key based on the current module/actions.
     *
     * @param array<int, array<string, mixed>> $navigation
     */
    private function getSelectedKey(array $navigation): ?int
    {
        $activeUrl = ActionSlug::fromRequest($this->requestStack->getMainRequest())->getSlug();
        foreach ($navigation as $key => $navigationItem) {
            if ($this->navigationItemMatchesActiveUrl($navigationItem, $activeUrl)) {
                return $key;
            }
        }

        return null;
    }

    public function buildBreadcrumbs(BreadcrumbCollection $breadcrumbs): void
    {
        $navigation = $this->getNavigationForAllowedModulesAndActions();
        while (count($navigation) > 0) {
            $continue = false;
            foreach ($navigation as $navigationItem) {
                if ($navigationItem['active'] ?? false) {
                    $breadcrumbs->add(
                        new Breadcrumb(
                            $navigationItem['label'],
                            ActionSlug::fromSlug($navigationItem['slug'])->generateRoute($this->router)
                        )
                    );

                    $continue = true;
                    $navigation = $navigationItem['children'] ?? [];
                    break;
                }
            }
            if (!$continue) {
                $navigation = [];
            }
        }
    }
}
