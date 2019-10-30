<?php

namespace ForkCMS\Bundle\CoreBundle\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;

/**
 * Constraint for the Unique Entity validator.
 *
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
final class UniqueDataTransferObject extends Constraint
{
    const NOT_UNIQUE_ERROR = '23bd9dbf-6b9b-41cd-a99e-4844bcf3077f';

    /** @var string */
    public $message = 'err.NotUnique';

    /** @var string */
    public $service = 'unique_data_transfer_object';

    /** @var EntityManagerInterface|null */
    public $em = null;

    /** @var mixed|null */
    public $entityClass = null;

    /** @var string */
    public $repositoryMethod = 'findBy';

    /** @var array */
    public $fields = [];

    /** @var string|null */
    public $errorPath = null;

    /** @var bool */
    public $ignoreNull = true;

    /** @var array */
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

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function getDefaultOption(): string
    {
        return 'fields';
    }
}
