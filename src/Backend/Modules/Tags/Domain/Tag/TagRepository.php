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

    public function remove(Tag $tag): void
    {
        $this->getEntityManager()->remove($tag);
        $this->getEntityManager()->flush();
    }
}
