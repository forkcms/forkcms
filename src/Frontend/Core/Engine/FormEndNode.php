<?php

namespace Frontend\Core\Engine;

/**
 * Twig node for writing out a compiled version of a closing form tag.
 *
 * @author <per@wijs.be>
 */
class FormEndNode extends \Twig_Node
{
    /**
     * @param int $lineno Line number in the template source file.
     * @param string $tag
     */
    public function __construct($lineno, $tag)
    {
        parent::__construct(array(), array(), $lineno, $tag);
    }

    /**
     * @param Twig_Compiler $compiler
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write('echo \'</form>\';');
    }
}
