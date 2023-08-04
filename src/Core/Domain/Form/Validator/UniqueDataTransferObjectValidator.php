<?php

namespace ForkCMS\Core\Domain\Form\Validator;

use DateTimeInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\Embeddable;
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
use function is_string;

/**
 * Unique Entity Validator checks if one or a set of fields contain unique values.
 */
final class UniqueDataTransferObjectValidator extends ConstraintValidator
{
    public function __construct(private ManagerRegistry $registry)
    {
    }

    /**
     * @template T of UniqueDataTransferObjectInterface
     *
     * @param T|null $value
     *
     * @throws UnexpectedTypeException
     * @throws ConstraintDefinitionException
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueDataTransferObject) {
            throw new UnexpectedTypeException($constraint, UniqueDataTransferObject::class);
        }
        if (!is_array($constraint->fields) && !is_string($constraint->fields)) {
            throw new UnexpectedTypeException($constraint->fields, 'array');
        }
        if ($constraint->errorPath !== null && !is_string($constraint->errorPath)) {
            throw new UnexpectedTypeException($constraint->errorPath, 'string or null');
        }
        $fields = (array) $constraint->fields;
        if (count($fields) === 0) {
            throw new ConstraintDefinitionException('At least one field has to be specified.');
        }
        if ($value === null) {
            return;
        }
        $entityClass = $constraint->entityClass;
        if ($entityClass === null) {
            if (!$value->hasEntity()) {
                throw new ConstraintDefinitionException('No entityClass or entity was specified.');
            }
            $entityClass = get_class($value->getEntity());
        }
        $om = $this->getObjectManager($value, $constraint);
        $class = $om->getClassMetadata($entityClass);
        $criteria = [];
        $hasNullValue = false;
        foreach ($fields as $fieldName) {
            if (!$class->hasField($fieldName) && !$class->hasAssociation($fieldName)) {
                throw new ConstraintDefinitionException(sprintf('The field "%s" is not mapped by Doctrine, so it cannot be validated for uniqueness.', $fieldName));
            }
            $fieldValue = $value->$fieldName;
            if ($fieldValue === null) {
                $hasNullValue = true;
            }
            if ($constraint->ignoreNull && $fieldValue === null) {
                continue;
            }
            $criteria[$fieldName] = $fieldValue;
            if ($criteria[$fieldName] !== null && $class->hasAssociation($fieldName)) {
                /* Ensure the Proxy is initialized before using reflection to
                 * read its identifiers. This is necessary because the wrapped
                 * getter methods in the Proxy are being bypassed.
                 */
                $om->initializeObject($criteria[$fieldName]);
            }
        }
        // validation doesn't fail if one of the fields is null and if null values should be ignored
        if ($hasNullValue && $constraint->ignoreNull) {
            return;
        }
        // skip validation if there are no criteria (this can happen when the
        // "ignoreNull" option is enabled and fields to be checked are null
        if (empty($criteria)) {
            return;
        }
        $repository = $this->getRepository($value, $constraint, $om, $class);
        $result = $repository->{$constraint->repositoryMethod}($criteria);
        if ($result instanceof IteratorAggregate) {
            $result = $result->getIterator();
        }
        /* If the result is a MongoCursor, it must be advanced to the first
         * element. Rewinding should have no ill effect if $result is another
         * iterator implementation.
         */
        if ($result instanceof Iterator) {
            $result->rewind();
        } elseif (is_array($result)) {
            reset($result);
        }
        /* If no entity matched the query criteria or a single entity matched,
         * which is the same as the entity being validated, the criteria is
         * unique.
         */
        if (
            count($result) === 0
            || (
                count($result) === 1
                && $value->hasEntity()
                && $value->getEntity() === ($result instanceof Iterator ? $result->current() : current($result))
            )
        ) {
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

    private function formatWithIdentifiers(ObjectManager $em, ClassMetadata $class, mixed $value): string
    {
        if (!is_object($value) || $value instanceof DateTimeInterface) {
            return $this->formatValue($value, self::PRETTY_DATE);
        }
        $idClass = get_class($value);
        $identifiers = $this->getIdentifiers($em, $class, $value, $idClass);
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
     * @param class-string $idClass
     *
     * @return array<string,mixed>
     */
    private function getIdentifiers(ObjectManager $om, ClassMetadata $class, mixed $value, string $idClass): array
    {
        if ($class->getName() === $idClass) {
            return $class->getIdentifierValues($value);
        }
        // non unique value might be a composite PK that consists of other entity objects
        if ($om->getMetadataFactory()->hasMetadataFor($idClass)) {
            $metaData = $om->getClassMetadata($idClass);
            if (!empty($metaData->getIdentifierFieldNames())) {
                return $metaData->getIdentifierValues($value);
            }
        }
        // this case might happen if the non unique column has a custom doctrine type and its value is an object
        // in which case we cannot get any identifiers for it
        return [];
    }

    /**
     * @template T of object
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
            throw new ConstraintDefinitionException(sprintf('The "%s" entity repository does not support the "%s" entity. The entity should be an instance of or extend "%s".', $constraint->entityClass, $class->getName(), $supportedClass));
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
