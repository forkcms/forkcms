<?php

namespace Backend\Modules\ContentBlocks\ContentBlock;

use Backend\Core\Language\LanguageName;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ContentBlockRepository extends EntityRepository
{
    /**
     * @param LanguageName $language
     *
     * @return Query
     */
    public function getDataGridQuery(LanguageName $language)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('cb.id, cb.title, cb.isHidden')
            ->from(ContentBlock::class, 'cb')
            ->where('cb.status = ?1 AND cb.language = ?2')
            ->setParameters([Status::active(), $language])
            ->getQuery();
    }

    /**
     * @param LanguageName $language
     *
     * @return int
     */
    public function getNextIdForLanguage(LanguageName $language)
    {
        return (int) $this->getEntityManager()
            ->createQueryBuilder()
            ->select('MAX(cb.id) + 1 as id')
            ->from(ContentBlock::class, 'cb')
            ->where('cb.language = :language')
            ->setParameter('language', $language)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
