<?php

namespace Common\Form;

/**
 * This is an extended version of SpoonFormText where the value will be trimmed automagically
 */
class TrimmedTextField extends \SpoonFormText
{
    /** @inheritdoc */
    public function __construct(
        $name,
        $value = null,
        $maxLength = null,
        $class = 'inputText',
        $classError = 'inputTextError',
        $HTML = false
    ) {
        $value = trim($value);
        parent::__construct($name, $value, $maxLength, $class, $classError, $HTML);
    }

    /** @inheritdoc */
    public function getValue($allowHTML = null)
    {
        $value = parent::getValue($allowHTML);

        return trim($value);
    }
}
