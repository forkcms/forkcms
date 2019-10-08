<?php

namespace Backend\Modules\Pages\Domain\ModuleExtra;

use Backend\Modules\ContentBlocks\Domain\ContentBlock\Exception\ContentBlockNotFound;
use Common\Locale;
use Doctrine\ORM\EntityRepository;

class ModuleExtraRepository extends EntityRepository
{
    public function add(ModuleExtra $moduleExtra): void
    {
        // We don't flush here, see http://disq.us/p/okjc6b
        $this->getEntityManager()->persist($moduleExtra);
    }
}
