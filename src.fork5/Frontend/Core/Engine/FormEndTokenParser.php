<?php

namespace Frontend\Core\Engine;

use Twig\Error\SyntaxError;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * Twig token parser for form closing tag.
 */
class FormEndTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): Node
    {
        $stream = $this->parser->getStream();
        if ($stream->getCurrent()->getType() !== Token::BLOCK_END_TYPE) {
            $error = sprintf("'%s' does not require any arguments.", $this->getTag());
            throw new SyntaxError(
                $error,
                $token->getLine(),
                $this->parser->getStream()->getSourceContext()->getPath()
            );
        }
        $stream->expect(Token::BLOCK_END_TYPE);

        if (FormState::$current === null) {
            throw new SyntaxError(
                'Trying to close a form tag, while none opened',
                $token->getLine(),
                $this->parser->getStream()->getSourceContext()->getPath()
            );
        }

        FormState::$current = null;

        return new FormEndNode($token->getLine(), $this->getTag());
    }

    public function getTag(): string
    {
        return 'endform';
    }
}
