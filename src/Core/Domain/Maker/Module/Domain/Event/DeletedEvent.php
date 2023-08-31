<?php

namespace ForkCMS\Core\Domain\Maker\Module\Domain\Event;

use ForkCMS\Core\Domain\Maker\Util\Entity as EntityUtil;
use ForkCMS\Core\Domain\Maker\Util\Template;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Contracts\EventDispatcher\Event;

final class DeletedEvent
{
    public static function generate(Generator $generator, EntityUtil $entity): string
    {
        $baseNamespace = $entity->getNamespace();

        return $generator->generateClass(
            $baseNamespace . '\\Event\\' . $entity->getName() . 'DeletedEvent',
            Template::getPath('Domain/Event/Deleted.tpl.php'),
            [
                'entity' => $entity->getName(),
                'useStatements' => [
                    'use ' . $entity->entityClassNameDetails->getFullName() . ';',
                    'use ' . $baseNamespace . '\\Command\\Delete' . $entity->getName() . ';',
                    'use ' . Event::class . ';',
                ],
                'deleteCommand' => 'Delete' . $entity->getName(),
            ]
        );
    }
}
