<?php

namespace ForkCMS\Core\Domain\Maker;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\Persistence\ManagerRegistry;
use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Core\Domain\Settings\SettingsBag;
use ForkCMS\Modules\Backend\Domain\User\Blameable;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleInstallerLocator;
use ForkCMS\Modules\Frontend\Domain\Meta\EntityWithMetaTrait;
use ReflectionClass;
use RuntimeException;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\EventDispatcher\Event;

final class MakeModuleEntity extends AbstractMaker
{
    private const FIELD_MAPPING = [
        'string' => 'string',
        'ascii_string' => 'string',
        'text' => 'string',
        'blob' => 'string',
        'guid' => 'string',

        'integer' => 'int',
        'smallint' => 'int',
        'bigint' => 'int',

        'datetime' => DateTime::class,
        'datetimetz' => DateTime::class,
        'time' => DateTime::class,
        'date' => DateTime::class,

        'datetime_immutable' => DateTimeImmutable::class,
        'datetimetz_immutable' => DateTimeImmutable::class,
        'time_immutable' => DateTimeImmutable::class,
        'date_immutable' => DateTimeImmutable::class,

        'dateinterval' => DateInterval::class,

        'float' => 'float',
        'decimal' => 'float',

        'boolean' => 'bool',

        'array' => 'array',
        'json_array' => 'array',
        'simple_array' => 'array',

        'uuid' => Uuid::class,
        'ulid' => Ulid::class,

        'one_to_many' => Collection::class,
        'many_to_many' => Collection::class,
        'many_to_one' => Collection::class,

        'core__settings__settings_bag' => SettingsBag::class,
    ];

    /** @var array<string, string> */
    private array $dbalTypes;

    public function __construct(
        ModuleInstallerLocator $moduleInstallerLocator,
        private readonly ManagerRegistry $managerRegistry,
    ) {
        parent::__construct($moduleInstallerLocator);
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
        $this->initiateForModule($io, $generator, true);
        $this->loadDoctrineTypeMapping();
        $entity = ucfirst(
            $this->io->ask('What is the name of the entity class', null, Validator::validateClassName(...))
        );
        $hasIdField = false;
        $hasNameField = false;
        $fields = [];
        $io->progressStart(0);
        $io->newLine();
        do {
            $fieldName = $io->ask(
                'What name should the field have?',
                'id',
                function (?string $fieldName): string {
                    return Validator::validateDoctrineFieldName($fieldName, $this->managerRegistry);
                }
            );
            $field = $this->getField($io->confirm('Is this the id field?', !$hasIdField), !$hasNameField);
            $field['name'] = $fieldName;
            $hasIdField = $hasIdField || $field['is_id'];
            $hasNameField = $hasNameField || $field['is_name'];
            if ($io->confirm(sprintf('Are you happy with the config of the field: %s ?', $fieldName))) {
                $io->progressAdvance();
                $io->newLine();
                $fields[$field['name']] = $field;
            }
        } while (!$hasIdField || $io->confirm('Do you want to add another field?', false));
        $io->progressFinish();

        $entityClassNameDetails = $this->generator->createClassNameDetails(
            $entity,
            $this->moduleNamespace . 'Domain\\' . $entity
        );
        $isBlamable = $this->io->confirm('Do you want to add the Blameable trait?', false);
        $hasMeta = $this->io->confirm('Do you want to add the Meta trait?', false);
        $this->createDataTransferObject($entityClassNameDetails, $fields);
        $this->createEntity($entityClassNameDetails, $isBlamable, $hasMeta, $fields);
        $this->createRepository($entityClassNameDetails);
        $this->createChangedEvent($entityClassNameDetails);
        $this->createCreatedEvent($entityClassNameDetails);
        $this->createDeletedEvent($entityClassNameDetails);
        $this->createChangeCommand($entityClassNameDetails);
        $this->createCreateCommand($entityClassNameDetails);
        $this->createDeleteCommand($entityClassNameDetails, $fields);
        $this->createBackendAddAction($entityClassNameDetails);
        $this->createBackendEditAction($entityClassNameDetails, $fields);
        $this->createBackendDeleteAction($entityClassNameDetails);
        $this->createBackendIndexAction($entityClassNameDetails);

        $generator->writeChanges();
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }

