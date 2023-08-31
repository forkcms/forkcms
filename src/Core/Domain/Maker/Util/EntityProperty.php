<?php

namespace ForkCMS\Core\Domain\Maker\Util;

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
use ForkCMS\Core\Domain\Settings\SettingsBag;
use ReflectionClass;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Uid\Uuid;

final class EntityProperty
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

    public function __construct(
        public readonly string $name,
        public readonly bool $isId,
        public readonly bool $isGeneratedValue,
        public readonly string $dbalType,
        public readonly string $dbalTypeFull,
        public readonly ?string $dbalUseStatement,
        public readonly ?string $enumType,
        public readonly bool $isNullable,
        public readonly bool $isName,
        public readonly string $type,
        public readonly ?string $typeUseStatement,
    ) {
    }

    public static function fromInput(ConsoleStyle $io, ManagerRegistry $managerRegistry, bool $hasIdField = false, bool $askIsNameField = false): self
    {
        $name = $io->ask(
            'What name should the field have?',
            $hasIdField ? null : 'id',
            static fn(?string $fieldName): string => Validator::validateDoctrineFieldName(
                Validator::notBlank($fieldName),
                $managerRegistry
            )
        );
        $isId = $io->confirm('Is this the id field?', !$hasIdField);
        $dbalTypes = self::getDbalTypes();
        $dbalTypeName = $io->choice('What type', array_keys($dbalTypes), $isId ? 'integer' : 'string');
        $selectedDbalType = $dbalTypes[$dbalTypeName];

        $isNotEmbedded = $selectedDbalType !== Embedded::class;
        $class = self::FIELD_MAPPING[$dbalTypeName] ?? $io->ask(
            'What class should be used for the property?',
            '',
            static fn(?string $className): string => Validator::classExists(Validator::notBlank($className))
        );
        $isGeneratedValue = $isId && $dbalTypeName === 'integer' && $io->confirm('Is generated value?');
        $dbalType = $selectedDbalType;
        if (str_contains($dbalType, '::')) {
            $dbalType = Str::getShortClassName($dbalType);
        }
        $dbalTypeNamespace = null;
        if (str_contains($selectedDbalType, '::')) {
            $dbalTypeNamespace = 'use ' . substr($selectedDbalType, 0, strpos($selectedDbalType, '::')) . ';';
        }
        $enumType = null;
        if (in_array($dbalTypeName, ['enum_int', 'enum_string'], true)) {
            $enumType = Str::getShortClassName($class);
        }
        $isNullable = !$isId && $isNotEmbedded && $io->confirm('Is this field nullable?', false);
        $isName = false;
        if (!$isId && !$isNullable && $askIsNameField) {
            $isName = $io->confirm('Do you want to use this field to textually represent this entity?', false);
        }

        return new self(
            $name,
            $isId,
            $isGeneratedValue,
            $dbalType,
            $selectedDbalType,
            $dbalTypeNamespace,
            $enumType,
            $isNullable,
            $isName,
            ($isNullable ? '?' : '') . Str::getShortClassName($class),
            ctype_lower($class) ? null : 'use ' . $class . ';',
        );
    }

    /** @return array<string, string> */
    private static function getDbalTypes(): array
    {
        static $dbalTypes = [];
        if (count($dbalTypes) > 0) {
            return $dbalTypes;
        }
        $defaultTypes = array_map(
            static function (string $constant): string {
                return Types::class . '::' . $constant;
            },
            array_flip((new ReflectionClass(Types::class))->getConstants())
        );

        $registeredTypes = array_keys(StringType::getTypesMap());

        $dbalTypes = array_merge(
            $defaultTypes,
            array_map(
                static function (string $type): string {
                    return '\'' . $type . '\'';
                },
                array_combine($registeredTypes, $registeredTypes)
            ),
            $defaultTypes
        );
        $dbalTypes['enum_int'] = Types::class . '::INTEGER';
        $dbalTypes['enum_string'] = Types::class . '::STRING';
        $dbalTypes['one_to_many'] = OneToMany::class;
        $dbalTypes['many_to_many'] = ManyToMany::class;
        $dbalTypes['many_to_one'] = ManyToOne::class;
        $dbalTypes['embeded'] = Embedded::class;

        return $dbalTypes;
    }
}
