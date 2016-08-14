<?php

namespace Backend\Modules\ContentBlocks\ContentBlock;

use Backend\Core\Language\Locale;
use Doctrine\ORM\EntityRepository;

class ContentBlockRepository extends EntityRepository
{
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

    /**
     * @param int $id
     * @param Locale $locale
     *
     * @return ContentBlock|null
     */
    public function findByIdAndLocale($id, Locale $locale)
    {
        return $this->findOneBy(['id' => $id, 'status' => Status::active(), 'language' => $locale]);
    }
}
