<?php

namespace ForkCMS\Core\Domain\Maker\Module\Domain\Command;

use ForkCMS\Core\Domain\Maker\Util\Entity as EntityUtil;
use ForkCMS\Core\Domain\Maker\Util\Template;
use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use RuntimeException;
use Symfony\Bundle\MakerBundle\Generator;

final class DeleteCommand
{
    public static function generate(Generator $generator, EntityUtil $entity): string
    {
        $baseNamespace = $entity->getNamespace();
        foreach ($entity->properties as $property) {
            if ($property->isId) {
                $idField = $property->name;
                $idFieldType = $property->type;

                break;
            }
        }
        $idField = $idField ?? throw new RuntimeException('Id field is not defined');
        $idFieldType = $idFieldType ?? throw new RuntimeException('Id field type is not defined');

        $generator->generateClass(
            $baseNamespace . '\\Command\\Delete' . $entity->getName() . 'Handler',
            Template::getPath('Domain/Command/DeleteHandler.tpl.php'),
            [
                'entity' => $entity->getName(),
                'useStatements' => [
                    'use ' . CommandHandlerInterface::class . ';',
                    'use ' . $baseNamespace . '\\' . $entity->getName() . 'Repository;',
                ],
                'deleteCommand' => 'Delete' . $entity->getName(),
                'repository' => $entity->getName() . 'Repository',
                'idField' => $idField,
            ]
        );

        return $generator->generateClass(
            $baseNamespace . '\\Command\\Delete' . $entity->getName(),
            Template::getPath('Domain/Command/Delete.tpl.php'),
            [
                'entity' => $entity->getName(),
                'useStatements' => [
                    'use ' . $entity->entityClassNameDetails->getFullName() . ';',
                ],
                'idField' => $idField,
                'idFieldType' => $idFieldType,
            ]
        );
    }
}