    private function createBackendAddAction(ClassNameDetails $entityClassNameDetails): void
    {
        $addAction = $this->generator->createClassNameDetails(
            $entityClassNameDetails->getShortName() . 'Add',
            $this->moduleNamespace . 'Backend\\Actions'
        );
        $domainNamespace = $this->getNamespace($entityClassNameDetails);

        $this->generator->generateClass(
            $addAction->getFullName(),
            $this->getTemplatePath('Backend/Actions/Add.tpl.php'),
            [
                'entity' => $entityClassNameDetails->getShortName(),
                'useStatements' => [
                    'use ' . $domainNamespace . '\\' . $entityClassNameDetails->getShortName() . 'Type;',
                    'use ' . $domainNamespace . '\\Command\\Create' . $entityClassNameDetails->getShortName() . ';',
                    'use ' . $this->getNamespace($addAction) . '\\' . $entityClassNameDetails->getShortName() . 'Index;',
                ],
            ]
        );
    }

    /** @param array<string, mixed> $fields */
    private function createBackendEditAction(ClassNameDetails $entityClassNameDetails, array $fields): void
    {
        $nameFields = array_filter($fields, static fn (array $field): bool => $field['is_name']);

        $editAction = $this->generator->createClassNameDetails(
            $entityClassNameDetails->getShortName() . 'Edit',
            $this->moduleNamespace . 'Backend\\Actions'
        );
        $domainNamespace = $this->getNamespace($entityClassNameDetails);

        $this->generator->generateClass(
            $editAction->getFullName(),
            $this->getTemplatePath('Backend/Actions/Edit.tpl.php'),
            [
                'entity' => $entityClassNameDetails->getShortName(),
                'useStatements' => [
                    'use ' . $entityClassNameDetails->getFullName() . ';',
                    'use ' . $domainNamespace . '\\' . $entityClassNameDetails->getShortName() . 'Type;',
                    'use ' . $domainNamespace . '\\Command\\Change' . $entityClassNameDetails->getShortName() . ';',
                    'use ' . $this->getNamespace($editAction) . '\\' . $entityClassNameDetails->getShortName() . 'Index;',
                ],
                'nameField' => reset($nameFields)['name'] ?? false
            ]
        );
    }

    private function createBackendDeleteAction(ClassNameDetails $entityClassNameDetails): void
    {
        $deleteAction = $this->generator->createClassNameDetails(
            $entityClassNameDetails->getShortName() . 'Delete',
            $this->moduleNamespace . 'Backend\\Actions'
        );
        $domainNamespace = $this->getNamespace($entityClassNameDetails);

        $this->generator->generateClass(
            $deleteAction->getFullName(),
            $this->getTemplatePath('Backend/Actions/Delete.tpl.php'),
            [
                'entity' => $entityClassNameDetails->getShortName(),
                'useStatements' => [
                    'use ' . $entityClassNameDetails->getFullName() . ';',
                    'use ' . $domainNamespace . '\\Command\\Delete' . $entityClassNameDetails->getShortName() . ';',
                    'use ' . $this->getNamespace($deleteAction) . '\\' . $entityClassNameDetails->getShortName() . 'Index;',
                ],
            ]
        );
    }

    private function createBackendIndexAction(ClassNameDetails $entityClassNameDetails): void
    {
        $indexAction = $this->generator->createClassNameDetails(
            $entityClassNameDetails->getShortName() . 'Index',
            $this->moduleNamespace . 'Backend\\Actions'
        );

        $this->generator->generateClass(
            $indexAction->getFullName(),
            $this->getTemplatePath('Backend/Actions/Index.tpl.php'),
            [
                'entity' => $entityClassNameDetails->getShortName(),
                'useStatements' => [
                    'use ' . $entityClassNameDetails->getFullName() . ';',
                ],
            ]
        );
    }

