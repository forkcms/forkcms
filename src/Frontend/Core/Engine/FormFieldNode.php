<?php

namespace Frontend\Core\Engine;

use Twig\Compiler;
use Twig\Node\Node;

/**
 * Twig node for writing out the compiled version of form field.
 */
class FormFieldNode extends Node
{
    /**
     * Name of the template var holding the form this field belongs to.
     *
     * @var string
     */
    private $form;

    /**
     * Name of the field to render.
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
        $form = "\$context['form_{$this->form}']";
        $parseField = $form . "->getField('{$this->field}')->parse()";

        $compiler
            ->addDebugInfo($this)
            ->write("echo $parseField;\n")
        ;
    }
}
