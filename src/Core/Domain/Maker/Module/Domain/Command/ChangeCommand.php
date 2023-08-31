<?php

namespace ForkCMS\Core\Domain\Maker\Module\Domain\Command;

use ForkCMS\Core\Domain\Maker\Util\Entity as EntityUtil;
use ForkCMS\Core\Domain\Maker\Util\Template;
use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use Symfony\Bundle\MakerBundle\Generator;

final class ChangeCommand
{
    public static function generate(Generator $generator, EntityUtil $entity): string
    {
        $baseNamespace = $entity->getNamespace();

        $generator->generateClass(
            $baseNamespace . '\\Command\\Change' . $entity->getName() . 'Handler',
            Template::getPath('Domain/Command/ChangeHandler.tpl.php'),
            [
                'entity' => $entity->getName(),
                'useStatements' => [
                    'use ' . CommandHandlerInterface::class . ';',
                    'use ' . $entity->entityClassNameDetails->getFullName() . ';',
                    'use ' . $baseNamespace . '\\' . $entity->getName() . 'Repository;',
                ],
                'changeCommand' => 'Change' . $entity->getName(),
                'repository' => $entity->getName() . 'Repository',
            ]
        );

        return $generator->generateClass(
            $baseNamespace . '\\Command\\Change' . $entity->getName(),
            Template::getPath('Domain/Command/Change.tpl.php'),
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