    /** @param array<string, mixed> $fields */
    private function createEntity(ClassNameDetails $entityClass, bool $isBlameable, bool $isMeta, array $fields): void
    {
        $fieldUseStatements = ['use Doctrine\ORM\Mapping as ORM;' => 'use Doctrine\ORM\Mapping as ORM;'];
        array_walk($fields, static function (array $field) use (&$fieldUseStatements) {
            $fieldUseStatements[$field['dbal_use_statement']] = $field['dbal_use_statement'];
            $fieldUseStatements[$field['type_use_statement']] = $field['type_use_statement'];
        });
        $fieldUseStatements = array_filter($fieldUseStatements);
        if ($isBlameable) {
            $fieldUseStatements['use ' . Blameable::class . ';'] = 'use ' . Blameable::class . ';';
        }
        if ($isMeta) {
            $fieldUseStatements['use ' . EntityWithMetaTrait::class . ';'] = 'use ' . EntityWithMetaTrait::class . ';';
        }
        sort($fieldUseStatements);

        $this->generator->generateClass(
            $entityClass->getFullName(),
            $this->getTemplatePath('Domain/Entity.tpl.php'),
            [
                'fields' => $fields,
                'useStatements' => $fieldUseStatements,
                'blameable' => $isBlameable ? Str::getShortClassName(Blameable::class) : null,
                'meta' => $isMeta ? Str::getShortClassName(EntityWithMetaTrait::class) : null,
            ]
        );
    }

    /** @param array<string, mixed> $fields */
    private function createDataTransferObject(ClassNameDetails $entityClass, array $fields): void
    {
        $fieldUseStatements = ['use Symfony\Component\Validator\Constraints as Assert;' => 'use Symfony\Component\Validator\Constraints as Assert;'];
        array_walk($fields, static function (array $field) use (&$fieldUseStatements) {
            $fieldUseStatements[$field['type_use_statement']] = $field['type_use_statement'];
        });
        $fieldUseStatements = array_filter($fieldUseStatements);
        sort($fieldUseStatements);

        $this->generator->generateClass(
            $entityClass->getFullName() . 'DataTransferObject',
            $this->getTemplatePath('Domain/DataTransferObject.tpl.php'),
            [
                'entity' => $entityClass->getShortName(),
                'fields' => $fields,
                'useStatements' => $fieldUseStatements,
            ]
        );
    }

    private function createRepository(ClassNameDetails $entityClass): void
    {
        $this->generator->generateClass(
            $entityClass->getFullName() . 'Repository',
            $this->getTemplatePath('Domain/Repository.tpl.php'),
            [
                'entity' => $entityClass->getShortName(),
            ]
        );
    }

    private function createChangedEvent(ClassNameDetails $entityClassNameDetails): void
    {
        $baseNamespace = $this->getNamespace($entityClassNameDetails);

        $this->generator->generateClass(
            $baseNamespace . '\\Event\\' . $entityClassNameDetails->getShortName() . 'ChangedEvent',
            $this->getTemplatePath('Domain/Event/Changed.tpl.php'),
            [
                'entity' => $entityClassNameDetails->getShortName(),
                'useStatements' => [
                    'use ' . $entityClassNameDetails->getFullName() . ';',
                    'use ' . $baseNamespace . '\\Command\\Change' . $entityClassNameDetails->getShortName() . ';',
                    'use ' . Event::class . ';',
                ],
                'changeCommand' => 'Change' . $entityClassNameDetails->getShortName(),
            ]
        );
    }

    private function createCreatedEvent(ClassNameDetails $entityClassNameDetails): void
    {
        $baseNamespace = $this->getNamespace($entityClassNameDetails);

        $this->generator->generateClass(
            $baseNamespace . '\\Event\\' . $entityClassNameDetails->getShortName() . 'CreatedEvent',
            $this->getTemplatePath('Domain/Event/Created.tpl.php'),
            [
                'entity' => $entityClassNameDetails->getShortName(),
                'useStatements' => [
                    'use ' . $entityClassNameDetails->getFullName() . ';',
                    'use ' . $baseNamespace . '\\Command\\Create' . $entityClassNameDetails->getShortName() . ';',
                    'use ' . Event::class . ';',
                ],
                'createCommand' => 'Create' . $entityClassNameDetails->getShortName(),
            ]
        );
    }

