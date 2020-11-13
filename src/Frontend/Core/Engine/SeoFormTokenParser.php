<?php

namespace Frontend\Core\Engine;

use Twig\Error\SyntaxError;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * Twig token parser for form fields.
 */
class SeoFormTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): Node
    {
        $stream = $this->parser->getStream();
        if ($stream->getCurrent()->getType() !== Token::BLOCK_END_TYPE) {
            $error = sprintf("'%s' does not require any arguments.", $this->getTag());
            throw new \Twig_Error_Syntax(
                $error,
                $token->getLine(),
                $this->parser->getStream()->getSourceContext()->getPath()
            );
        }
        $stream->expect(Token::BLOCK_END_TYPE);

        if (FormState::$current === null) {
            throw new SyntaxError(
                sprintf('Cannot render seo outside a form element'),
                $token->getLine(),
                $this->parser->getStream()->getSourceContext()->getPath()
            );
        }

        return new SeoFormNode(FormState::$current, $token->getLine(), $this->getTag());
    }

    public function getTag(): string
    {
        return 'seo';
    }
}
