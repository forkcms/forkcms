<?php

namespace Backend\Modules\Faq\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CategoryRepository
 *
 * @author Wouter Sioen <wouter@woutersioen.be>
 */
class CategoryRepository extends EntityRepository
{
    /**
     * Fetch a category based on it's id and language
     *
     * @param  string $url      The url for the category
     * @param  string $language The current language
     * @param  id     $ignoreId The id of the category we don't want to fetch
     *
     * @return Category|null    The category that matches these criteria
     */
    function findByUrl($url, $language, $ignoreId = null)
    {
        // Add limits on url and language to the querybuilder
        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->innerJoin('c.meta', 'm')
            ->where('m.url = :url')
            ->andWhere('c.language = :language')
            ->setParameters(array(
                'url' => $url,
                'language' => $language,
            ))
        ;

        // if we got an id to ignore, add it to the query
        if ($ignoreId !== null) {
            $qb->andWhere('c.id != :id')
                ->setParameter('id', $ignoreId)
            ;
        }

        return $qb->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
