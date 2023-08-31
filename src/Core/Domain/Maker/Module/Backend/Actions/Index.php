<?php

namespace ForkCMS\Core\Domain\Maker\Module\Backend\Actions;

use ForkCMS\Core\Domain\Maker\Util\Entity;
use ForkCMS\Core\Domain\Maker\Util\ModuleInfo;
use ForkCMS\Core\Domain\Maker\Util\Template;
use Symfony\Bundle\MakerBundle\Generator;

final class Index
{
    public static function generate(Generator $generator, Entity $entity, ModuleInfo $moduleInfo): string
    {
        $indexAction = $generator->createClassNameDetails(
            $entity->entityClassNameDetails->getShortName() . 'Index',
            $moduleInfo->namespace . 'Backend\\Actions'
        );

        $actionPath = $generator->generateClass(
            $indexAction->getFullName(),
            Template::getPath('Backend/Actions/Index.tpl.php'),
            [
                'entity' => $entity->entityClassNameDetails->getShortName(),
                'useStatements' => [
                    'use ' . $entity->entityClassNameDetails->getFullName() . ';',
                ],
            ]
        );

        $generator->generateFile(
            dirname($actionPath, 3) . '/templates/Backend/Actions/Index.html.twig',
            Template::getPath('templates/Backend/Actions/Index.html.twig.tpl.php'),
            [
                'entity' => $entity->getName(),
            ]
        );

        return $actionPath;
    }
}
