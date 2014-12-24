<?php

namespace Backend\Modules\Faq\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * QuestionRepository
 *
 * @author Wouter Sioen <wouter@woutersioen.be>
 */
class QuestionRepository extends EntityRepository
{
    /**
     * Fetch a question based on it's id and language
     *
     * @param  string $url      The url for the question
     * @param  string $language The current language
     * @param  id     $ignoreId The id of the question we don't want to fetch
     *
     * @return Question|null    The question that matches these criteria
     */
    function findByUrl($url, $language, $ignoreId = null)
    {
        // Add limits on url and language to the querybuilder
        $qb = $this->createQueryBuilder('q')
            ->select('q')
            ->innerJoin('q.meta', 'm')
            ->where('m.url = :url')
            ->andWhere('q.language = :language')
            ->setParameters(array(
                'url' => $url,
                'language' => $language,
            ))
        ;

        // if we got an id to ignore, add it to the query
        if ($ignoreId !== null) {
            $qb->andWhere('q.id != :id')
                ->setParameter('id', $ignoreId)
            ;
        }

        return $qb->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
