<?php

namespace ForkCMS\Core\Domain\Maker\Module\Domain;

use Doctrine\ORM\Mapping\Entity as ORMEntity;
use ForkCMS\Core\Domain\Maker\Util\Entity as EntityUtil;
use ForkCMS\Core\Domain\Maker\Util\Template;
use ForkCMS\Modules\Backend\Domain\User\Blameable;
use ForkCMS\Modules\Frontend\Domain\Meta\EntityWithMetaTrait;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Validator\Constraints\NotBlank;

final class Entity
{
    public static function generate(Generator $generator, EntityUtil $entity): string
    {
        $useStatements = [
            'use ' . Str::getNamespace(ORMEntity::class) . '  as ORM;',
        ];
        if ($entity->hasRequiredFields()) {
            $useStatements[] = 'use ' . Str::getNamespace(NotBlank::class) . ' as Assert;';
        }
        if ($entity->isBlamable) {
            $useStatements[] = 'use ' . Blameable::class . ';';
        }
        if ($entity->hasMeta) {
            $useStatements[] = 'use ' . EntityWithMetaTrait::class . ';';
        }

        return $generator->generateClass(
            $entity->entityClassNameDetails->getFullName(),
            Template::getPath('Domain/Entity.tpl.php'),
            [
                'entity' => $entity,
                'useStatements' => $entity->getPropertyUseStatements($useStatements, true),
            ]
        );
    }
}
