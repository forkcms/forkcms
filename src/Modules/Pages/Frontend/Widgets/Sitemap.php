<?php

namespace ForkCMS\Modules\Pages\Frontend\Widgets;

use ForkCMS\Modules\Frontend\Domain\Block\BlockServices;
use ForkCMS\Modules\Frontend\Domain\Widget\AbstractWidgetController;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Pages\Domain\Page\NavigationBuilder;
use ForkCMS\Modules\Pages\Domain\Revision\Revision;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * This is a widget wherein the sitemap lives
 *
 * @phpstan-import-type PageCache from NavigationBuilder
 */
class Sitemap extends AbstractWidgetController
{
    public function __construct(
        BlockServices $blockServices,
        private readonly NavigationBuilder $navigationBuilder,
    ) {
        parent::__construct($blockServices);
    }

    public function execute(Request $request, Response $response): void
    {
        $navigationTree = $this->navigationBuilder->getTree(Locale::current());
        $sitemap = [];
        $currentLocale = Locale::current();
        foreach ($navigationTree as $navigationCategory) {
            $categoryKey = $navigationCategory['label']->trans($this->translator, $currentLocale->value);
            $sitemap[$categoryKey] = $this->getPageData((array) $navigationCategory['pages'], $currentLocale);
        }

        $this->assign('sitemap', $sitemap);
    }

    /**
     * @param PageCache[] $pages
     *
     * @return array<int<0, max>, array{title: string, url: string, children: mixed[]}>
     */
    private function getPageData(array $pages, Locale $locale): array
    {
        $pageData = [];
        foreach ($pages as $page) {
            /** @var Revision $revision */
            $revision = $page['page']->getActiveRevision($locale);
            $pageData[] = [
                'title' => $revision->getNavigationTitle(),
                'url' => $this->router->generate(
                    $revision->getRouteName(),
                    referenceType: RouterInterface::ABSOLUTE_URL
                ),
                'children' => $this->getPageData((array) $page['children'], $locale),
            ];
        }

        return $pageData;
    }
}
