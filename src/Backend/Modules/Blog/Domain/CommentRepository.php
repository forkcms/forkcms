<?php

namespace Backend\Modules\Blog\Domain;

use Common\Locale;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;

class CommentRepository extends EntityRepository
{
    public function listCountPerStatus(Locale $locale): array
    {
        $builder = $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select('c.status, count(c.status) as number')
                        ->from(Comment::class, 'c')
                        ->where("c.locale = :locale")
                        ->setParameter('locale', $locale)
                        ->groupBy('c.status');

        $results = $builder->getQuery()->getResult();
        $data = [];

        foreach ($results as $row) {
            $data[$row['status']] = $row['number'];
        }

        return $data;
    }

    public function deleteMultipleById(array $ids): void
    {
        $builder = $this->getEntityManager()
                        ->createQueryBuilder()
                        ->delete(Comment::class, 'c')
                        ->where('c.id IN(:ids)')
                        ->setParameter(
                            ':ids',
                            $ids,
                            Connection::PARAM_INT_ARRAY
                        );
        $builder->getQuery()->execute();
    }
}
