<?php

namespace ForkCMS\Core\Domain\Twig;

use Doctrine\ORM\Query\Expr\Join;
use ForkCMS\Modules\Backend\Domain\Action\ActionName;
use ForkCMS\Modules\Backend\Domain\Action\ActionSlug;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Pages\Domain\Page\Page;
use ForkCMS\Modules\Pages\Domain\Page\PageRepository;
use ForkCMS\Modules\Pages\Domain\Page\PageRouter;
use Symfony\Bridge\Twig\Extension\RoutingExtension as TwigBridgeRoutingExtension;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class RoutingExtension extends AbstractExtension
{
    public function __construct(
        private UrlGeneratorInterface $generator,
        private TwigBridgeRoutingExtension $twigBridgeRoutingExcension,
        private RequestStack $requestStack,
        private readonly PageRepository $pageRepository,
        private readonly PageRouter $pageRouter
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
            new TwigFunction(
                'get_url_for_block',
                [$this, 'getUrlForBlock']
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

    public function getUrlForBlock(
        string $moduleName,
        string $actionName,
        ?string $type = 'action',
        array $parameters = [],
    ): string {
        $page = $this->pageRepository->createQueryBuilder('p')
            ->innerJoin('p.revisions', 'r', Join::WITH, 'r.isDraft = :draft AND r.locale = :locale')
            ->innerJoin('r.blocks', 'pb')
            ->innerJoin('pb.block', 'fb', Join::WITH, 'fb.block.module = :module AND fb.block.name = :name')
            ->setParameter('module', $moduleName)
            ->setParameter('name', $type . '__' . $actionName)
            ->setParameter('locale', Locale::current()->value)
            ->setParameter('draft', false)
            ->getQuery()
            ->getOneOrNullResult();

        if ($page === null) {
            return $this->pageRouter->getRouteForPageId(Page::PAGE_ID_404, Locale::current());
        }

        return $this->pageRouter->getRouteForPage($page, Locale::current(), $parameters);
    }
}
