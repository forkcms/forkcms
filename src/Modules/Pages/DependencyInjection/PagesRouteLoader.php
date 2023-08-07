<?php

namespace ForkCMS\Modules\Pages\DependencyInjection;

use ForkCMS\Core\Domain\Router\ModuleRouteProviderInterface;
use ForkCMS\Modules\Internationalisation\Domain\Locale\InstalledLocaleRepository;
use ForkCMS\Modules\Pages\Controller\PageController;
use ForkCMS\Modules\Pages\Controller\LocaleRedirectController;
use ForkCMS\Modules\Pages\Domain\Page\Page;
use ForkCMS\Modules\Pages\Domain\Revision\Revision;
use ForkCMS\Modules\Pages\Domain\Revision\RevisionRepository;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class PagesRouteLoader implements ModuleRouteProviderInterface
{
    private const FORMAT_WILDCARD_REGEX = '[^\.]+';
    public const FORMAT_REQUIREMENT = 'html|json';
    public const FORMAT_DEFAULT = 'html';

    public function __construct(
        private readonly RevisionRepository $revisionRepository,
        private readonly InstalledLocaleRepository $installedLocaleRepository,
    ) {
    }

    public function getRouteCollection(): RouteCollection
    {
        $pagesRoutes = new RouteCollection();
        /** @var Revision[] $revisions */
        $revisions = $this->revisionRepository->createQueryBuilder('r')
            ->leftJoin('r.meta', 'rm')
            ->addSelect('rm')
            ->innerJoin('r.page', 'p')
            ->addSelect('p')
            ->innerJoin('p.revisions', 'pr')
            ->addSelect('pr')
            ->leftJoin('pr.meta', 'prm')
            ->addSelect('prm')
            ->leftJoin('r.parentPage', 'pp')
            ->addSelect('pp')
            ->leftJoin('pp.revisions', 'ppr')
            ->addSelect('ppr')
            ->leftJoin('ppr.meta', 'pprm')
            ->addSelect('pprm')
            ->getQuery()
            ->getResult();

        $paths = [];
        $websiteLocales = $this->installedLocaleRepository->findForWebsite();
        foreach ($revisions as $revision) {
            $locale = $revision->getLocale();
            if (!array_key_exists($locale->value, $websiteLocales)) {
                continue;
            }
            $path = $revision->getMeta()->getSlug();
            $parentPage = $revision->getParentPage();
            while ($parentPage !== null) {
                $parentRevision = $parentPage->getActiveRevision($locale);
                if ($parentRevision->getPage()->getId() !== Page::PAGE_ID_HOME) {
                    $path = $parentRevision->getMeta()->getSlug() . '/' . $path;
                }
                $parentPage = $parentRevision->getParentPage();
            }

            if ($_ENV['SITE_MULTILINGUAL'] === 'true') {
                if ($revision->getPage()->getId() !== Page::PAGE_ID_HOME) {
                    $path = $locale->value . '/' . $path;
                } else {
                    $path = $locale->value;
                }
            }

            $routeName = $revision->getRouteName();
            $paths[$path] = [
                'path' => $path . '.{_format}',
                'name' => $routeName,
                'defaults' => [
                    '_canonical_route' => $routeName,
                    '_controller' => PageController::class,
                    '_locale' => $locale->value,
                    'revision' => $revision->getId(),
                    'navigationTitle' => $revision->getNavigationTitle(),
                    '_format' => self::FORMAT_DEFAULT,
                ],
                'requirements' => [
                    '_locale' => $locale->value,
                    '_format' => self::FORMAT_REQUIREMENT,
                ],
            ];
        }

        // Make sure we'll add the longest path first to prevent conflicts
        $keys = array_map(strlen(...), array_keys($paths));
        array_multisort($keys, SORT_DESC, $paths);
        foreach ($paths as $data) {
            $pagesRoutes->add($data['name'], new Route($data['path'], $data['defaults'], $data['requirements']));
        }

        if ($_ENV['SITE_MULTILINGUAL'] === 'true') {
            foreach ($websiteLocales as $websiteLocale => $isDefault) {
                $redirectName = LocaleRedirectController::ROUTE_LOCALE_REDIRECT . '.' . $websiteLocale;
                $pagesRoutes->add(
                    $redirectName,
                    new Route(
                        '/{_locale}/{path}.{_format}',
                        [
                            '_controller' => LocaleRedirectController::class,
                            '_locale' => $websiteLocale,
                            '_canonical_route' => $redirectName,
                            'path' => '~',
                            '_format' => self::FORMAT_DEFAULT,
                        ],
                        [
                            '_locale' => $websiteLocale,
                            '_format' => self::FORMAT_REQUIREMENT,
                            'path' => self::FORMAT_WILDCARD_REGEX,
                        ],
                    ),
                    -1
                );

                if ($isDefault) {
                    $pagesRoutes->add(
                        LocaleRedirectController::ROUTE_LOCALE_REDIRECT,
                        new Route(
                            '/{path}.{_format}',
                            [
                                '_controller' => LocaleRedirectController::class,
                                'path' => '',
                                'default_locale' => $websiteLocale,
                                '_format' => self::FORMAT_DEFAULT,
                            ],
                            [
                                'path' => self::FORMAT_WILDCARD_REGEX,
                                '_format' => self::FORMAT_REQUIREMENT,
                            ],
                        ),
                        -2
                    );
                }
            }
        }

        return $pagesRoutes;
    }
}
