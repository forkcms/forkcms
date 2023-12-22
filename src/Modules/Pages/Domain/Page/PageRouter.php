<?php

namespace ForkCMS\Modules\Pages\Domain\Page;

use Doctrine\ORM\Query\Expr\Join;
use ForkCMS\Modules\Frontend\Domain\Block\BlockRouterInterface;
use ForkCMS\Modules\Frontend\Domain\Block\ModuleBlock;
use ForkCMS\Modules\Frontend\Domain\Block\Type;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use InvalidArgumentException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class PageRouter implements BlockRouterInterface
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly PageRepository $pageRepository,
    ) {
    }

    /** @param array<string, mixed> $parameters */
    public function getRouteForBlock(
        ModuleBlock $moduleBlock,
        Locale $locale = null,
        array $parameters = [],
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        if ($moduleBlock->getName()->getType() !== Type::ACTION) {
            throw new InvalidArgumentException('Only actions can be routed');
        }
        $locale = $locale ?? Locale::current();

        $page = $this->pageRepository->createQueryBuilder('p')
            ->innerJoin('p.revisions', 'r', Join::WITH, 'r.isDraft = :draft AND r.locale = :locale')
            ->innerJoin('r.blocks', 'pb')
            ->innerJoin('pb.block', 'fb', Join::WITH, 'fb.block.module = :module AND fb.block.name = :name')
            ->setParameter('module', $moduleBlock->getModule())
            ->setParameter('name', $moduleBlock->getName())
            ->setParameter('locale', $locale->value)
            ->setParameter('draft', false)
            ->getQuery()
            ->getSingleResult();

        return $this->getRouteForPage($page, $locale, $parameters, $referenceType);
    }

    /** @param array<string, mixed> $parameters */
    public function getRouteForPage(
        Page $page,
        Locale $locale = null,
        array $parameters = [],
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        return $this->getRouteForPageId(
            $page->hasId() ? $page->getId() : Page::PAGE_ID_HOME,
            $locale,
            $parameters,
            $referenceType
        );
    }

    /** @param array<string, mixed> $parameters */
    public function getRouteForPageId(
        int $pageId,
        Locale $locale = null,
        array $parameters = [],
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        return $this->router->generate(
            Page::getRouteNameForIdAndLocale($pageId, $locale ?? Locale::current()),
            $parameters,
            $referenceType
        );
    }
}
