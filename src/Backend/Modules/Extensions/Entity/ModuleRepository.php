<?php

namespace Backend\Modules\Extensions\Entity;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * This is the Module Repository
 *
 * @author Mathias Dewelde <mathias@dewelde.be>
 */
class ModuleRepository extends EntityRepository
{
    /**
     * Fetches all module names
     *
     * @return array
     */
    public function getAllModuleNames()
    {
        $qb = $this->createQueryBuilder('m')->select('partial m.{name}');
        return $qb->getQuery()->getResult(Query::HYDRATE_SCALAR);
    }
}
