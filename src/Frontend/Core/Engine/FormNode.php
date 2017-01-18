<?php

namespace Frontend\Core\Engine;

/**
 * Twig node for writing out the compiled representation of an opeing form tag.
 */
class FormNode extends \Twig_Node
{
    /**
     * @var string Template variable holding the form.
     */
    private $form;

    /**
     * @param string $form The name of the template variable to which the form is assigned
     * @param int $lineno
     * @param string $tag
     */
    public function __construct($form, $lineno, $tag)
    {
        parent::__construct(array(), array(), $lineno, $tag);
        $this->form = $form;
    }

    /**
     * @param \Twig_Compiler $compiler
     */
    public function compile(\Twig_Compiler $compiler)
    {
        // Set some string representations to make the code writing via the
        // compiler a bit more readable. ("a bit")
        $frm = "\$context['form_{$this->form}']";
        $frmAction = $frm . '->getAction()';
        $frmMethod = $frm . '->getMethod()';
        $frmName = $frm . '->getName()';
        $frmToken = $frm . '->getToken()';
        $frmUseToken = $frm . '->getUseToken()';
        $frmParamsHtml = $frm . '->getParametersHTML()';
        $frmAttrAction = ' action="\', ' . $frmAction . ', \'"';
        $frmAttrMethod = ' method="\', ' . $frmMethod . ', \'"';
        $hiddenFormName = '<input type="hidden" name="form" value="\', ' . $frmName . ', \'" id="form\', ucfirst(' . $frmName . '), \'" />';
        $hiddenFormToken = '<input type="hidden" name="form_token" value="\', ' . $frmToken . ', \'" id="formToken\', ucfirst(' . $frmName . '), \'" />';

        $compiler
            ->addDebugInfo($this)

            ->write('echo \'<form')
            ->raw($frmAttrMethod)
            ->raw($frmAttrAction)
            //->raw($htmlAcceptCharset)
            ->raw("', ")
            ->raw(' ' . $frmParamsHtml)
            ->raw(', \'')
            ->raw('>\'')
            ->raw(";\n")

            ->write("echo '$hiddenFormName';\n")
            ->write("if($frmUseToken) echo '$hiddenFormToken';")
        ;
    }
}
