<?php

namespace Backend\Form\DataTransferObject;

class CheckboxEnabledFieldDataTransferObject
{
    public bool $enableField = false;

    public string $field = '';

    public static function create(bool $enableField, string $field): self
    {
        $dto = new self();
        $dto->enableField = $enableField;
        $dto->field = $field;

        return $dto;
    }
}
