<?php

namespace ForkCMS\Modules\Pages\Controller;

use ForkCMS\Modules\Internationalisation\Domain\Locale\InstalledLocaleRepository;
use ForkCMS\Modules\Pages\DependencyInjection\PagesRouteLoader;
use ForkCMS\Modules\Pages\Domain\Page\Page;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;

final class LocaleRedirectController
{
    public const ROUTE_LOCALE_REDIRECT = 'pages_page_locale_redirect';

    public function __construct(
        private readonly InstalledLocaleRepository $installedLocaleRepository,
        private readonly RouterInterface $router,
        private readonly HttpKernelInterface $httpKernel,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        if ($request->attributes->get('_route') === self::ROUTE_LOCALE_REDIRECT) {
            $locale = $request->getPreferredLanguage($this->installedLocaleRepository->findRedirectLocales());
            $routeParameters = [
                'path' => $request->attributes->get('path'),
            ];
            if ($request->getPreferredFormat() !== PagesRouteLoader::FORMAT_DEFAULT) {
                $routeParameters['_format'] = $request->getPreferredFormat();
            }
            try {
                $path = $this->router->generate(self::ROUTE_LOCALE_REDIRECT . '.' . $locale, $routeParameters);
            } catch (RouteNotFoundException) {
                $path = $this->router->generate(
                    self::ROUTE_LOCALE_REDIRECT . '.' . $request->attributes->get('default_locale'),
                    $routeParameters
                );
            }

            return new RedirectResponse($path, Response::HTTP_TEMPORARY_REDIRECT);
        }

        return $this->forwardTo404($request);
    }

    private function forwardTo404(Request $request): Response
    {
        $context = $this->router->match(
            $this->router->generate(Page::getRouteNameForIdAndLocale(Page::PAGE_ID_404, $request->getLocale()))
        );

        $request404 = $request->duplicate();
        $request404->attributes->set('_controller', $context['_controller']);
        $request404->attributes->set('_route', $context['_route']);
        $request404->attributes->set('navigationTitle', $context['navigationTitle']);
        $request404->attributes->set('revision', $context['revision']);


        return $this->httpKernel->handle($request404, HttpKernelInterface::SUB_REQUEST);
    }
}
