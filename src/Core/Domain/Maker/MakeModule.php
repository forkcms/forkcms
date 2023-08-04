<?php

namespace ForkCMS\Core\Domain\Maker;

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

final class MakeModule extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:fork-module';
    }

    public static function getCommandDescription(): string
    {
        return 'Create a new Fork CMS module';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $this->initiateForModule($io, $generator);

        $this->createInstaller();
        $this->createDependencyInjectionConfiguration();

        $generator->writeChanges();
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }

    private function createDependencyInjectionConfiguration(): void
    {
        $dependencyInjectionClass = $this->generator->createClassNameDetails(
            $this->moduleName->getName() . 'Extension',
            $this->moduleNamespace . 'DependencyInjection'
        );
        $modulePath = dirname(
            $this->generator->generateClass(
                $dependencyInjectionClass->getFullName(),
                $this->getTemplatePath('DependencyInjection/ModuleExtension.tpl.php')
            ),
            2
        );
        $this->generator->generateFile(
            $modulePath . '/config/services.yaml',
            $this->getTemplatePath('config/services.tpl.yaml')
        );
        $this->generator->generateFile(
            $modulePath . '/config/doctrine.yaml',
            $this->getTemplatePath('config/doctrine.tpl.yaml')
        );
    }

    private function createInstaller(): void
    {
        $installerClass = $this->generator->createClassNameDetails(
            $this->moduleName->getName() . 'Installer',
            $this->moduleNamespace . 'Installer'
        );
        $this->generator->generateClass(
            $installerClass->getFullName(),
            $this->getTemplatePath('Installer/ModuleInstaller.tpl.php'),
            [
                'isRequired' => $this->io->confirm('Is this module required?', false),
                'hideFromOverview' => $this->io->confirm('Is this module hidden from the overview?', false),
            ]
        );
    }
}
