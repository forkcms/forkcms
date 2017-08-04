<?php

namespace Frontend\Core\Engine;

use Twig_Environment;

class FormExtension
{
    /**
     * @param Twig_Environment
     */
    protected $twig;

    /**
     * Create a new Form Twig_Extension instance.
     *
     * @param Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
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
