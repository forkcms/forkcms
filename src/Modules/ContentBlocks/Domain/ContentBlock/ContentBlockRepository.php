<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;

/**
 * @method ContentBlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContentBlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContentBlock[]    findAll()
 * @method ContentBlock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ContentBlockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, ContentBlock::class);
    }

    public function save(ContentBlock $contentBlock): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($contentBlock);
        $entityManager->flush();
    }

    public function remove(ContentBlock $contentBlock): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($contentBlock);
        $entityManager->flush();
    }

    public function getNextIdForLocale(Locale $locale): int
    {
        return (int) $this->getEntityManager()
                ->createQueryBuilder()
                ->select('MAX(cb.id) as id')
                ->from(ContentBlock::class, 'cb')
                ->where('cb.locale = :locale')
                ->setParameter('locale', $locale)
                ->getQuery()
                ->getSingleScalarResult() + 1;
    }
}
