<?php

namespace Backend\Modules\ContentBlocks\Repository;

use Backend\Modules\ContentBlocks\Entity\ContentBlock;
use Backend\Modules\ContentBlocks\ValueObject\ContentBlockStatus;
use Common\Locale;
use Doctrine\ORM\EntityRepository;

class ContentBlockRepository extends EntityRepository
{
    /**
     * @param ContentBlock $contentBlock
     *
     * We don't flush here, see http://disq.us/p/okjc6b
     */
    public function add(ContentBlock $contentBlock)
    {
        // make sure the other revisions are archived
        if ($contentBlock->getStatus()->isActive() && $contentBlock->getId() !== null) {
            array_map(
                function (ContentBlock $contentBlock) {
                    $contentBlock->archive();
                },
                (array) $this->findBy(['id' => $contentBlock->getId(), 'locale' => $contentBlock->getLocale()])
            );
        }

        $this->getEntityManager()->persist($contentBlock);
    }

    /**
     * @param Locale $locale
     *
     * @return int
     */
    public function getNextIdForLanguage(Locale $locale): int
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

    /**
     * @param int $id
     * @param Locale $locale
     *
     * @return ContentBlock|null
     */
    public function findOneByIdAndLocale($id, Locale $locale)
    {
        return $this->findOneBy(['id' => $id, 'status' => ContentBlockStatus::active(), 'locale' => $locale]);
    }

    /**
     * @param int $revisionId
     * @param Locale $locale
     *
     * @return ContentBlock|null
     */
    public function findOneByRevisionIdAndLocale($revisionId, Locale $locale)
    {
        return $this->findOneBy(
            ['revisionId' => $revisionId, 'locale' => $locale]
        );
    }

    /**
     * @param int $id
     * @param Locale $locale
     */
    public function removeByIdAndLocale($id, Locale $locale)
    {
        array_map(
            function (ContentBlock $contentBlock) {
                $this->getEntityManager()->remove($contentBlock);
            },
            (array) $this->findBy(['id' => $id, 'locale' => $locale])
        );
    }
}
