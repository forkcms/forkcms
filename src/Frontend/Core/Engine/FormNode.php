<?php

namespace Frontend\Core\Engine;

use Twig\Compiler;
use Twig\Node\Node;

/**
 * Twig node for writing out the compiled representation of an opeing form tag.
 */
class FormNode extends Node
{
    /**
     * @var string Template variable holding the form.
     */
    private $form;

    public function __construct(string $form, int $lineNumber, string $tag)
    {
        parent::__construct([], [], $lineNumber, $tag);
        $this->form = $form;
    }

    public function compile(Compiler $compiler): void
    {
        // Set some string representations to make the code writing via the
        // compiler a bit more readable. ("a bit")
        $form = "\$context['form_{$this->form}']";
        $formAction = $form . '->getAction()';
        $formMethod = $form . '->getMethod()';
        $formName = $form . '->getName()';
        $formToken = $form . '->getToken()';
        $formUseToken = $form . '->getUseToken()';
        $formParamsHtml = $form . '->getParametersHTML()';
        $formAttrAction = ' action="\', ' . $formAction . ', \'"';
        $formAttrMethod = ' method="\', ' . $formMethod . ', \'"';
        $hiddenFormName = '<input type="hidden" name="form" value="\', ' . $formName . ', \'" id="form\', ucfirst(' . $formName . '), \'" />';
        $hiddenFormToken = '<input type="hidden" name="form_token" value="\', ' . $formToken . ', \'" id="formToken\', ucfirst(' . $formName . '), \'" />';

        $compiler
            ->addDebugInfo($this)

            ->write('echo \'<form')
            ->raw($formAttrMethod)
            ->raw($formAttrAction)
            ->raw("', ")
            ->raw(' ' . $formParamsHtml)
            ->raw(', \'')
            ->raw('>\'')
            ->raw(";\n")

            ->write("echo '$hiddenFormName';\n")
            ->write("if($formUseToken) echo '$hiddenFormToken';")
        ;
    }
}