    private function createDeletedEvent(ClassNameDetails $entityClassNameDetails): void
    {
        $baseNamespace = $this->getNamespace($entityClassNameDetails);

        $this->generator->generateClass(
            $baseNamespace . '\\Event\\' . $entityClassNameDetails->getShortName() . 'DeletedEvent',
            $this->getTemplatePath('Domain/Event/Deleted.tpl.php'),
            [
                'entity' => $entityClassNameDetails->getShortName(),
                'useStatements' => [
                    'use ' . $entityClassNameDetails->getFullName() . ';',
                    'use ' . $baseNamespace . '\\Command\\Delete' . $entityClassNameDetails->getShortName() . ';',
                    'use ' . Event::class . ';',
                ],
                'deleteCommand' => 'Delete' . $entityClassNameDetails->getShortName(),
            ]
        );
    }

    private function createChangeCommand(ClassNameDetails $entityClassNameDetails): void
    {
        $baseNamespace = $this->getNamespace($entityClassNameDetails);

        $this->generator->generateClass(
            $baseNamespace . '\\Command\\Change' . $entityClassNameDetails->getShortName(),
            $this->getTemplatePath('Domain/Command/Change.tpl.php'),
            [
                'entity' => $entityClassNameDetails->getShortName(),
                'useStatements' => [
                    'use ' . $entityClassNameDetails->getFullName() . ';',
                    'use ' . $baseNamespace . '\\' . $entityClassNameDetails->getShortName() . 'DataTransferObject;',
                ],
                'dataTransferObject' => $entityClassNameDetails->getShortName() . 'DataTransferObject',
            ]
        );
        $this->generator->generateClass(
            $baseNamespace . '\\Command\\Change' . $entityClassNameDetails->getShortName() . 'Handler',
            $this->getTemplatePath('Domain/Command/ChangeHandler.tpl.php'),
            [
                'entity' => $entityClassNameDetails->getShortName(),
                'useStatements' => [
                    'use ' . CommandHandlerInterface::class . ';',
                    'use ' . $entityClassNameDetails->getFullName() . ';',
                    'use ' . $baseNamespace . '\\' . $entityClassNameDetails->getShortName() . 'Repository;',
                ],
                'changeCommand' => 'Change' . $entityClassNameDetails->getShortName(),
                'repository' => $entityClassNameDetails->getShortName() . 'Repository',
            ]
        );
    }

    private function createCreateCommand(ClassNameDetails $entityClassNameDetails): void
    {
        $baseNamespace = $this->getNamespace($entityClassNameDetails);

        $this->generator->generateClass(
            $baseNamespace . '\\Command\\Create' . $entityClassNameDetails->getShortName(),
            $this->getTemplatePath('Domain/Command/Create.tpl.php'),
            [
                'entity' => $entityClassNameDetails->getShortName(),
                'useStatements' => [
                    'use ' . $entityClassNameDetails->getFullName() . ';',
                    'use ' . $baseNamespace . '\\' . $entityClassNameDetails->getShortName() . 'DataTransferObject;',
                ],
                'dataTransferObject' => $entityClassNameDetails->getShortName() . 'DataTransferObject',
            ]
        );
        $this->generator->generateClass(
            $baseNamespace . '\\Command\\Create' . $entityClassNameDetails->getShortName() . 'Handler',
            $this->getTemplatePath('Domain/Command/CreateHandler.tpl.php'),
            [
                'entity' => $entityClassNameDetails->getShortName(),
                'useStatements' => [
                    'use ' . CommandHandlerInterface::class . ';',
                    'use ' . $entityClassNameDetails->getFullName() . ';',
                    'use ' . $baseNamespace . '\\' . $entityClassNameDetails->getShortName() . 'Repository;',
                ],
                'createCommand' => 'Create' . $entityClassNameDetails->getShortName(),
                'repository' => $entityClassNameDetails->getShortName() . 'Repository',
            ]
        );
    }

