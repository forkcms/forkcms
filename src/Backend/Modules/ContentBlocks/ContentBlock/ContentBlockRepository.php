<?php

namespace Backend\Modules\ContentBlocks\ContentBlock;

use Backend\Core\Language\Locale;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ContentBlockRepository extends EntityRepository
{
    /**
     * @param Locale $locale
     *
     * @return Query
     */
    public function getDataGridQuery(Locale $locale)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('cb.id, cb.title, cb.isHidden')
            ->from(ContentBlock::class, 'cb')
            ->where('cb.status = ?1 AND cb.locale = ?2')
            ->setParameters([Status::active(), $locale])
            ->getQuery();
    }

    /**
     * @param Locale $locale
     *
     * @return int
     */
    public function getNextIdForLanguage(Locale $locale)
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
