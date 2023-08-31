<?php

namespace ForkCMS\Core\Domain\Maker\Module\Domain\Event;

use ForkCMS\Core\Domain\Maker\Util\Entity as EntityUtil;
use ForkCMS\Core\Domain\Maker\Util\Template;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Contracts\EventDispatcher\Event;

final class CreatedEvent
{
    public static function generate(Generator $generator, EntityUtil $entity): string
    {
        $baseNamespace = $entity->getNamespace();

        return $generator->generateClass(
            $baseNamespace . '\\Event\\' . $entity->getName() . 'CreatedEvent',
            Template::getPath('Domain/Event/Created.tpl.php'),
            [
                'entity' => $entity->getName(),
                'useStatements' => [
                    'use ' . $entity->entityClassNameDetails->getFullName() . ';',
                    'use ' . $baseNamespace . '\\Command\\Create' . $entity->getName() . ';',
                    'use ' . Event::class . ';',
                ],
                'createCommand' => 'Create' . $entity->getName(),
            ]
        );
    }
}
