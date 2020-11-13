<?php

namespace Frontend\Core\Engine;

use Twig\Error\SyntaxError;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * Twig template tag for the start/opening element of a form tag.
 */
class FormTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): Node
    {
        $stream = $this->parser->getStream();
        $form = $stream->expect(Token::NAME_TYPE)->getValue();
        $stream->expect(Token::BLOCK_END_TYPE);

        if (FormState::$current !== null) {
            throw new SyntaxError(
                sprintf(
                    'form [%s] not closed while opening form [%s]',
                    FormState::$current,
                    $form
                ),
                $token->getLine(),
                $stream->getFilename()
            );
        }

        FormState::$current = $form;

        return new FormNode($form, $token->getLine(), $this->getTag());
    }

    /**
     * @return string
     */
    public function getTag(): string
    {
        return 'form';
    }
}
