<?php

namespace Frontend\Modules\MediaLibrary\Twig\Extensions;

use Frontend\Modules\MediaLibrary\Twig\AppRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FrontendHelperExtensions extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'media_library_widget',
                [AppRuntime::class, 'parseWidget'],
                ['is_safe' => ['html']]
            ),
        ];
    }
}
