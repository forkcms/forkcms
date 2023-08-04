<?php

namespace ForkCMS\Core\Domain\Twig;

use ForkCMS\Modules\Backend\Domain\Action\ActionName;
use ForkCMS\Modules\Backend\Domain\Action\ActionSlug;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use Symfony\Bridge\Twig\Extension\RoutingExtension as TwigBridgeRoutingExtension;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class RoutingExtension extends AbstractExtension
{
    public function __construct(
        private UrlGeneratorInterface $generator,
        private TwigBridgeRoutingExtension $twigBridgeRoutingExcension,
        private RequestStack $requestStack
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'action_url',
                [$this, 'getUrl'],
                ['is_safe_callback' => [$this->twigBridgeRoutingExcension, 'isUrlGenerationSafe']]
            ),
            new TwigFunction(
                'action_path',
                [$this, 'getPath'],
                ['is_safe_callback' => [$this->twigBridgeRoutingExcension, 'isUrlGenerationSafe']]
            ),
        ];
    }

    /** @param array<string,mixed> $parameters */
    public function getPath(
        string $actionName = null,
        string $moduleName = null,
        array $parameters = [],
        bool $relative = false,
        ?string $locale = null
    ): string {
        return $this->getActionSlug($moduleName, $actionName)->generateRoute(
            $this->generator,
            $parameters,
            $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH,
            $locale === null ? null : Locale::tryFrom($locale)
        );
    }

    /** @param array<string,mixed> $parameters */
    public function getUrl(
        string $actionName = null,
        string $moduleName = null,
        array $parameters = [],
        bool $schemeRelative = false,
        ?string $locale = null
    ): string {
        return $this->getActionSlug($moduleName, $actionName)->generateRoute(
            $this->generator,
            $parameters,
            $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL,
            $locale === null ? null : Locale::tryFrom($locale)
        );
    }

    private function getActionSlug(?string $moduleName, ?string $actionName): ActionSlug
    {
        $defaultSlug = ActionSlug::fromRequest($this->requestStack->getMainRequest());
        if ($moduleName === null && $actionName === null) {
            return $defaultSlug;
        }

        if ($moduleName === null) {
            $moduleName = $defaultSlug->getModuleName()->getName();
        }
        if ($actionName === null) {
            $actionName = $defaultSlug->getActionName()->getName();
        }

        return new ActionSlug(
            ModuleName::fromString($moduleName),
            ActionName::fromString($actionName)
        );
    }
}
