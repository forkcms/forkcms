<?php

namespace ForkCMS\Core\Domain\Maker\Module\Backend\Actions;

use ForkCMS\Core\Domain\Maker\Util\Entity;
use ForkCMS\Core\Domain\Maker\Util\EntityProperty;
use ForkCMS\Core\Domain\Maker\Util\ModuleInfo;
use ForkCMS\Core\Domain\Maker\Util\Template;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Str;

final class Edit
{
    public static function generate(Generator $generator, Entity $entity, ModuleInfo $moduleInfo): string
    {
        $nameFields = array_filter($entity->properties, static fn (EntityProperty $property): bool => $property->isName);

        $editAction = $generator->createClassNameDetails(
            $entity->getName() . 'Edit',
            $moduleInfo->namespace . 'Backend\\Actions'
        );
        $domainNamespace = $entity->getNamespace();

        $actionPath =  $generator->generateClass(
            $editAction->getFullName(),
            Template::getPath('Backend/Actions/Edit.tpl.php'),
            [
                'entity' => $entity->getName(),
                'useStatements' => [
                    'use ' . $entity->entityClassNameDetails->getFullName() . ';',
                    'use ' . $domainNamespace . '\\' . $entity->getName() . 'Type;',
                    'use ' . $domainNamespace . '\\Command\\Change' . $entity->getName() . ';',
                    'use ' . Str::getNamespace($editAction->getFullName()) . '\\' . $entity->getName() . 'Index;',
                ],
                // @phpstan-ignore-next-line
                'nameField' => reset($nameFields)?->name ?? false,
            ]
        );

        $generator->generateFile(
            dirname($actionPath, 3) . '/templates/Backend/Actions/Edit.html.twig',
            Template::getPath('templates/Backend/Actions/Edit.html.twig.tpl.php'),
            [
                'entity' => $entity->getName(),
            ]
        );

        return $actionPath;
    }
}
