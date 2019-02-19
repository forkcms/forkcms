<?php

namespace Frontend\Modules\MediaLibrary\Twig\Extensions;

use Frontend\Modules\MediaLibrary\Twig\AppRuntime;
use Twig_Extension;
use Twig_SimpleFunction;

class FrontendHelperExtensions extends Twig_Extension
{
    public function getFunctions(): array
    {
        return [
            new Twig_SimpleFunction(
                'media_library_widget',
                [AppRuntime::class, 'parseWidget'],
                ['is_safe' => ['html']]
            ),
        ];
    }
}
