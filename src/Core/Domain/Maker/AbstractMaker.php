<?php

namespace ForkCMS\Core\Domain\Maker;

use ForkCMS\Modules\Extensions\Domain\Module\ModuleInstallerLocator;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use InvalidArgumentException;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker as SymfonyAbstractMaker;

abstract class AbstractMaker extends SymfonyAbstractMaker
{
    protected ConsoleStyle $io;
    protected Generator $generator;
    protected ModuleName $moduleName;
    protected string $moduleNamespace;

    public function __construct(protected readonly ModuleInstallerLocator $moduleInstallerLocator)
    {
    }

    protected function getTemplatePath(string $template): string
    {
        return __DIR__ . '/../../templates/Maker/' . $template;
    }

    final protected function initiateForModule(
        ConsoleStyle $io,
        Generator $generator,
        bool $allowExisting = false
    ): void {
        $this->io = $io;
        $this->generator = $generator;

        $this->moduleName = $this->io->ask(
            'Module name',
            null,
            function (?string $value) use ($allowExisting): ModuleName {
                if (empty($value)) {
                    throw new InvalidArgumentException('Module name is required');
                }

                $moduleName = ModuleName::fromString($value);
                if (
                    !$allowExisting
                    && in_array($moduleName, $this->moduleInstallerLocator->getAllModuleNames(), true)
                ) {
                    throw new InvalidArgumentException('Module name already exists');
                }

                return $moduleName;
            }
        );
        $this->moduleNamespace = 'Modules\\' . $this->moduleName->getName() . '\\';
    }
}
