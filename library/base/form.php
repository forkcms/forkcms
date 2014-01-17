<?php

/**
 * This is our extended version of SpoonForm
 */
class CommonForm extends SpoonForm
{
    /**
     * Adds a single text field where the value will be trimmed automagically
     *
     * @param string $name       _                  The name
     * @param string $value      = null             The initial value
     * @param int    $maxLength  = null             The maximum-length the value can be
     * @param string $class      = 'inputText'      The CSS-class to be used
     * @param string $classError = 'inputTextError' The CSS-class to be used when there is an error
     * @param bool   $HTML       = false            Is HTML allowed?
     * @return BaseTrimmedTextField
     */
    public function addTrimmedText(
        $name,
        $value = null,
        $maxLength = null,
        $class = 'inputText',
        $classError = 'inputTextError',
        $HTML = false
    ) {
        $this->add(new BaseTrimmedTextField($name, $value, $maxLength, $class, $classError, $HTML));

        return $this->getField($name);
    }
}

/**
 * This is an extended version of SpoonFormText where the value will be trimmed automagically
 */
class BaseTrimmedTextField extends SpoonFormText
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
