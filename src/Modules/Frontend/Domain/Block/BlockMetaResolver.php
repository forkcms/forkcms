<?php

namespace ForkCMS\Modules\Frontend\Domain\Block;

use Doctrine\ORM\EntityManagerInterface;
use ForkCMS\Modules\Frontend\Domain\Meta\Meta;

interface BlockMetaResolver
{
    /** @return Meta[] */
    public static function getPossibleMeta(EntityManagerInterface $entityManager): array;
}
