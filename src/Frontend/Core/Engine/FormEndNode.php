<?php

namespace Frontend\Core\Engine;

use Twig\Compiler;
use Twig\Node\Node;

/**
 * Twig node for writing out a compiled version of a closing form tag.
 */
class FormEndNode extends Node
{
    /**
     * @param int $lineNumber Line number in the template source file.
     * @param string $tag
     */
    public function __construct(int $lineNumber, string $tag)
    {
        parent::__construct([], [], $lineNumber, $tag);
    }

    public function compile(Compiler $compiler): void
    {
        $compiler
            ->addDebugInfo($this)
            ->write('echo \'</form>\';');
    }
}
