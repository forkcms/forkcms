<?php

namespace Frontend\Core\Engine;

/**
 * Twig token parser for form fields.
 */
class FormFieldTokenParser extends \Twig_TokenParser
{
    /**
     * @param Twig_Token $token consumed token by the lexer.
     *
     * @return Twig_Node
     * @throw Twig_Error_Syntax
     */
    public function parse(\Twig_Token $token)
    {
        $stream = $this->parser->getStream();
        $field = $stream->expect(\Twig_Token::NAME_TYPE)->getValue();
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);
        if (FormState::$current === null) {
            throw new \Twig_Error_Syntax(
                sprintf('Cannot render form field [%s] outside a form element', $field),
                $token->getLine(),
                $this->parser->getFilename()
            );
        }

        return new FormFieldNode(FormState::$current, $field, $token->getLine(), $this->getTag());
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return 'form_field';
    }
}
