<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use ForkCMS\Modules\Frontend\Domain\Block\BlockRepository;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Pages\Domain\Revision\Revision;
use ForkCMS\Modules\Pages\Domain\Revision\RevisionRepository;
use ForkCMS\Modules\Pages\Domain\RevisionBlock\RevisionBlock;

/**
 * @method ContentBlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContentBlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContentBlock[]    findAll()
 * @method ContentBlock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<ContentBlock>
 */
final class ContentBlockRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $managerRegistry,
        private readonly BlockRepository $blockRepository,
        private readonly RevisionRepository $revisionRepository
    ) {
        parent::__construct($managerRegistry, ContentBlock::class);
    }

    public function save(ContentBlock $contentBlock): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($contentBlock);
        $entityManager->flush();

        if ($contentBlock->getStatus() !== Status::ARCHIVED) {
            $this->updateWidget($contentBlock);
        }
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
        $block = $this->blockRepository->findOneBy(['id' => $contentBlocks[0]->getExtraId()]);
        $this->revisionRepository->removeFrontendBlockFromRevision($block);
        $this->blockRepository->remove($block);

        $entityManager = $this->getEntityManager();
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
            ->andWhere('cb.isHidden = :false')
            ->setParameter('id', $id)
            ->setParameter('active', Status::ACTIVE)
            ->setParameter('locale', Locale::from($language))
            ->setParameter('false', false)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function updateWidget(ContentBlock $contentBlock): void
    {
        $block = $contentBlock->getWidget();
        $block->getSettings()->add([
            'label' => $contentBlock->getTitle(),
            'content_block_id' => $contentBlock->getId(),
        ]);
        if ($contentBlock->isHidden()) {
            $block->hide();
        } else {
            $block->show();
        }

        $this->blockRepository->save($block);
    }

    /**
     * @return array<ContentBlock>
     */
    public function getRevisionsForContentBlock(ContentBlock $contentBlock): array
    {
        return $this->createQueryBuilder('cb')
            ->andWhere('cb.id = :id')
            ->andWhere('cb.locale = :locale')
            ->andWhere('cb.status = :archived')
            ->addOrderBy('cb.updatedOn', 'ASC')
            ->setParameter('id', $contentBlock->getId())
            ->setParameter('locale', $contentBlock->getLocale())
            ->setParameter('archived', Status::ARCHIVED)
            ->getQuery()
            ->getResult();
    }

    public function isContentBlockInUse(ContentBlock $contentBlock): bool
    {
        $result = $this->getEntityManager()->createQueryBuilder()
            ->from(ContentBlock::class, 'cb')
            ->select('COUNT(cb.id)')
            ->innerJoin(Block::class, 'b', Join::WITH, 'b.id = cb.extraId')
            ->innerJoin(RevisionBlock::class, 'rb', Join::WITH, 'rb.block = b.id')
            ->innerJoin(Revision::class, 'r', Join::WITH, 'r.id = rb.revision')
            ->andWhere('cb.revisionId = :revisionId')
            ->andWhere('r.isArchived IS NULL')
            ->setParameter('revisionId', $contentBlock->getRevisionId())
            ->getQuery()
            ->getSingleScalarResult();

        return $result !== 0;
    }
}
