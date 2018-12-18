<?php

namespace Frontend\Core\Engine;

use Twig_Extension;

class FormExtension extends Twig_Extension
{
    public function getTokenParsers(): array
    {
        return [
            new FormTokenParser(),
            new FormEndTokenParser(),
            new FormFieldTokenParser(),
            new FormFieldErrorTokenParser(),
            new SeoFormTokenParser(),
        ];
    }
}
