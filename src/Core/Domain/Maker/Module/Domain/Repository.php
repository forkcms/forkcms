<?php

namespace ForkCMS\Core\Domain\Maker\Module\Domain;

use ForkCMS\Core\Domain\Maker\Util\Entity as EntityUtil;
use ForkCMS\Core\Domain\Maker\Util\Template;
use Symfony\Bundle\MakerBundle\Generator;

final class Repository
{
    public static function generate(Generator $generator, EntityUtil $entity): string
    {
        return $generator->generateClass(
            $entity->entityClassNameDetails->getFullName() . 'Repository',
            Template::getPath('Domain/Repository.tpl.php'),
            [
                'entity' => $entity,
            ]
        );
    }
}
