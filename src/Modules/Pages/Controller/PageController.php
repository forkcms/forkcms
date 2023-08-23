<?php

namespace ForkCMS\Modules\Pages\Controller;

use ForkCMS\Core\Domain\Header\Breadcrumb\Breadcrumb;
use ForkCMS\Core\Domain\Header\Header;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleSettings;
use ForkCMS\Modules\Frontend\Domain\Block\Block;
use ForkCMS\Modules\Frontend\Domain\Block\BlockControllerInterface;
use ForkCMS\Modules\Frontend\Domain\Twig\FrontendGlobals;
use ForkCMS\Modules\Internationalisation\Domain\Locale\InstalledLocaleRepository;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Pages\Domain\Page\NavigationBuilder;
use ForkCMS\Modules\Pages\Domain\Page\Page;
use ForkCMS\Modules\Pages\Domain\Revision\MenuType;
use ForkCMS\Modules\Pages\Domain\Revision\Revision;
use ForkCMS\Modules\Pages\Domain\RevisionBlock\RevisionBlock;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;

final class PageController
{
    public function __construct(
        private readonly ServiceLocator $frontendBlocks,
        private readonly SerializerInterface $serializer,
        private readonly Environment $twig,
        private readonly ModuleSettings $moduleSettings,
        private readonly NavigationBuilder $navigationBuilder,
        private readonly RouterInterface $router,
        private readonly InstalledLocaleRepository $installedLocaleRepository,
        private readonly FrontendGlobals $frontendGlobals,
        private readonly Header $header,
    ) {
    }

    public function __invoke(Request $request, Revision $revision): Response
    {
        $pagesModuleName = ModuleName::fromString('Pages');
        $allowedFormats = $this->moduleSettings->get($pagesModuleName, 'enabled_extensions');
        $format = $request->getPreferredFormat();
        $hasJsonResponse = $request->getPreferredFormat() === 'json';
        if (!in_array($format, $allowedFormats, true)) {
            throw new NotFoundHttpException('Page not found');
        }
        $this->parseRevision($request, $revision);
        $this->parseFooterLinks();
        $this->parseLocales($request);
        $this->buildBreadcrumbs($revision);
        $this->frontendGlobals->addGlobals();

        $revisionContext = [
            'positions' => [],
            'template' => $revision->getThemeTemplate()->getTemplatePath(),
        ];

        $response = $hasJsonResponse ? new JsonResponse() : new Response();

        /** @var array<string, array<int,RevisionBlock>> $positions */
        $positions = [];
        foreach ($revision->getBlocks() as $revisionBlock) {
            $positions[$revisionBlock->getPosition()][] = $revisionBlock;
        }
        foreach ($positions as $position => $revisionBlocks) {
            $revisionContext['positions'][$position] = [];
            foreach ($revisionBlocks as $revisionBlock) {
                $block = $revisionBlock->getBlock();
                if ($block instanceof Block) {
                    $blockName = (string) $block;
                    if ($this->frontendBlocks->has($blockName)) {
                        /** @var BlockControllerInterface $blockController */
                        $blockController = $this->frontendBlocks->get($blockName);
                        if ($hasJsonResponse) {
                            $revisionContext['positions'][$position][] = [
                                'block' => $blockName,
                                'content' => $blockController($request, $response, $block),
                            ];
                        } else {
                            $revisionContext['positions'][$position][] = $blockController($request, $response, $block);
                        }
                        $responseOverride = $blockController->getResponseOverride();
                        if ($responseOverride !== null) {
                            return $responseOverride;
                        }
                    }
                }
            }
        }

        if ($hasJsonResponse) {
            $twigGlobals = $this->twig->getGlobals();
            unset($twigGlobals['app']);
            $response->setJson($this->serializer->serialize($revisionContext + $twigGlobals, 'json'));

            return $response;
        }

        $response->setContent(
            $this->twig->render(
                $this->twig->createTemplate($this->twig->render('@Pages/_page_blocks.html.twig', $revisionContext)),
                $revisionContext
            )
        );

        return $response;
    }

    private function parseRevision(Request $request, Revision $revision): void
    {
        $frontendModuleName = ModuleName::fromString('Frontend');
        $this->twig->addGlobal(
            'siteTitle',
            $this->moduleSettings->get(
                $frontendModuleName,
                'site_title_' . $request->getLocale(),
                $_ENV['SITE_DEFAULT_TITLE']
            )
        );
        $this->twig->addGlobal('contentTitle', $revision->getTitle()); // @todo make it overwritable
        $this->twig->addGlobal('hideContentTitle', false);
    }

    protected function parseFooterLinks(): void
    {
        $pages = $this->navigationBuilder->getTree(Locale::request())[MenuType::FOOTER->value]['pages'] ?? [];
        $footerLinks = [];
        foreach ($pages as $menuItem) {
            /** @var Revision $revision */
            $revision = $menuItem['page']->getActiveRevision();
            $footerLinks[] = [
                'navigationTitle' => $revision->getNavigationTitle(),
                'url' => $this->router->generate($revision->getRouteName()),
                'linkClass' => $revision->getSetting('linkClass'),
                'rel' => $revision->getRel(),
            ];
        }

        $this->twig->addGlobal('footerLinks', $footerLinks);
    }

    protected function parseLocales(Request $request): void
    {
        $locales = [];
        $websiteLocales = $this->installedLocaleRepository->findForWebsite();
        foreach (array_keys($websiteLocales) as $locale) {
            try {
                $url = $this->router->generate(
                    str_replace(
                        '.' . $request->getLocale(),
                        '.' . $locale,
                        $request->attributes->get('_route')
                    )
                );
            } catch (RouteNotFoundException) {
                $url = $this->router->generate(Page::getRouteNameForIdAndLocale(Page::PAGE_ID_HOME, $locale));
            }
            $locales[] = [
                'locale' => Locale::from($locale),
                'url' => $url,
                'active' => $locale === $request->getLocale(),
            ];
        }
        $this->twig->addGlobal('locales', $locales);
    }

    private function buildBreadcrumbs(Revision $revision): void
    {
        $pages = array_reverse($this->navigationBuilder->getActivePages($revision), true);
        if (!array_key_exists(Page::PAGE_ID_HOME, $pages)) {
            $homeRoute = $this->router->generate(
                Page::getRouteNameForIdAndLocale(Page::PAGE_ID_HOME, $revision->getLocale())
            );
            $this->header->breadcrumbs->add(
                new Breadcrumb(
                    $this->router->match($homeRoute)['navigationTitle'],
                    $homeRoute
                )
            );
        } elseif (count($pages) === 1) {
            return;
        }
        foreach ($pages as $page) {
            $revision = $page->getActiveRevision();
            $this->header->breadcrumbs->add(
                new Breadcrumb(
                    $revision->getNavigationTitle(),
                    $this->router->generate(
                        Page::getRouteNameForIdAndLocale($page->getId(), $revision->getLocale())
                    )
                )
            );
        }
    }
}
