<?php

namespace Frontend\Core\Engine;

/**
 * Twig node for writing out a compiled version of a closing form tag.
 */
class FormEndNode extends \Twig_Node
{
    /**
     * @param int $lineNumber Line number in the template source file.
     * @param string $tag
     */
    public function __construct(int $lineNumber, string $tag)
    {
        parent::__construct([], [], $lineNumber, $tag);
    }

    /**
     * @param \Twig_Compiler $compiler
     */
    public function compile(\Twig_Compiler $compiler): void
    {
        $compiler
            ->addDebugInfo($this)
            ->write('echo \'</form>\';');
    }
}
