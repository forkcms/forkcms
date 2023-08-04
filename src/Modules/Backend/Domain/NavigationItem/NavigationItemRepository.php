<?php

namespace ForkCMS\Modules\Backend\Domain\NavigationItem;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use ForkCMS\Modules\Backend\Domain\Action\ActionSlug;
use ForkCMS\Modules\Backend\Domain\Action\ModuleAction;
use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Throwable;

/**
 * @method NavigationItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method NavigationItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method NavigationItem[] findAll()
 * @method NavigationItem[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<NavigationItem>
 */
final class NavigationItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private AuthorizationCheckerInterface $authorizationChecker)
    {
        try {
            parent::__construct($registry, NavigationItem::class);
        } catch (Throwable $throwable) {
            if (!empty($_ENV['FORK_DATABASE_HOST']) && $_ENV['APP_ENV'] !== 'test') {
                throw $throwable; // needed during the installer
            }
        }
    }

    public function remove(NavigationItem $navigationItem): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($navigationItem);
        $entityManager->flush();
    }

    public function save(NavigationItem $navigationItem): void
    {
        $this->getEntityManager()->persist($navigationItem);
        $this->getEntityManager()->flush();
    }

    public function findUnique(
        TranslationKey $label,
        ?ActionSlug $slug,
        ?NavigationItem $parent
    ): ?NavigationItem {
        return $this->findOneBy(
            [
                'label.type' => $label->getType()->value,
                'label.name' => $label->getName(),
                'slug' => $slug,
                'parent' => $parent,
            ]
        );
    }

    public function findFirstWithSlugForUser(User $user): NavigationItem
    {
        $sortedNavigationItems = $this->findSortedNavigationItems();

        foreach ($sortedNavigationItems as $navigationItem) {
            $accessibleNavigationItem = $this->findAccessibleNavigationItemForUser($navigationItem, $user);
            if ($accessibleNavigationItem instanceof NavigationItem) {
                return $accessibleNavigationItem;
            }
        }

        throw new AccessDeniedException('No accessible navigation item found');
    }

    private function findAccessibleNavigationItemForUser(NavigationItem $navigationItem, User $user): ?NavigationItem
    {
        if (
            $navigationItem->getSlug() instanceof ActionSlug
            && $navigationItem->getModuleAction() instanceof ModuleAction
            && $this->authorizationChecker->isGranted($navigationItem->getModuleAction()->asRole())
        ) {
            return $navigationItem;
        }

        foreach ($navigationItem->getChildren() as $childNavigationItem) {
            $result = $this->findAccessibleNavigationItemForUser($childNavigationItem, $user);
            if ($result instanceof NavigationItem && $result->getSlug() instanceof ActionSlug) {
                return $result;
            }
        }

        return null;
    }

    /** @return NavigationItem[] */
    public function findSortedNavigationItems(): array
    {
        return $this->getQueryBuilderWithChildren()
            ->andWhere('n.parent IS NULL')
            ->orderBy('n.sequence, c1.sequence, c2.sequence')
            ->getQuery()
            ->getResult();
    }

    /** @return NavigationItem[] */
    public function findChildrenForParentId(?int $parentId): array
    {
        $queryBuilder = $this->getQueryBuilderWithChildren();
        if ($parentId === null) {
            $queryBuilder = $queryBuilder->andWhere('n.parent IS null');
        } else {
            $queryBuilder = $queryBuilder
                ->andWhere('n.parent = :parentId')
                ->setParameter('parentId', $parentId);
        }

        return $queryBuilder
            ->orderBy('n.sequence, c1.sequence, c2.sequence')
            ->getQuery()
            ->getResult();
    }

    private function getQueryBuilderWithChildren(): QueryBuilder
    {
        return $this->createQueryBuilder('n')
            ->leftJoin('n.children', 'c1')
            ->addSelect('c1')
            ->leftJoin('c1.children', 'c2')
            ->addSelect('c2');
    }
}
