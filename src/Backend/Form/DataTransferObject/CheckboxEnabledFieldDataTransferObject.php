<?php

namespace Backend\Form\DataTransferObject;

class CheckboxEnabledFieldDataTransferObject
{
    public bool $enabled = false;

    public string $value = '';

    public function __construct(bool $enabled = false, string $value = '')
    {
        $this->enabled = $enabled;
        $this->value = $value;
    }
}
