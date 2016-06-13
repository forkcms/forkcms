<?php

namespace Frontend\Core\Engine;

/**
 * Twig note for writing out the compiled version of a form field error.
 *
 * @author <per@wijs.be>
 */
class FormFieldErrorNode extends \Twig_Node
{
    private $form;
    private $field;

    /**
     * @param string $form Name of the template var holding the form this field
     *                     error belongs to.
     * @param string $field Name of the field of which we need to render the error.
     * @param int $lineno Line number in the template source file.
     * @param string $tag the name of the template tag.
     */
    public function __construct($form, $field, $lineno, $tag)
    {
        parent::__construct(array(), array(), $lineno, $tag);
        $this->form = $form;
        $this->field = $field;
    }

    public function compile(\Twig_Compiler $compiler)
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
