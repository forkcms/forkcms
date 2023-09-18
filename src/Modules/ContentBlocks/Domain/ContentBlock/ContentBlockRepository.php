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

    /** @param ContentBlock[] $contentBlocks */
    public function removeMultiple(array $contentBlocks): void
    {
        $entityManager = $this->getEntityManager();
        /** @var ContentBlock $contentBlock */
        foreach ($contentBlocks as $contentBlock) {
            $entityManager->remove($contentBlock);
        }
        $entityManager->flush();
    }

    /** @return ContentBlock[] */
    public function getVersionsForRevisionId(int $revisionId): array
    {
        $contentBlock = $this->findOneby(['revisionId' => $revisionId]);

        if ($contentBlock === null) {
            return [];
        }

        return $this->findBy(['id' => $contentBlock->getId(), 'locale' => $contentBlock->getLocale()]);
    }

    public function removeByRevisionId(int $revisionId): void
    {
        $contentBlock = $this->findOneby(['revisionId' => $revisionId]);

        if ($contentBlock === null) {
            return;
        }

        // get all versions
        $versions = $this->findBy(['id' => $contentBlock->getId(), 'locale' => $contentBlock->getLocale()]);

        $entityManager = $this->getEntityManager();
        foreach ($versions as $version) {
            $entityManager->remove($version);
        }

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

    public function findForIdAndLocale(int $id, string $language): ?ContentBlock
    {
        return $this->createQueryBuilder('cb')
            ->andWhere('cb.id = :id')
            ->andWhere('cb.status = :active')
            ->andWhere('cb.locale = :locale')
            ->setParameter('id', $id)
            ->setParameter('active', Status::Active)
            ->setParameter('locale', Locale::from($language))
            ->getQuery()
            ->getOneOrNullResult();
    }
}