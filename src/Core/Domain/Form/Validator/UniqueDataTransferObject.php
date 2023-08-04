<?php

namespace ForkCMS\Core\Domain\Form\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Constraint for the Unique Entity validator.
 *
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class UniqueDataTransferObject extends Constraint
{
    public const NOT_UNIQUE_ERROR = '23bd9dbf-6b9b-41cd-a99e-4844bcf3077f';

    public string $message = 'err.NotUnique';

    public string $service = UniqueDataTransferObjectValidator::class;

    /** @var class-string|null */
    public string|null $entityClass = null;

    public string $repositoryMethod = 'findBy';

    /** @var string[]|string */
    public array|string $fields = [];

    public string|null $errorPath = null;

    public bool $ignoreNull = true;

    protected static $errorNames = [
        self::NOT_UNIQUE_ERROR => 'NOT_UNIQUE_ERROR',
    ];

    public function getRequiredOptions(): array
    {
        return ['fields'];
    }

    public function validatedBy(): string
    {
        return $this->service;
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function getDefaultOption(): string
    {
        return 'fields';
    }
}
