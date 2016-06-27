<?php

namespace Frontend\Core\Engine;

use Twig_Extension;

/**
 * Keeps the state of a form between opening and closing the tag.
 * Since forms cannot be nested, we can resort to a quick 'n dirty yes/no global state.
 *
 * In the future we could remove it and use stack {push,popp}ing, but I'm hoping
 * we're using symfony forms or some other more OO form library.
 */
class FormState
{
    public static $current = null;
}

class FormExtension
{
    /**
     * @param Object Twig
     */
    protected $twig;

    /**
     * Create a new Form Twig_Extension instance.
     *
     * @param
     */
    public function __construct($twig)
    {
        $this->twig = $twig;

        // option only on forms
        $this->twig->addTokenParser(new FormTokenParser());
        $this->twig->addTokenParser(new FormEndTokenParser());
        $this->twig->addTokenParser(new FormFieldTokenParser());
        $this->twig->addTokenParser(new FormFieldErrorTokenParser());
        $this->twig->addTokenParser(new SeoFormTokenParser());
    }
}
