<?php

namespace ForkCMS\Core\Domain\Maker;

use Doctrine\Persistence\ManagerRegistry;
use ForkCMS\Core\Domain\Maker\Util\Entity;
use ForkCMS\Core\Domain\Maker\Util\ModuleInfo;
use ForkCMS\Core\Domain\Maker\Module\DependencyInjection\DependencyInjection;
use ForkCMS\Core\Domain\Maker\Module\Installer\Installer;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleInstallerLocator;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

final class MakeModule extends AbstractMaker
{
    public function __construct(
        private readonly ModuleInstallerLocator $moduleInstallerLocator,
        private readonly ManagerRegistry $managerRegistry,
    ) {
    }

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
        $moduleInfo = ModuleInfo::fromInput($io, $this->moduleInstallerLocator);

        DependencyInjection::generate($generator, $moduleInfo);

        $entities = [];
        while ($io->confirm('Do you want to add an entity?', false)) {
            $entities[] = MakeModuleEntity::generateEntity($io, $generator, $this->managerRegistry, $moduleInfo);
        }
        usort($entities, static fn(Entity $a, Entity $b) => $a->getName() <=> $b->getName());

        Installer::generate(
            $generator,
            $moduleInfo,
            $io->confirm('Is this module required?', false),
            $io->confirm('Is this module hidden from the overview?', false),
            $entities
        );

        $generator->writeChanges();
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }
}
