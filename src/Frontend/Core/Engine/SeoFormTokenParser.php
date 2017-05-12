<?php

namespace Frontend\Core\Engine;

use Twig_Node;
use Twig_Token;

/**
 * Twig token parser for form fields.
 */
class SeoFormTokenParser extends \Twig_TokenParser
{
    public function parse(Twig_Token $token): Twig_Node
    {
        $stream = $this->parser->getStream();
        if ($stream->getCurrent()->getType() !== Twig_Token::BLOCK_END_TYPE) {
            $error = sprintf("'%s' does not require any arguments.", $this->getTag());
            throw new \Twig_Error_Syntax($error, $token->getLine(), $this->parser->getFilename());
        }
        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        if (FormState::$current === null) {
            throw new \Twig_Error_Syntax(
                sprintf('Cannot render seo outside a form element'),
                $token->getLine(),
                $this->parser->getFilename()
            );
        }

        return new SeoFormNode(FormState::$current, $token->getLine(), $this->getTag());
    }

    public function getTag(): string
    {
        return 'seo';
    }
}
