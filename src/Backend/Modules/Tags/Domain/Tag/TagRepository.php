<?php

namespace Backend\Modules\Tags\Domain\Tag;

use Doctrine\ORM\EntityRepository;

final class TagRepository extends EntityRepository
{
    public function add(Tag $tag): void
    {
        $this->getEntityManager()->persist($tag);
        $this->getEntityManager()->flush();
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(Tag ...$tag): void
    {
        $entityManager = $this->getEntityManager();

        array_map(
            function (Tag $tag) use ($entityManager) {
                $entityManager->remove($tag);
            },
            $tag
        );

        $this->getEntityManager()->flush();
    }

    public function findByIds(int ...$id): array
    {
        $queryBuilder = $this->createQueryBuilder('t');

        return $queryBuilder
            ->where($queryBuilder->expr()->in('t.id', $id))
            ->getQuery()
            ->getResult();
    }
}
