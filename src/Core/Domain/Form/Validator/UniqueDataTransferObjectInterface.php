<?php

namespace ForkCMS\Core\Domain\Form\Validator;

/**
 * @template T
 */
interface UniqueDataTransferObjectInterface
{
    public function hasEntity(): bool;

    /** @return T */
    public function getEntity(): mixed;
}
