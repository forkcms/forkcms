<?php

namespace ForkCMS\Core\Domain\Form\Validator;

use DateTimeInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Iterator;
use IteratorAggregate;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function count;
use function get_class;
use function is_array;
use function is_object;

/**
 * Unique Entity Validator checks if one or a set of fields contain unique values.
 */
final class UniqueDataTransferObjectValidator extends ConstraintValidator
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
    }

    /**
     * @template T of UniqueDataTransferObjectInterface
     *
     * @param UniqueDataTransferObjectInterface<T>|null $value
     *
     * @throws UnexpectedTypeException
     * @throws ConstraintDefinitionException
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!is_object($value)) {
            return;
        }

        if (!$constraint instanceof UniqueDataTransferObject) {
            throw new UnexpectedTypeException($constraint, UniqueDataTransferObject::class);
        }

        $fields = $this->getFields($constraint);

        $entityClass = $this->getEntityClass($constraint, $value);
        $om = $this->getObjectManager($value, $constraint);
        $class = $om->getClassMetadata($entityClass);

        [$criteria, $hasNullValue] = $this->validateFields(
            $fields,
            $class,
            $value,
            $constraint,
            $om
        );

        if (($hasNullValue && $constraint->ignoreNull) || count($criteria) === 0) {
            return;
        }

        $result = $this->findDuplicate($value, $constraint, $om, $class, $criteria);
        if ($result === null) {
            return;
        }

        $errorPath = $constraint->errorPath ?? $fields[0];
        $invalidValue = $criteria[$errorPath] ?? $criteria[$fields[0]];

        $this->context->buildViolation($constraint->message)
            ->atPath($errorPath)
            ->setParameter('{{ value }}', $this->formatWithIdentifiers($om, $class, $invalidValue))
            ->setInvalidValue($invalidValue)
            ->setCode(UniqueDataTransferObject::NOT_UNIQUE_ERROR)
            ->setCause($result)
            ->addViolation();
    }

    /**
     * @template T of UniqueDataTransferObjectInterface
     *
     * @param UniqueDataTransferObjectInterface<T> $dataTransferObject
     * @param ClassMetadata<T> $class
     *
     * @return T[]|null
     */
    private function findDuplicate(
        UniqueDataTransferObjectInterface $dataTransferObject,
        UniqueDataTransferObject $constraint,
        ObjectManager $om,
        ClassMetadata $class,
        mixed $criteria
    ): ?array {
        $repository = $this->getRepository($dataTransferObject, $constraint, $om, $class);
        $result = $repository->{$constraint->repositoryMethod}($criteria);

        if ($result instanceof IteratorAggregate) {
            $result = $result->getIterator();
        }

        if ($result instanceof Iterator) {
            $result->rewind();
        } elseif (is_array($result)) {
            reset($result);
        }

        $currentResult = $result instanceof Iterator ? $result->current() : current($result);

        if (
            count($result) === 0
            || (
                count($result) === 1
                && $dataTransferObject->hasEntity()
                && $dataTransferObject->getEntity() === $currentResult
            )
        ) {
            return null;
        }

        return $result;
    }

    /**
     * @template T of UniqueDataTransferObjectInterface
     *
     * @param string[] $fields
     * @param UniqueDataTransferObjectInterface<T> $dataTransferObject
     * @param ClassMetadata<T> $class
     *
     * @return array{array<string,mixed>, bool}
     */
    private function validateFields(
        array $fields,
        ClassMetadata $class,
        UniqueDataTransferObjectInterface $dataTransferObject,
        UniqueDataTransferObject $constraint,
        ObjectManager $om
    ): array {
        $criteria = [];
        $hasNullValue = false;

        foreach ($fields as $fieldName) {
            if (!$class->hasField($fieldName) && !$class->hasAssociation($fieldName)) {
                throw new ConstraintDefinitionException(
                    sprintf(
                        'The field "%s" is not mapped by Doctrine, so it cannot be validated for uniqueness.',
                        $fieldName
                    )
                );
            }

            $fieldValue = $dataTransferObject->$fieldName;

            if ($fieldValue === null) {
                $hasNullValue = true;

                if ($constraint->ignoreNull) {
                    continue;
                }
            }

            $criteria[$fieldName] = $fieldValue;

            if ($criteria[$fieldName] !== null && $class->hasAssociation($fieldName)) {
                $om->initializeObject($criteria[$fieldName]);
            }
        }

        return [$criteria, $hasNullValue];
    }

    private function formatWithIdentifiers(ObjectManager $em, ClassMetadata $class, mixed $invalidValue): string
    {
        if (!is_object($invalidValue) || $invalidValue instanceof DateTimeInterface) {
            return $this->formatValue($invalidValue, self::PRETTY_DATE);
        }
        $idClass = get_class($invalidValue);
        $identifiers = $this->getIdentifiers($em, $class, $invalidValue, $idClass);
        if (!$identifiers) {
            return sprintf('object("%s")', $idClass);
        }
        array_walk(
            $identifiers,
            function (&$id, $field) {
                if (!is_object($id) || $id instanceof DateTimeInterface) {
                    $idAsString = $this->formatValue($id, self::PRETTY_DATE);
                } else {
                    $idAsString = sprintf('object("%s")', get_class($id));
                }
                $id = sprintf('%s => %s', $field, $idAsString);
            }
        );

        return sprintf('object("%s") identified by (%s)', $idClass, implode(', ', $identifiers));
    }

    /**
     * @return class-string
     */
    private function getEntityClass(UniqueDataTransferObject $constraint, object $value): string
    {
        if ($constraint->entityClass === null && !$value->hasEntity()) {
            throw new ConstraintDefinitionException('No entityClass or entity was specified.');
        }

        return $constraint->entityClass ?? get_class($value->getEntity());
    }

    /** @return string[] */
    private function getFields(UniqueDataTransferObject $constraint): array
    {
        $fields = (array) $constraint->fields;
        if (count($fields) === 0) {
            throw new ConstraintDefinitionException('At least one field has to be specified.');
        }

        return $fields;
    }

    /**
     * @param class-string $idClass
     *
     * @return array<string,mixed>
     */
    private function getIdentifiers(ObjectManager $om, ClassMetadata $class, object $value, string $idClass): array
    {
        if ($class->getName() === $idClass) {
            return $class->getIdentifierValues($value);
        }
        // non-unique value might be a composite PK that consists of other entity objects
        if ($om->getMetadataFactory()->hasMetadataFor($idClass)) {
            $metaData = $om->getClassMetadata($idClass);
            if (!empty($metaData->getIdentifierFieldNames())) {
                return $metaData->getIdentifierValues($value);
            }
        }
        // this case might happen if the non-unique column has a custom doctrine type and its value is an object
        // in which case we cannot get any identifiers for it
        return [];
    }

    /**
     * @template T of UniqueDataTransferObjectInterface
     *
     * @param UniqueDataTransferObjectInterface<T> $dataTransferObject
     * @param ClassMetadata<T> $class
     *
     * @return EntityRepository<T>
     */
    private function getRepository(
        UniqueDataTransferObjectInterface $dataTransferObject,
        UniqueDataTransferObject $constraint,
        ObjectManager $om,
        ClassMetadata $class
    ): ObjectRepository {
        if ($constraint->entityClass === null) {
            return $om->getRepository(get_class($dataTransferObject->getEntity()));
        }
        /* Retrieve repository from given entity name.
         * We ensure the retrieved repository can handle the entity
         * by checking the entity is the same, or subclass of the supported entity.
         */
        $repository = $om->getRepository($constraint->entityClass);
        $supportedClass = $repository->getClassName();
        if (
            $dataTransferObject->hasEntity()
            && !$dataTransferObject->getEntity() instanceof $supportedClass
        ) {
            throw new ConstraintDefinitionException(
                sprintf(
                    'The "%s" entity repository does not support the "%s" entity. ' .
                    'The entity should be an instance of or extend "%s".',
                    $constraint->entityClass,
                    $class->getName(),
                    $supportedClass
                )
            );
        }

        // @phpstan-ignore-next-line
        return $repository;
    }

    /**
     * @template T of UniqueDataTransferObjectInterface
     *
     * @param T $dataTransferObject
     */
    private function getObjectManager(
        UniqueDataTransferObjectInterface $dataTransferObject,
        UniqueDataTransferObject $constraint
    ): ObjectManager {
        $om = $this->registry->getManagerForClass(
            $constraint->entityClass ?? get_class($dataTransferObject->getEntity())
        );

        if (!$om) {
            return $this->registry->getManager();
        }

        return $om;
    }
}
