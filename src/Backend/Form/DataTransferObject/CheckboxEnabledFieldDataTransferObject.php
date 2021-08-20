<?php

namespace Backend\Form\DataTransferObject;

class CheckboxEnabledFieldDataTransferObject
{
    public bool $enableField = false;

    public string $field = '';

    public function __construct(bool $enableField = false, string $field = '')
    {
        $this->enableField = $enableField;
        $this->field = $field;
    }
}
