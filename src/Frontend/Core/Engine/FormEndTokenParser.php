<?php

namespace Frontend\Core\Engine;

/**
 * Twig token parser for form closing tag.
 */
class FormEndTokenParser extends \Twig_TokenParser
{
    /**
     * @param \Twig_Token $token Token consumed by the lexer.
     *
     * @throws \Twig_Error_Syntax
     *
     * @return \Twig_Node
     */
    public function parse(\Twig_Token $token): \Twig_Node
    {
        $stream = $this->parser->getStream();
        if ($stream->getCurrent()->getType() !== \Twig_Token::BLOCK_END_TYPE) {
            $error = sprintf("'%s' does not require any arguments.", $this->getTag());
            throw new \Twig_Error_Syntax($error, $token->getLine(), $this->parser->getFilename());
        }
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        if (FormState::$current === null) {
            throw new \Twig_Error_Syntax(
                'Trying to close a form tag, while none opened',
                $token->getLine(),
                $this->parser->getFilename()
            );
        }

        FormState::$current = null;

        return new FormEndNode($token->getLine(), $this->getTag());
    }

    /**
     * @return string
     */
    public function getTag(): string
    {
        return 'endform';
    }
}
