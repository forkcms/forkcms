<?php

namespace Frontend\Core\Engine;

use Twig\Environment;

class FormExtension
{
    /**
     * @param Environment
     */
    protected $twig;

    public function __construct(Environment $twig)
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
