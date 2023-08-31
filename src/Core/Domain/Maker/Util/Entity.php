<?php

namespace ForkCMS\Core\Domain\Maker\Util;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Bundle\MakerBundle\Validator;

final class Entity
{
    /** @param EntityProperty[] $properties */
    public function __construct(
        public readonly ClassNameDetails $entityClassNameDetails,
        public readonly bool $isBlamable,
        public readonly bool $hasMeta,
        public readonly array $properties,
    ) {
    }

    public function getName(): string
    {
        return $this->entityClassNameDetails->getShortName();
    }

    public function getNamespace(): string
    {
        return Str::getNamespace($this->entityClassNameDetails->getFullName());
    }

    /**
     * @param string[] $useStatements
     *
     * @return array<string, string>
     */
    public function getPropertyUseStatements(array $useStatements = [], bool $includeDbalUseStatement = false): array
    {
        $useStatements = array_combine($useStatements, $useStatements);
        array_map(
            static function (EntityProperty $property) use (&$useStatements, $includeDbalUseStatement) {
                $useStatements[$property->typeUseStatement] = $property->typeUseStatement;
                if ($includeDbalUseStatement) {
                    $useStatements[$property->dbalUseStatement] = $property->dbalUseStatement;
                }
            },
            $this->properties
        );
        $useStatements = array_filter($useStatements);
        sort($useStatements);

        return $useStatements;
    }

    public function hasRequiredFields(): bool
    {
        return (bool) count(
            array_filter(
                $this->properties,
                static fn(EntityProperty $property) => !$property->isNullable
            )
        );
    }

    public static function fromInput(
        ConsoleStyle $io,
        Generator $generator,
        ManagerRegistry $managerRegistry,
        ModuleInfo $moduleInfo
    ): self {
        $entity = ucfirst(
            $io->ask(
                'What is the name of the entity class',
                null,
                static fn(?string $className): string => Validator::validateClassName(Validator::notBlank($className))
            )
        );

        return new self(
            $generator->createClassNameDetails(
                $entity,
                $moduleInfo->namespace . 'Domain\\' . $entity
            ),
            $io->confirm('Do you want to add the Blameable trait?', false),
            $io->confirm('Do you want to add the Meta trait?', false),
            self::fieldsFromInput($io, $managerRegistry)
        );
    }

    /** @return EntityProperty[] */
    private static function fieldsFromInput(ConsoleStyle $io, ManagerRegistry $managerRegistry): array
    {
        $fields = [];
        $hasIdField = false;
        $hasNameField = false;
        $io->progressStart();
        $io->newLine();
        do {
            $field = EntityProperty::fromInput(
                $io,
                $managerRegistry,
                $hasIdField,
                !$hasNameField
            );
            $hasIdField = $hasIdField || $field->isId;
            $hasNameField = $hasNameField || $field->isName;
            if ($io->confirm(sprintf('Are you happy with the config of the field: %s ?', $field->name))) {
                $io->progressAdvance();
                $io->newLine();
                $fields[$field->name] = $field;
            }
        } while (!$hasIdField || $io->confirm('Do you want to add another field?', false));
        $io->progressFinish();

        return $fields;
    }
}
