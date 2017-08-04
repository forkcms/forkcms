<?php

namespace Frontend\Core\Engine;

/**
 * Twig note for writing out the compiled version of a form field error.
 */
class FormFieldErrorNode extends \Twig_Node
{
    /**
     * @var string
     */
    private $form;

    /**
     * @var string
     */
    private $field;

    /**
     * @param string $form Name of the template var holding the form this field
     *                     error belongs to.
     * @param string $field Name of the field of which we need to render the error.
     * @param int $lineNumber Line number in the template source file.
     * @param string $tag the name of the template tag.
     */
    public function __construct(string $form, string $field, int $lineNumber, string $tag)
    {
        parent::__construct([], [], $lineNumber, $tag);
        $this->form = $form;
        $this->field = $field;
    }

    /**
     * @param \Twig_Compiler $compiler
     */
    public function compile(\Twig_Compiler $compiler): void
    {
        $writeErrorMessage = 'echo '
            . "\$context['form_{$this->form}']->getField('{$this->field}')->getErrors() "
            . "? '<span class=\"formError\">' "
            . ". \$context['form_{$this->form}']->getField('{$this->field}')->getErrors() "
            . ". '</span>' : '';";
        $compiler
            ->addDebugInfo($this)
            ->write($writeErrorMessage)
        ;
    }
}
