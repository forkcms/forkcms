<?php

namespace Frontend\Core\Engine;

/**
 * Twig node for writing out the compiled version of form field.
 *
 * @author <per@wijs.be>
 */
class FormFieldNode extends \Twig_Node
{
    private $form;
    private $field;

    /**
     * @param string $form Name of the template var holding the form this field
     *                     belongs to.
     * @param string $field Name of the field to render.
     * @param int $lineno Line number in the template source file.
     * @param string $tag
     */
    public function __construct($form, $field, $lineno, $tag)
    {
        parent::__construct(array(), array(), $lineno, $tag);
        $this->form = $form;
        $this->field = $field;
    }

    /**
     * @param Twig_Compiler $compiler
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $frm = "\$context['form_{$this->form}']";
        $parseField = $frm . "->getField('{$this->field}')->parse()";

        $compiler
            ->addDebugInfo($this)
            ->write("echo $parseField;\n")
        ;
    }
}
