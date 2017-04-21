<?php

namespace Frontend\Core\Engine;

/**
 * Twig template tag for the start/opening element of a form tag.
 */
class FormTokenParser extends \Twig_TokenParser
{
    /**
     * @param \Twig_Token $token
     *
     * @throws \Twig_Error_Syntax
     *
     * @return \Twig_Node
     */
    public function parse(\Twig_Token $token): \Twig_Node
    {
        $stream = $this->parser->getStream();
        $form = $stream->expect(\Twig_Token::NAME_TYPE)->getValue();
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        if (FormState::$current !== null) {
            throw new \Twig_Error_Syntax(
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
