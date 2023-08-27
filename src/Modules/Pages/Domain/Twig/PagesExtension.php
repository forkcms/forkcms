<?php

namespace ForkCMS\Modules\Pages\Domain\Twig;

use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Pages\Domain\Page\NavigationBuilder;
use ForkCMS\Modules\Pages\Domain\Page\Page;
use ForkCMS\Modules\Pages\Domain\Revision\MenuType;
use ForkCMS\Modules\Pages\Domain\Revision\RevisionRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PagesExtension extends AbstractExtension
{
    public function __construct(
        private readonly NavigationBuilder $navigationBuilder,
        private readonly RequestStack $requestStack,
        private readonly RevisionRepository $revisionRepository,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'navigation',
                $this->getNavigation(...),
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
        ];
    }

    /** @param int[] $excludedIds */
    public function getNavigation(
        Environment $twig,
        MenuType|string $type = MenuType::MAIN,
        int|null $parentId = null,
        int $depth = 3,
        array $excludedIds = [],
        string $template = '@Pages/Frontend/Navigation.html.twig',
        bool $isSubNavigation = false,
    ): string {
        if ($depth < 1) {
            return '';
        }

        $groupedPages = $this->navigationBuilder->getActiveGroupedPages(
            $type instanceof MenuType ? $type : MenuType::from($type),
            $this->revisionRepository->find($this->requestStack->getCurrentRequest()->attributes->get('revision')),
        );

        $pages = $groupedPages[$parentId ?? 0] ?? [];
        if ($parentId === null) {
            $pages += $groupedPages[Page::PAGE_ID_HOME] ?? [];
        }
        foreach ($excludedIds as $excludedId) {
            unset($pages[$excludedId]);
        }

        return $twig->render(
            $template,
            [
                'pages' => $pages,
                'type' => $type instanceof MenuType ? $type : MenuType::from($type),
                'nextDepth' => $depth - 1,
                'excludedIds' => $excludedIds,
                'template' => $template,
                'isSubNavigation' => $isSubNavigation,
            ]
        );
    }
}
