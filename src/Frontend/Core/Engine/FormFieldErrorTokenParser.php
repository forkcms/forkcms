<?php

namespace Frontend\Core\Engine;

/**
 * Twig token parser for form field errors.
 */
class FormFieldErrorTokenParser extends \Twig_TokenParser
{
    /**
     * @param \Twig_Token $token consumed token by the lexer.
     *
     * @throws \Twig_Error_Syntax
     *
     * @return \Twig_Node
     */
    public function parse(\Twig_Token $token): \Twig_Node
    {
        $stream = $this->parser->getStream();
        $field = $stream->expect(\Twig_Token::NAME_TYPE)->getValue();
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);
        if (FormState::$current === null) {
            throw new \Twig_Error_Syntax(
                sprintf('Cannot render form field error [%s] outside a form element', $field),
                $token->getLine(),
                $this->parser->getFilename()
            );
        }

        return new FormFieldErrorNode(
            FormState::$current,
            $field,
            $token->getLine(),
            $this->getTag()
        );
    }

    /**
     * @return string
     */
    public function getTag(): string
    {
        return 'form_field_error';
    }
}
