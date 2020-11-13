<?php

namespace Frontend\Core\Engine;

use Twig\Error\SyntaxError;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * Twig token parser for form field errors.
 */
class FormFieldErrorTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): Node
    {
        $stream = $this->parser->getStream();
        $field = $stream->expect(Token::NAME_TYPE)->getValue();
        $stream->expect(Token::BLOCK_END_TYPE);
        if (FormState::$current === null) {
            throw new SyntaxError(
                sprintf('Cannot render form field error [%s] outside a form element', $field),
                $token->getLine(),
                $this->parser->getStream()->getSourceContext()->getPath()
            );
        }

        return new FormFieldErrorNode(
            FormState::$current,
            $field,
            $token->getLine(),
            $this->getTag()
        );
    }

    public function getTag(): string
    {
        return 'form_field_error';
    }
}
