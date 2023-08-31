<?php

namespace ForkCMS\Core\Domain\Maker\Module\DependencyInjection;

use ForkCMS\Core\Domain\Maker\Util\ModuleInfo;
use ForkCMS\Core\Domain\Maker\Util\Template;
use Symfony\Bundle\MakerBundle\Generator;

final class DependencyInjection
{
    public static function generate(Generator $generator, ModuleInfo $moduleInfo): void
    {
        $dependencyInjectionClass = $generator->createClassNameDetails(
            $moduleInfo->name->getName() . 'Extension',
            $moduleInfo->namespace . 'DependencyInjection'
        );
        $modulePath = dirname(
            $generator->generateClass(
                $dependencyInjectionClass->getFullName(),
                Template::getPath('DependencyInjection/ModuleExtension.tpl.php')
            ),
            2
        );
        $generator->generateFile(
            $modulePath . '/config/services.yaml',
            Template::getPath('config/services.tpl.yaml')
        );
        $generator->generateFile(
            $modulePath . '/config/doctrine.yaml',
            Template::getPath('config/doctrine.tpl.yaml')
        );
    }
}
