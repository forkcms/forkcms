<?php

namespace Frontend\Core\Engine;

use Twig\Compiler;
use Twig\Node\Node;

/**
 * Twig note for writing out the compiled version of a form field error.
 */
class FormFieldErrorNode extends Node
{
    /**
     * Name of the template var holding the form this field error belongs to.
     *
     * @var string
     */
    private $form;

    /**
     * Name of the field of which we need to render the error.
     *
     * @var string
     */
    private $field;

    public function __construct(string $form, string $field, int $lineNumber, string $tag)
    {
        parent::__construct([], [], $lineNumber, $tag);
        $this->form = $form;
        $this->field = $field;
    }

    public function compile(Compiler $compiler): void
    {
        $writeErrorMessage = 'echo '
            . "\$context['form_{$this->form}']->getField('{$this->field}')->getErrors() "
            . "? '<span class=\"invalid-feedback\">' "
            . ". \$context['form_{$this->form}']->getField('{$this->field}')->getErrors() "
            . ". '</span>' : '';";
        $compiler
            ->addDebugInfo($this)
            ->write($writeErrorMessage)
        ;
    }
}
