<?php

namespace Backend\Modules\Tags\Domain\ModuleTag;

use Doctrine\ORM\EntityRepository;

final class ModuleTagRepository extends EntityRepository
{
    public function add(ModuleTag $moduleTag): void
    {
        $this->getEntityManager()->persist($moduleTag);
        $this->getEntityManager()->flush();
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(ModuleTag $moduleTag): void
    {
        $this->getEntityManager()->remove($moduleTag);
        $this->getEntityManager()->flush();
    }
}
