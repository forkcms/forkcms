<?php

namespace ForkCMS\Core\Domain\Maker\Module\Backend\Actions;

use ForkCMS\Core\Domain\Maker\Util\Entity;
use ForkCMS\Core\Domain\Maker\Util\ModuleInfo;
use ForkCMS\Core\Domain\Maker\Util\Template;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Str;

final class Add
{
    public static function generate(Generator $generator, Entity $entity, ModuleInfo $moduleInfo): string
    {
        $addAction = $generator->createClassNameDetails(
            $entity->getName() . 'Add',
            $moduleInfo->namespace . 'Backend\\Actions'
        );
        $domainNamespace = $entity->getNamespace();

        $actionPath = $generator->generateClass(
            $addAction->getFullName(),
            Template::getPath('Backend/Actions/Add.tpl.php'),
            [
                'entity' => $entity->getName(),
                'useStatements' => [
                    'use ' . Str::getNamespace($addAction->getFullName()) . '\\' . $entity->getName() . 'Index;',
                    'use ' . $domainNamespace . '\\' . $entity->getName() . 'Type;',
                    'use ' . $domainNamespace . '\\Command\\Create' . $entity->getName() . ';',
                ],
            ]
        );

        $generator->generateFile(
            dirname($actionPath, 3) . '/templates/Backend/Actions/Add.html.twig',
            Template::getPath('templates/Backend/Actions/Add.html.twig.tpl.php'),
            [
                'entity' => $entity->getName(),
            ]
        );

        return $actionPath;
    }
}
