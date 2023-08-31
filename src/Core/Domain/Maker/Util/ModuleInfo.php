<?php

namespace ForkCMS\Core\Domain\Maker\Util;

use ForkCMS\Modules\Extensions\Domain\Module\ModuleInstallerLocator;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use InvalidArgumentException;
use Symfony\Bundle\MakerBundle\ConsoleStyle;

final class ModuleInfo
{
    public function __construct(public readonly ModuleName $name, public readonly string $namespace)
    {
    }

    public static function fromInput(
        ConsoleStyle $io,
        ModuleInstallerLocator $moduleInstallerLocator,
        bool $allowExisting = false
    ): self {
        $Name = $io->ask(
            'Module name',
            null,
            static function (?string $value) use ($allowExisting, $moduleInstallerLocator): ModuleName {
                if (empty($value)) {
                    throw new InvalidArgumentException('Module name is required');
                }

                $moduleName = ModuleName::fromString($value);
                if (
                    !$allowExisting
                    && in_array($moduleName, $moduleInstallerLocator->getAllModuleNames(), true)
                ) {
                    throw new InvalidArgumentException('Module name already exists');
                }

                return $moduleName;
            }
        );

        return new self($Name, 'Modules\\' . $Name->getName() . '\\');
    }
}