    /** @param array<string, mixed> $fields */
    private function createDeleteCommand(ClassNameDetails $entityClassNameDetails, array $fields): void
    {
        foreach ($fields as $field) {
            if ($field['is_id']) {
                $idField = $field['name'];
                $idFieldType = $field['type'];
            }
        }
        $idField = $idField ?? throw new RuntimeException('Id field is not defined');
        $idFieldType = $idFieldType ?? throw new RuntimeException('Id field type is not defined');

        $baseNamespace = $this->getNamespace($entityClassNameDetails);

        $this->generator->generateClass(
            $baseNamespace . '\\Command\\Delete' . $entityClassNameDetails->getShortName(),
            $this->getTemplatePath('Domain/Command/Delete.tpl.php'),
            [
                'entity' => $entityClassNameDetails->getShortName(),
                'useStatements' => [
                    'use ' . $entityClassNameDetails->getFullName() . ';',
                ],
                'idField' => $idField,
                'idFieldType' => $idFieldType,
            ]
        );
        $this->generator->generateClass(
            $baseNamespace . '\\Command\\Delete' . $entityClassNameDetails->getShortName() . 'Handler',
            $this->getTemplatePath('Domain/Command/DeleteHandler.tpl.php'),
            [
                'entity' => $entityClassNameDetails->getShortName(),
                'useStatements' => [
                    'use ' . CommandHandlerInterface::class . ';',
                    'use ' . $baseNamespace . '\\' . $entityClassNameDetails->getShortName() . 'Repository;',
                ],
                'deleteCommand' => 'Delete' . $entityClassNameDetails->getShortName(),
                'repository' => $entityClassNameDetails->getShortName() . 'Repository',
                'idField' => $idField,
            ]
        );
    }

    private function loadDoctrineTypeMapping(): void
    {
        $defaultTypes = array_map(
            static function (string $constant): string {
                return Types::class . '::' . $constant;
            },
            array_flip((new ReflectionClass(Types::class))->getConstants())
        );

        $registeredTypes = array_keys(StringType::getTypesMap());

        $this->dbalTypes = array_merge(
            $defaultTypes,
            array_map(
                static function (string $type): string {
                    return '\'' . $type . '\'';
                },
                array_combine($registeredTypes, $registeredTypes)
            ),
            $defaultTypes
        );
        $this->dbalTypes['enum_int'] = Types::class . '::INTEGER';
        $this->dbalTypes['enum_string'] = Types::class . '::STRING';
        $this->dbalTypes['one_to_many'] = OneToMany::class;
        $this->dbalTypes['many_to_many'] = ManyToMany::class;
        $this->dbalTypes['many_to_one'] = ManyToOne::class;
        $this->dbalTypes['embeded'] = Embedded::class;
    }

    /** @return array<string, mixed> */
    private function getField(bool $isId = false, bool $askIsNameField = false): array
    {
        $dbalTypeName = $this->io->choice('What type', array_keys($this->dbalTypes), $isId ? 'integer' : 'string');

        $selectedDbalType = $this->dbalTypes[$dbalTypeName];
        $class = self::FIELD_MAPPING[$dbalTypeName]
            ?? $this->io->ask(
                'What class should be used for the property?',
                '',
                Validator::classExists(...)
            );
        $isGeneratedValue = $isId && $dbalTypeName === 'integer' && $this->io->confirm('Is generated value?', true);
        $dbalType = str_contains($selectedDbalType, '::')
            ? Str::getShortClassName($selectedDbalType) : $selectedDbalType;
        $dbalTypeNamespace = str_contains($selectedDbalType, '::')
            ? 'use ' . substr($selectedDbalType, 0, strpos($selectedDbalType, '::')) . ';' : null;
        $enumType = in_array($dbalTypeName, ['enum_int', 'enum_string'], true)
            ? Str::getShortClassName($class) : null;
        $isNullable = !$isId
            && $selectedDbalType !== Embedded::class
            && $this->io->confirm('Is this field nullable?', false);
        $isName = !$isId && $askIsNameField && $this->io->confirm('Do you want to use this field to textually represent this entity?', false);

        return [
            'is_id' => $isId,
            'is_generated_value' => $isGeneratedValue,
            'dbal_type' => $dbalType,
            'dbal_type_full' => $selectedDbalType,
            'dbal_use_statement' => $dbalTypeNamespace,
            'enum_type' => $enumType,
            'is_nullable' => $isNullable,
            'is_name' => $isName,
            'type' => ($isNullable ? '?' : '') . Str::getShortClassName($class),
            'type_use_statement' => ctype_lower($class) ? null : 'use ' . $class . ';',
        ];
    }

    private function getNamespace(ClassNameDetails $entityClassNameDetails): string
    {
        return substr(
            $entityClassNameDetails->getFullName(),
            0,
            strrpos($entityClassNameDetails->getFullName(), '\\', -1)
        );
    }
}
