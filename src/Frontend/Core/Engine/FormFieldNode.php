<?php

namespace Frontend\Core\Engine;

/**
 * Twig node for writing out the compiled version of form field.
 */
class FormFieldNode extends \Twig_Node
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
     *                     belongs to.
     * @param string $field Name of the field to render.
     * @param int $lineNumber Line number in the template source file.
     * @param string $tag
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
        $form = "\$context['form_{$this->form}']";
        $parseField = $form . "->getField('{$this->field}')->parse()";

        $compiler
            ->addDebugInfo($this)
            ->write("echo $parseField;\n")
        ;
    }
}
