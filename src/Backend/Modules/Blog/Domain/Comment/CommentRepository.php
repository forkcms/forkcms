<?php

namespace Backend\Modules\Blog\Domain\Comment;

use Common\Locale;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;

class CommentRepository extends EntityRepository
{
    public function listCountPerStatus(Locale $locale): array
    {
        $builder = $this->createQueryBuilder('c')
                        ->select('c.status, count(c.status) as number')
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

    public function updateMultipleStatusById(array $ids, string $status): void
    {
        $builder = $this->getEntityManager()
                        ->createQueryBuilder()
                        ->update(Comment::class, 'c')
                        ->set('c.status', ':newStatus')
                        ->where('c.id IN(:ids)')
                        ->setParameter(
                            ':newStatus',
                            $status
                        )
                        ->setParameter(
                            ':ids',
                            $ids,
                            Connection::PARAM_INT_ARRAY
                        );
        $builder->getQuery()->execute();
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
