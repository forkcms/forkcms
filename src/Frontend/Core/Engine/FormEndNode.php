<?php

namespace Frontend\Core\Engine;

/**
 * Twig node for writing out a compiled version of a closing form tag.
 */
class FormEndNode extends \Twig_Node
{
    /**
     * @param int $lineno Line number in the template source file.
     * @param string $tag
     */
    public function __construct(int $lineno, string $tag)
    {
        parent::__construct(array(), array(), $lineno, $tag);
    }

    /**
     * @param \Twig_Compiler $compiler
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write('echo \'</form>\';');
    }
}
