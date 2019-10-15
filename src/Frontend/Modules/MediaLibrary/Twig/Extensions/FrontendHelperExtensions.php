<?php

namespace Frontend\Modules\MediaLibrary\Twig\Extensions;

use Frontend\Modules\MediaLibrary\Helper\FrontendHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FrontendHelperExtensions extends AbstractExtension
{
    /**
     * @var FrontendHelper
     */
    private $frontendHelper;

    public function __construct(FrontendHelper $frontendHelper)
    {
        $this->frontendHelper = $frontendHelper;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'media_library_widget',
                [$this, 'parseWidget'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    /**
     * Parses the widget
     *
     * @param string $mediaWidgetAction - The ClassName from the Media widget you want to use.
     * @param string $mediaGroupId - The MediaGroup id you want to parse
     * @param string $title - You can give your optional custom title.
     * @param string $module - You can parse a widget from a custom module. Default is the "MediaLibrary" module.
     *
     * @return string
     */
    public function parseWidget(
        string $mediaWidgetAction,
        string $mediaGroupId,
        string $title = null,
        string $module = null
    ): string {
        return $this->frontendHelper->parseWidget(
            $mediaWidgetAction,
            $mediaGroupId,
            $title,
            $module
        );
    }
}
