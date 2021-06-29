<?php

namespace Backend\Modules\Blog\Domain\Comment;

use Common\Locale;
use Doctrine\DBAL\Connection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[] findAll()
 * @method Comment[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

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
        $entityManager = $this->getEntityManager();

        $builder = $entityManager
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

        // clear all the entities, as they won't get detached automatically on
        // removal
        $entityManager->clear(Comment::class);
    }

    public function deleteMultipleById(array $ids): void
    {
        $entityManager = $this->getEntityManager();

        $builder = $entityManager
            ->createQueryBuilder()
            ->delete(Comment::class, 'c')
            ->where('c.id IN(:ids)')
            ->setParameter(
                ':ids',
                $ids,
                Connection::PARAM_INT_ARRAY
            );
        $builder->getQuery()->execute();

        // clear all the entities, as they won't get detached automatically on
        // removal
        $entityManager->clear(Comment::class);
    }
}
