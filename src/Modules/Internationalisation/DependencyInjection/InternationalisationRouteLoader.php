<?php

namespace ForkCMS\Modules\Internationalisation\DependencyInjection;

use ForkCMS\Core\Domain\Router\ModuleRouteProviderInterface;
use ForkCMS\Modules\Internationalisation\Controller\JsTranslationsController;
use ForkCMS\Modules\Internationalisation\Domain\Locale\InstalledLocaleRepository;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class InternationalisationRouteLoader implements ModuleRouteProviderInterface
{
    public const ROUTE_NAME_JS_TRANSLATIONS = 'internationalisation_js_translations';

    public function __construct(private readonly InstalledLocaleRepository $installedLocaleRepository)
    {
    }

    public function getRouteCollection(): RouteCollection
    {
        $allowedLocales = $this->installedLocaleRepository
            ->createQueryBuilder('l')
            ->select('l.locale')
            ->where('l.isEnabledForWebsite = 1')
            ->orWhere('l.isEnabledForUser = 1')
            ->orderBy('l.isDefaultForWebsite', 'DESC')
            ->getQuery()
            ->getSingleColumnResult();

        $routes = new RouteCollection();
        $defaultLocale = reset($allowedLocales);
        foreach ($allowedLocales as $locale) {
            $localeRouteName = self::ROUTE_NAME_JS_TRANSLATIONS;
            if ($locale !== $defaultLocale) {
                $localeRouteName .= '_' . $locale;
            }

            $routes->add(
                $localeRouteName,
                new Route(
                    '/_translations/{_locale}.json',
                    [
                        '_controller' => JsTranslationsController::class,
                        '_locale' => $locale,
                        '_canonical_route' => self::ROUTE_NAME_JS_TRANSLATIONS,
                    ],
                    [
                        '_locale' => $locale,
                    ],
                    methods: ['GET'],
                    condition: 'request.isXmlHttpRequest()',
                )
            );
        }

        return $routes;
    }
}
