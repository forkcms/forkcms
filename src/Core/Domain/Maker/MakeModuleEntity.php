<?php

namespace ForkCMS\Core\Domain\Maker;

use Doctrine\Persistence\ManagerRegistry;
use ForkCMS\Core\Domain\Maker\Module\Backend\Actions\Add;
use ForkCMS\Core\Domain\Maker\Module\Backend\Actions\Delete;
use ForkCMS\Core\Domain\Maker\Module\Backend\Actions\Edit;
use ForkCMS\Core\Domain\Maker\Module\Backend\Actions\Index;
use ForkCMS\Core\Domain\Maker\Module\Domain\Command\ChangeCommand;
use ForkCMS\Core\Domain\Maker\Module\Domain\Command\CreateCommand;
use ForkCMS\Core\Domain\Maker\Module\Domain\Command\DeleteCommand;
use ForkCMS\Core\Domain\Maker\Module\Domain\Entity;
use ForkCMS\Core\Domain\Maker\Module\Domain\Event\ChangedEvent;
use ForkCMS\Core\Domain\Maker\Module\Domain\Event\CreatedEvent;
use ForkCMS\Core\Domain\Maker\Module\Domain\Event\DeletedEvent;
use ForkCMS\Core\Domain\Maker\Module\Domain\Repository;
use ForkCMS\Core\Domain\Maker\Util\Entity as EntityUtil;
use ForkCMS\Core\Domain\Maker\Util\ModuleInfo;
use ForkCMS\Core\Domain\Maker\Util\Template;
use ForkCMS\Core\Domain\Maker\Module\Domain\DataTransferObject;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleInstallerLocator;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

final class MakeModuleEntity extends AbstractMaker
{
    public function __construct(
        private readonly ModuleInstallerLocator $moduleInstallerLocator,
        private readonly ManagerRegistry $managerRegistry,
    ) {
    }

    public static function getCommandName(): string
    {
        return 'make:fork-module-entity';
    }

    public static function getCommandDescription(): string
    {
        return 'Create a new Fork CMS module entity';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $moduleInfo = ModuleInfo::fromInput($io, $this->moduleInstallerLocator, true);

        self::generateEntity($io, $generator, $this->managerRegistry, $moduleInfo);

        $generator->writeChanges();
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }

    public static function generateEntity(
        ConsoleStyle $io,
        Generator $generator,
        ManagerRegistry $managerRegistry,
        ModuleInfo $moduleInfo
    ): EntityUtil {
        $entity = EntityUtil::fromInput($io, $generator, $managerRegistry, $moduleInfo);

        DataTransferObject::generate($generator, $entity);
        Entity::generate($generator, $entity);
        Repository::generate($generator, $entity);

        CreateCommand::generate($generator, $entity);
        ChangeCommand::generate($generator, $entity);
        DeleteCommand::generate($generator, $entity);

        CreatedEvent::generate($generator, $entity);
        ChangedEvent::generate($generator, $entity);
        DeletedEvent::generate($generator, $entity);

        Add::generate($generator, $entity, $moduleInfo);
        Edit::generate($generator, $entity, $moduleInfo);
        Delete::generate($generator, $entity, $moduleInfo);
        Index::generate($generator, $entity, $moduleInfo);

        return $entity;
    }
}
