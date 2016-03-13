<?php
namespace Frontend\Core\Engine;

/**
 * Twig template tag for the start/opening element of a form tag.
 *
 * @author <per@wijs.be>
 */
class FormTokenParser extends \Twig_TokenParser
{
    /**
     * @param Twig_Token $token
     * @return Twig_Node
     * @throw Twig_Error_Syntax
     */
    public function parse(\Twig_Token $token)
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
        } else {
            FormState::$current = $form;
        }

        return new FormNode($form, $token->getLine(), $this->getTag());
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return 'form';
    }
}
