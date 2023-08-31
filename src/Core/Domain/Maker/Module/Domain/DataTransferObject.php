<?php

namespace ForkCMS\Core\Domain\Maker\Module\Domain;

use ForkCMS\Core\Domain\Maker\Util\Entity;
use ForkCMS\Core\Domain\Maker\Util\Template;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Validator\Constraints\NotBlank;

final class DataTransferObject
{
    public static function generate(Generator $generator, Entity $entity): string
    {
        $useStatements = [];
        if ($entity->hasRequiredFields()) {
            $useStatements[] = 'use ' . Str::getNamespace(NotBlank::class) . ' as Assert;';
        }

        return $generator->generateClass(
            $entity->entityClassNameDetails->getFullName() . 'DataTransferObject',
            Template::getPath('Domain/DataTransferObject.tpl.php'),
            [
                'entity' => $entity,
                'useStatements' => $entity->getPropertyUseStatements($useStatements),
            ]
        );
    }
}
