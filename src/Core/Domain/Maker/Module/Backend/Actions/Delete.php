<?php

namespace ForkCMS\Core\Domain\Maker\Module\Backend\Actions;

use ForkCMS\Core\Domain\Maker\Util\Entity;
use ForkCMS\Core\Domain\Maker\Util\ModuleInfo;
use ForkCMS\Core\Domain\Maker\Util\Template;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Str;

final class Delete
{
    public static function generate(Generator $generator, Entity $entity, ModuleInfo $moduleInfo): string
    {
        $deleteAction = $generator->createClassNameDetails(
            $entity->getName() . 'Delete',
            $moduleInfo->namespace . 'Backend\\Actions'
        );
        $domainNamespace = $entity->getNamespace();

        return $generator->generateClass(
            $deleteAction->getFullName(),
            Template::getPath('Backend/Actions/Delete.tpl.php'),
            [
                'entity' => $entity->getName(),
                'useStatements' => [
                    'use ' . Str::getNamespace($deleteAction->getFullName()) . '\\' . $entity->getName() . 'Index;',
                    'use ' . $entity->entityClassNameDetails->getFullName() . ';',
                    'use ' . $domainNamespace . '\\Command\\Delete' . $entity->getName() . ';',
                ],
            ]
        );
    }
}
