<?php

namespace ForkCMS\Core\Domain\Maker\Module\Installer;

use ForkCMS\Core\Domain\Maker\Util\Entity;
use ForkCMS\Core\Domain\Maker\Util\ModuleInfo;
use ForkCMS\Core\Domain\Maker\Util\Template;
use Symfony\Bundle\MakerBundle\Generator;

final class Installer
{
    /** @param Entity[] $entities */
    public static function generate(Generator $generator, ModuleInfo $moduleInfo, bool $isRequired, bool $isHiddenFromOverview, array $entities): void
    {
        $installerClass = $generator->createClassNameDetails(
            $moduleInfo->name->getName() . 'Installer',
            $moduleInfo->namespace . 'Installer'
        );
        $generator->generateClass(
            $installerClass->getFullName(),
            Template::getPath('Installer/ModuleInstaller.tpl.php'),
            [
                'isRequired' => $isRequired,
                'hideFromOverview' => $isHiddenFromOverview,
                'entities' => $entities,
            ]
        );
    }
}
