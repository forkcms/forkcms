<?php

namespace ForkCMS\Modules\Backend\Controller;

use ForkCMS\Core\Domain\Header\Header;
use ForkCMS\Modules\Backend\Backend\Actions\NotFound;
use ForkCMS\Modules\Backend\Domain\Action\ActionControllerInterface;
use ForkCMS\Modules\Backend\Domain\Action\ActionSlug;
use ForkCMS\Modules\Backend\Domain\Navigation\Navigation;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleSettings;
use ForkCMS\Modules\Internationalisation\Domain\Locale\InstalledLocale;
use ForkCMS\Modules\Internationalisation\Domain\Locale\InstalledLocaleRepository;
use InvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

final class BackendController
{
    /** @param ServiceLocator<ActionControllerInterface> $actions */
    public function __construct(
        private readonly ServiceLocator $actions,
        private readonly Environment $twig,
        private readonly Navigation $navigation,
        private readonly Header $header,
        private readonly InstalledLocaleRepository $localeRepository,
        private readonly ModuleSettings $moduleSettings,
    ) {
    }

    public function __invoke(
        Request $request,
        ActionSlug $actionSlug
    ): Response {
        $locales = $this->localeRepository->findAllIndexed();
        $this->configureTwigForAction($request, $actionSlug, $locales);

        if (!$locales[$request->getLocale()]->isEnabledForWebsite()) {
            return $this->actions->get(NotFound::class)($request);
        }

        try {
            $action = $this->actions->get($actionSlug->getFQCN());
        } catch (NotFoundExceptionInterface) {
            throw new InvalidArgumentException(
                sprintf(
                    'The action class %s must be registered as a service and implement %s',
                    $actionSlug->getFQCN(),
                    ActionControllerInterface::class
                )
            );
        }

        try {
            return $action($request);
        } catch (NotFoundHttpException) {
            return $this->actions->get(NotFound::class)($request);
        }
    }

    /** @param array<string, InstalledLocale> $locales */
    private function configureTwigForAction(Request $request, ActionSlug $actionSlug, array $locales): void
    {
        $this->navigation->parse($this->twig);
        $this->navigation->buildBreadcrumbs($this->header->breadcrumbs);
        $this->header->addAssetsForAction($actionSlug->asModuleAction());
        $this->header->parse($this->twig);
        $this->twig->addGlobal(
            'SITE_TITLE',
            $this->moduleSettings->get(
                ModuleName::fromString('Frontend'),
                'site_title_' . $request->getLocale(),
                $_ENV['SITE_DEFAULT_TITLE']
            )
        );
        $this->twig->addGlobal('SITE_URL', $_ENV['SITE_PROTOCOL'] . '://' . $_ENV['SITE_DOMAIN']);
        $this->twig->addGlobal('SITE_MULTILINGUAL', $_ENV['SITE_MULTILINGUAL'] === 'true');
        $this->twig->addGlobal('bodyID', Container::underscore($actionSlug->getModuleName()));
        $this->twig->addGlobal('bodyClass', str_replace('/', '_', $actionSlug->getSlug()));
        $this->twig->addGlobal('LOCALES', $locales);
        $this->twig->addGlobal('MODULE_ACTION', $actionSlug->asModuleAction());
        $this->twig->addGlobal('CRLF', "\n");
        $this->twig->addGlobal('TAB', "\t");
    }
}
