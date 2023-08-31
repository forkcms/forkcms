<?php

namespace ForkCMS\Core\Domain\Maker\Module\Domain\Command;

use ForkCMS\Core\Domain\Maker\Util\Entity as EntityUtil;
use ForkCMS\Core\Domain\Maker\Util\Template;
use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use Symfony\Bundle\MakerBundle\Generator;

final class CreateCommand
{
    public static function generate(Generator $generator, EntityUtil $entity): string
    {
        $baseNamespace = $entity->getNamespace();

        $generator->generateClass(
            $baseNamespace . '\\Command\\Create' . $entity->getName() . 'Handler',
            Template::getPath('Domain/Command/CreateHandler.tpl.php'),
            [
                'entity' => $entity->getName(),
                'useStatements' => [
                    'use ' . CommandHandlerInterface::class . ';',
                    'use ' . $entity->entityClassNameDetails->getFullName() . ';',
                    'use ' . $baseNamespace . '\\' . $entity->getName() . 'Repository;',
                ],
                'createCommand' => 'Create' . $entity->getName(),
                'repository' => $entity->getName() . 'Repository',
            ]
        );

        return $generator->generateClass(
            $baseNamespace . '\\Command\\Create' . $entity->getName(),
            Template::getPath('Domain/Command/Create.tpl.php'),
            [
                'entity' => $entity->getName(),
                'useStatements' => [
                    'use ' . $entity->entityClassNameDetails->getFullName() . ';',
                    'use ' . $baseNamespace . '\\' . $entity->getName() . 'DataTransferObject;',
                ],
                'dataTransferObject' => $entity->getName() . 'DataTransferObject',
            ]
        );
    }
}
