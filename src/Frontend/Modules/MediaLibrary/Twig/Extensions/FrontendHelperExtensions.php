<?php

namespace Frontend\Modules\MediaLibrary\Twig\Extensions;

use Frontend\Modules\MediaLibrary\Helper\FrontendHelper;

class FrontendHelperExtensions extends \Twig_Extension
{
    /**
     * @var FrontendHelper
     */
    private $frontendHelper;

    /**
     * @param FrontendHelper $frontendHelper
     */
    public function __construct(FrontendHelper $frontendHelper)
    {
        $this->frontendHelper = $frontendHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction(
                'media_library_widget',
                array($this, 'parseWidget'),
                array('is_safe' => array('html'))
            ),
        );
    }

    /**
     * Parses the widget
     *
     * @param string $mediaWidgetAction - The ClassName from the Media widget you want to use.
     * @param string $mediaGroupId - The MediaGroup id you want to parse
     * @param string $title - You can give your optional custom title.
     * @param string $module - You can parse a widget from a custom module. Default is the "MediaLibrary" module.
     * @return \Twig_Markup
     */
    public function parseWidget(
        string $mediaWidgetAction,
        string $mediaGroupId,
        string $title = null,
        string $module = null
    ) {
        return $this->frontendHelper->parseWidget(
            $mediaWidgetAction,
            $mediaGroupId,
            $title,
            $module
        );
    }
}
